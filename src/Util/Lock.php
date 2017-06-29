<?php declare(strict_types=1);

namespace LotteryEngine\Util;

use malkusch\lock\mutex\CASMutex;
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
    private static $flockMutex = null;

    /**
     * @return FlockMutex|null
     */
    private static function flockMutex()
    {
        if (!self::$flockMutex) {
            self::$flockMutex = new FlockMutex(fopen(dirname(__FILE__).'/Lock', "r"));
        }

        return self::$flockMutex;
    }

    /**
     * @param callable $check
     * @param callable|null $code
     */
    public static function flockSynchronized(callable $check, callable $code = null)
    {
        if (isset($code)) {
            self::flockMutex()->check($check)->then($code);
        } else {
            $code = $check;
            self::flockMutex()->synchronized($code);
        }
    }

    /**
     * @var CASMutex|null
     */
    private static $casMutex = null;

    /**
     * @return CASMutex|null
     */
    private static function casMutex()
    {
        if (!self::$casMutex) {
            self::$casMutex = new CASMutex();
        }

        return self::$casMutex;
    }

    /**
     * Notifies the Mutex about a successful CAS operation.
     */
    public static function casNotify()
    {
        self::casMutex()->notify();
    }

    /**
     * @param callable $check
     * @param callable|null $code
     */
    public static function casSynchronized(callable $check, callable $code = null)
    {
        if (isset($code)) {
            self::casMutex()->check($check)->then($code);
        } else {
            $code = $check;
            self::casMutex()->synchronized($code);
        }
    }
}
