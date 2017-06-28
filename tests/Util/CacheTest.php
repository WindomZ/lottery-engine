<?php declare(strict_types=1);

namespace LotteryEngine\Test\Util;

use LotteryEngine\Util\Cache;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function test_save_cache()
    {
        self::assertTrue(Cache::save_cache(123, Cache::NAME_TEST));
        self::assertEquals(Cache::get_cache(Cache::NAME_TEST), 123);

        self::assertTrue(Cache::save_cache("hello world", Cache::NAME_TEST));
        self::assertEquals(Cache::get_cache(Cache::NAME_TEST), "hello world");
    }
}
