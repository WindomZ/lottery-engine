<?php declare(strict_types=1);

namespace LotteryEngine\Test\Util;

use LotteryEngine\Util\Date;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTest
 * @package LotteryEngine\Test\Date
 */
class DateTest extends TestCase
{
    /**
     *
     */
    public function test_get_time_stamp()
    {
        self::assertNotEmpty(Date::get_now_time_stamp());
        self::assertEquals(Date::get_now_time_stamp(), time());
    }

    /**
     *
     */
    public function test_get_time()
    {
        self::assertEquals(gettype(Date::get_now_time()), 'string');

        self::assertEquals(
            substr(Date::get_next_time(), 0, 10),
            substr(Date::get_next_zero_time(), 0, 10)
        );
        self::assertEquals(
            substr(Date::get_next_time(86400), 0, 10),
            substr(Date::get_next_zero_time(86400), 0, 10)
        );

        self::assertEquals(substr(Date::get_next_zero_time(), 11, 8), '00:00:00');
    }

    /**
     *
     */
    public function test_before()
    {
        self::assertTrue(Date::before(Date::get_next_time(-1)));
        self::assertTrue(Date::before(Date::get_next_time(-1), Date::get_next_time()));
    }

    /**
     *
     */
    public function test_after()
    {
        self::assertTrue(Date::after(Date::get_next_time(1)));
        self::assertTrue(Date::after(Date::get_next_time(1), Date::get_next_time()));
    }
}
