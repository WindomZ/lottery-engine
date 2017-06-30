<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Config\Config;
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
     * @return Config
     */
    protected static function Config()
    {
        return Lottery::getInstance()->getConfig();
    }

    /**
     * @return Database
     */
    protected static function DB()
    {
        return Lottery::getInstance()->getDatabase();
    }
}
