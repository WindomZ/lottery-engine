<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Play as DbPlay;
use LotteryEngine\Exception\ErrorException;
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

    public function play(string $user_id): bool
    {
        if (!Uuid::isValid($user_id)) {
            throw new ErrorException('"user_id" should be UUID: '.$user_id);
        }

        if (!$this->refresh() || !$this->pass()) {
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
            // TODO: play--
        }
        if ($record->winning) {
            // TODO: reward--
        }

//        $record->post();

        return true;
    }
}
