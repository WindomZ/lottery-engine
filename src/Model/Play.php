<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Play as DbPlay;
use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Util\Lock;
use LotteryEngine\Util\Uuid;

/**
 * Class Play
 * @package LotteryEngine\Model
 */
class Play extends DbPlay
{
    /**
     * Play constructor.
     */
    public function __construct()
    {
        parent::__construct();
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
        if ($this->rule) {
            if (!Uuid::isValid($this->id)) {
                $this->id = Uuid::uuid();
            }
            foreach ($this->weights as $r => $w) {
                if (!Rule::create($this->id, $r, $w)->post()) {
                    throw new ErrorException('Fail to add play rule!');
                }
            }
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
        if ($this->rule) {
            if (gettype($columns) === 'string' || array_key_exists($this::COL_WEIGHTS, $columns)) {
                $list = Rule::rules($this->id);
                if (!empty($list)) {
                    foreach ($this->weights as $r => $w) {
                        $create = true;
                        foreach ($list as $l) {
                            if ($l instanceof Rule && $l->reward_id === $r) {
                                if ($l->weight !== $w) {
                                    $l->weight = $w;
                                    if (!$l->put([Rule::COL_WEIGHT])) {
                                        throw new ErrorException('Fail to put play rule!');
                                    }
                                }
                                $create = false;
                                break;
                            }
                        }
                        if ($create) {
                            $obj = Rule::create($this->id, $r, $w);
                            if (!$obj->post()) {
                                throw new ErrorException('Fail to post new play rule!');
                            }
                        }
                    }
                }
            }
        }

        return parent::put($columns, $where);
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
        if ($this->rule && empty($this->weights)) {
            $this->weights = Rule::weights($this->id);
        }
        foreach ($this->weights as $weight) {
            if (gettype($weight) === 'integer') {
                $sum += $weight;
            }
        }

        if ($sum > 0) {
            $index = mt_rand(0, $sum);
            foreach ($this->weights as $reward_id => $weight) {
                if (gettype($weight) === 'integer') {
                    $index -= $weight;
                }
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
            }

            return $this->limit - $count;
        }

        return $this->limit;
    }

    /**
     * @param string $user_id
     * @return string
     * @throws ErrorException
     */
    public function play(string $user_id): string
    {
        if (!Uuid::isValid($user_id)) {
            throw new ErrorException('"user_id" should be UUID: '.$user_id);
        }

        if (!$this->pass()) {
            throw new ErrorException('Activity end!');
        }

        $id = $this->randomRewardId();
        if (empty($id)) {
            throw new ErrorException('No reward!');
        }

        if ($id === Reward::ID_AGAIN) {
            return Record::ID_AGAIN;
        }

        $activity = $this;
        $record = Record::create($user_id, $this->id, $id);

        Lock::synchronized(
            function () use ($activity, $record) {
                return $activity->refresh() && $activity->pass();
            },
            function () use ($activity, $record) {
                $reward = Reward::object($record->reward_id);

                $record->winning = $activity->playCount($record->user_id) > 0;
                if ($record->winning) {
                    $record->winning = $reward->refresh() && $reward->pass();
                }
                if ($record->winning) {
                    $record->winning = $activity->increase($activity::COL_COUNT);
                }
                if ($record->winning) {
                    $record->winning = $reward->increase($reward::COL_COUNT);
                }

                $record->post();
            }
        );

        return $record->id;
    }
}
