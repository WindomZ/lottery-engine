<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Reward as DbReward;

/**
 * Class Reward
 * @package LotteryEngine\Model
 */
class Reward extends DbReward
{
    /**
     * Reward constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}
