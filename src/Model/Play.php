<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Play as DbPlay;
use LotteryEngine\Exception\ErrorException;

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
}
