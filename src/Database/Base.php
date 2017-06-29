<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Lottery;

/**
 * Class Base
 * @package LotteryEngine\Database
 */
abstract class Base
{
    /**
     * Base constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return Database
     */
    protected static function DB()
    {
        return Lottery::getInstance()->getDatabase();
    }
}
