<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Play as DbPlay;
use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Util\Date;
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
     * @return array|null
     */
    public static function list(array $where = null, int $limit = 0, int $page = 0)
    {
        return (new Play())->select($where, $limit, $page);
    }

    /**
     * @param string $reward_id
     * @param int $weight
     * @throws ErrorException
     */
    public function addReward(string $reward_id, int $weight = 0)
    {
        if (empty($reward_id)) {
            throw new ErrorException('"reward_id" should be valid UUID: '.$reward_id);
        }
        if ($weight <= 0) {
            throw new ErrorException('"weight" should be positive: '.$weight);
        }
        $this->weights[$reward_id] = $weight;
    }

    /**
     * @return string
     */
    private function randRewardId(): string
    {
        $id = '';

        $sum = 0;
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

        return -1;
    }

    public function play(string $user_id): bool
    {
        if (!Uuid::isValid($user_id)) {
            throw new ErrorException('"user_id" should be UUID: '.$user_id);
        }

        // TODO: LOCK...

        if (!$this->refresh() || !$this->pass()) {
            return false;
        }

        if ($this->playCount($user_id) === 0) {
            return false;
        }

        $id = $this->randRewardId();
        if (empty($id)) {
            return false;
        }
        $reward = Reward::object($id);

        $record = Record::create($user_id, $this->id, $reward->id);
        $record->winning = $reward->pass();

        if ($record->winning) {
            $record->winning = $this->increase($this::COL_COUNT);
        }
        if ($record->winning) {
            $record->winning = $reward->increase($reward::COL_COUNT);
        }

        $record->post();

        // TODO: UNLOCK...

        return true;
    }
}
