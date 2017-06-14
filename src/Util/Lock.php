<?php declare(strict_types=1);

namespace LotteryEngine\Util;

use malkusch\lock\mutex\FlockMutex;

/**
 * Class Lock
 * @package LotteryEngine\Util
 */
class Lock
{
    /**
     * @var FlockMutex|null
     */
    private static $mutex = null;

    /**
     * @return FlockMutex|null
     */
    private static function mutex()
    {
        if (!self::$mutex) {
            self::$mutex = new FlockMutex(fopen(__FILE__, "r"));
        }

        return self::$mutex;
    }

    /**
     * @param callable $check
     * @param callable $code
     */
    public static function check(callable $check, callable $code)
    {
        self::mutex()->check($check)->then($code);
    }

    /**
     * @param callable $code
     */
    public static function synchronized(callable $code)
    {
        self::mutex()->synchronized($code);
    }
}
