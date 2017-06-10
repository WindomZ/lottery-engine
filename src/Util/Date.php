<?php declare(strict_types=1);

namespace LotteryEngine\Util;

use LotteryEngine\Exception\ErrorException;
use DateTime;

/**
 * Class Date
 * @package LotteryEngine\Util
 */
class Date
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @return int
     */
    public static function get_now_time_stamp()
    {
        return Date::get_next_time_stamp();
    }

    /**
     * @return bool|string
     */
    public static function get_now_time()
    {
        return Date::get_next_time();
    }

    /**
     * @param int $second 秒
     * @return bool|string
     */
    public static function get_next_time($second = 0)
    {
        $time = Date::get_now_time_stamp() + $second;

        return date(self::DATE_FORMAT, $time);
    }

    /**
     * @param int $second
     * @return int
     */
    public static function get_next_time_stamp($second = 0)
    {
        $time = time() + $second;

        return $time;
    }

    /**
     * @param string $time
     * @return bool
     * @throws ErrorException
     */
    public static function before(string $time)
    {
        $date = DateTime::createFromFormat(self::DATE_FORMAT, $time);
        if (!$date) {
            throw new ErrorException('Invalid time format!');
        }

        return time() > $date->getTimestamp();
    }

    /**
     * @param string $time
     * @return bool
     * @throws ErrorException
     */
    public static function after(string $time)
    {
        $date = DateTime::createFromFormat(self::DATE_FORMAT, $time);
        if (!$date) {
            throw new ErrorException('Invalid time format!');
        }

        return time() < $date->getTimestamp();
    }
}
