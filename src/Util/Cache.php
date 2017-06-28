<?php declare(strict_types=1);

namespace LotteryEngine\Util;

use DateTime;

/**
 * Class Cache
 * @package LotteryEngine\Util
 */
class Cache
{
    const NAME_TEST = 'test';
    const NAME_PLAY = 'play';
    const NAME_RECORD = 'record';
    const NAME_REWARD = 'reward';
    const NAME_RULE = 'rule';

    /**
     * @param string $name
     * @return mixed
     */
    private static function get_cache_id(string $name)
    {
        // maintain list of caches here
        $id = array(
            self::NAME_TEST => 1,
            self::NAME_PLAY => 10,
            self::NAME_RECORD => 11,
            self::NAME_REWARD => 12,
            self::NAME_RULE => 13,
        );

        return $id[$name];
    }

    /**
     * @param $data
     * @param string $name
     * @param int $timeout
     * @return bool
     */
    public static function save_cache($data, string $name, int $timeout = 0)
    {
        // delete cache
        try {
            $id = shmop_open(self::get_cache_id($name), "a", 0, 0);
            shmop_delete($id);
            shmop_close($id);
        } catch (\Exception $err) {
        }

        $data = serialize($data);

        // get id for name of cache
        $id = shmop_open(self::get_cache_id($name), "c", 0644, strlen($data));

        // return int for data size or boolean false for fail
        if ($id) {
            self::set_timeout($name, $timeout);

            return true && shmop_write($id, $data, 0);
        } else {
            return false;
        }
    }

    /**
     * @param string $name
     * @return bool|mixed
     */
    public static function get_cache(string $name)
    {
        if (!self::check_timeout($name)) {
            $id = shmop_open(self::get_cache_id($name), "a", 0, 0);

            if ($id) {
                $data = unserialize(shmop_read($id, 0, shmop_size($id)));
            } else {
                return false; // failed to load data
            }

            // array retrieved
            if ($data) {
                shmop_close($id);

                return $data;
            } else {
                return false; // failed to load data
            }
        } else {
            return false; // data was expired
        }
    }

    /**
     * @param string $name
     * @param int $int
     */
    private static function set_timeout(string $name, int $int)
    {
        $timeout = 0;
        if ($int > 0) {
            $timeout = new DateTime(date('Y-m-d H:i:s'));
            date_add($timeout, date_interval_create_from_date_string("$int seconds"));
            $timeout = date_format($timeout, 'YmdHis');
        }

        $tl = array();

        try {
            $id = shmop_open(100, "a", 0, 0);
            if ($id) {
                $tl = unserialize(shmop_read($id, 0, shmop_size($id)));
            }
            shmop_delete($id);
            shmop_close($id);
        } catch (\Exception $err) {
        }

        $tl[$name] = $timeout;
        $id = shmop_open(100, "c", 0644, strlen(serialize($tl)));
        shmop_write($id, serialize($tl), 0);
    }

    /**
     * @param string $name
     * @return bool
     */
    private static function check_timeout(string $name)
    {
        $now = new DateTime(date('Y-m-d H:i:s'));
        $now = intval(date_format($now, 'YmdHis'));

        $id = shmop_open(100, "a", 0, 0);
        if ($id) {
            $tl = unserialize(shmop_read($id, 0, shmop_size($id)));
        } else {
            return true;
        }
        shmop_close($id);

        if ($tl && isset($tl[$name])) {
            $timeout = intval($tl[$name]);
            if ($timeout > 0) {
                return $now > $timeout;
            }
        }

        return false;
    }
}
