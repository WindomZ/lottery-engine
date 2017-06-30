<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use SHMCache\Block;

class Cache extends Block
{
    /**
     * Get the $value by $key from cache
     * @param string $key
     * @return bool|mixed
     */
    public function get(string $key)
    {
        // Here you can hook the code you need.
        return parent::get($key); // If necessary, you can replace this part of the code
    }

    /**
     * Save $value by $key to cache
     * @param string $key
     * @param mixed $value
     * @param int $seconds
     * @return bool
     * @throws \ErrorException
     */
    public function save(string $key, $value, int $seconds = 0): bool
    {
        // Here you can hook the code you need.
        return parent::save($key, $value, $seconds); // If necessary, you can replace this part of the code
    }
}
