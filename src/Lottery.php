<?php declare(strict_types=1);

namespace LotteryEngine;

/**
 * Class Lottery
 * @package LotteryEngine
 */
class Lottery
{
    /**
     * @var Lottery
     */
    private static $_instance;

    private function __clone()
    {
    }

    /**
     * @return Lottery
     */
    public static function getInstance(): Lottery
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new Lottery();
        }

        return self::$_instance;
    }

    /**
     * Lottery constructor.
     */
    private function __construct()
    {
        self::$_instance = $this;
    }
}
