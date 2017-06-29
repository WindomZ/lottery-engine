<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Play as DbPlay;
use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Util\Lock;
use LotteryEngine\Util\Uuid;
use SHMCache\Block;

/**
 * Class Play
 * @package LotteryEngine\Model
 */
class Play extends DbPlay
{
    /**
     * @var Block
     */
    private static $cache;

    /**
     * Play constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if (!self::$cache) {
            self::$cache = new Block(60);
        }
    }

    /**
     * @return Play
     */
    protected function newObject()
    {
        return new Play();
    }

    /**
     * @param string|null $id
     * @return Play|null
     */
    public static function object(string $id = null)
    {
        $obj = new Play();
        if ($id && !$obj->getById($id)) {
            return null;
        }

        return $obj;
    }

    /**
     * @param array|null $where
     * @param int $limit
     * @param int $page
     * @param array|null $order
     * @return array|null
     */
    public static function list(array $where = null, int $limit = 0, int $page = 0, array $order = null)
    {
        return (new Play())->select($where, $limit, $page, $order);
    }

    /**
     * @return bool
     * @throws ErrorException
     */
    public function post(): bool
    {
        if ($this->hasRule()) {
            if (!Uuid::isValid($this->id)) {
                $this->id = Uuid::uuid();
            }
            Rule::postByPlay($this);
        }

        return parent::post();
    }

    /**
     * @param array|string $columns
     * @param array $where
     * @return bool
     * @throws ErrorException
     */
    public function put($columns, array $where = []): bool
    {
        if ($this->hasRule()) {
            if (is_string($columns)) {
                Rule::putByPlay($this);
            } elseif (is_array($columns) && in_array(self::COL_WEIGHTS, $columns)) {
                Rule::putByPlay($this);
                $columns = array_diff($columns, [self::COL_WEIGHTS]);
            }
        }

        return parent::put($columns, $where);
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function passSync(string $user_id)
    {
        if (!$this->pass()) {
            return false;
        } elseif (!$this->hasCount($user_id)) {
            return false;
        }

        $data = self::$cache->get($this->id);
        if (!is_array($data)) {
            if (!$this->refresh()) {
                return false;
            }
            $data = array(
                'active' => $this->active,
                'count' => $this->count,
                'size' => $this->size,
            );
            self::$cache->save($this->id, $data);

            return true;
        }

        return boolval($data['active']) && intval($data['count']) < intval($data['size']);
    }

    /**
     * @param string $column
     * @param int $count
     * @param array $where
     * @param array $data
     * @return bool
     * @throws ErrorException
     */
    protected function increase(string $column, int $count = 1, array $where = [], array $data = []): bool
    {
        if ($column === self::COL_COUNT) {
            $arr = self::$cache->get($this->id);
            if (is_array($arr)) {
                $arr['count'] = intval($arr['count']) + 1;
                self::$cache->save($this->id, $arr);
            }
        }

        return parent::increase($column, $count, $where, $data);
    }

    /**
     * @param string $reward_id
     * @param int $weight
     * @return Play
     * @throws ErrorException
     */
    public function setReward(string $reward_id, int $weight = 0)
    {
        if (empty($reward_id)) {
            throw new ErrorException('"reward_id" should be valid UUID: '.$reward_id);
        }
        if ($weight < 0) {
            $weight = 0;
        }
        $this->weights[$reward_id] = $weight;

        return $this;
    }

    /**
     * @return string
     */
    private function randomRewardId(): string
    {
        $id = '';

        $sum = 0;
        if ($this->hasRule() && empty($this->weights)) {
            $this->weights = Rule::weights($this->id);
        }
        foreach ($this->weights as $weight) {
            is_integer($weight) && $sum += $weight;
        }

        if ($sum > 0) {
            $index = mt_rand(0, $sum);
            foreach ($this->weights as $reward_id => $weight) {
                is_integer($weight) && $index -= $weight;
                if ($index <= 0) {
                    $id = $reward_id;
                    break;
                }
            }
        }

        return $id;
    }

    /**
     * @param string $user_id
     * @return int
     */
    public function playCount(string $user_id): int
    {
        if ($this->limit > 0) {
            $count = $this->daily ? Record::totalQuery(
                sprintf(
                    "%s = :user_id AND %s = :play_id AND TO_DAYS(%s) = TO_DAYS(NOW())",
                    Record::COL_USER_ID,
                    Record::COL_PLAY_ID,
                    Record::COL_POST_TIME
                ),
                [':user_id' => $user_id, ':play_id' => $this->id]
            ) : Record::total(
                [
                    Record::COL_USER_ID => $user_id,
                    Record::COL_PLAY_ID => $this->id,
                ]
            );
            if ($count >= $this->limit) {
                return 0;
            } elseif ($count < 0) {
                return $this->limit;
            }

            return $this->limit - $count;
        }

        return $this->limit;
    }

    /**
     * @param string $user_id
     * @return int
     */
    protected function hasCount(string $user_id): int
    {
        $key = $this->id.'/'.$user_id;
        $count = self::$cache->get($key);
        if (!is_integer($count)) {
            $count = $this->playCount($user_id);
            self::$cache->save($key, $count);
        }

        return $count;
    }

    /**
     * @param string $user_id
     */
    protected function payCount(string $user_id)
    {
        $key = $this->id.'/'.$user_id;
        $count = self::$cache->get($key);
        if (!is_integer($count)) {
            $count = $this->playCount($user_id);
        }
        self::$cache->save($key, $count - 1);
    }

    /**
     * @param string $user_id
     * @param callable|null $callback
     * @return string
     * @throws ErrorException
     */
    public function play(string $user_id, callable $callback = null): string
    {
        if (!Uuid::isValid($user_id)) {
            throw new ErrorException('"user_id" should be UUID: '.$user_id);
        }

        if (!$this->passSync($user_id)) {
            return Record::ID_FINISH;
        }

        $id = $this->randomRewardId();
        if (empty($id)) {
            throw new ErrorException('No reward!');
        }

        $record = Record::create($user_id, $this->id, $id);

        if ($id === Reward::ID_AGAIN) {
            if (is_callable($callback)) {
                $callback(null, $record);
            }

            return Record::ID_AGAIN;
        }

        $play = $this;

        Lock::casSynchronized(
            function () use ($play, $record, $callback) {
                $err = null;
                try {
                    if ($play->passSync($record->user_id)) {
                        $play->payCount($record->user_id);
                        $record->winning = Reward::pay($record->reward_id)
                            && $play->increase(self::COL_COUNT)
                            && ($record->reward_id !== Reward::ID_NULL);
                    } else {
                        $record->winning = false;
                        $record->passing = false;
                    }
                    $record->post();
                } catch (\Exception $e) {
                    $err = $e;
                } finally {
                    Lock::casNotify();
                    if (is_callable($callback)) {
                        @$callback($err, $record);
                    }
                }
            }
        );

        if ($id === Reward::ID_NULL) {
            return Record::ID_NULL;
        }

        return $record->id;
    }
}
