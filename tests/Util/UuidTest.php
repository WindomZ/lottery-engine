<?php declare(strict_types=1);

namespace LotteryEngine\Test\Util;

use LotteryEngine\Util\Uuid;
use PHPUnit\Framework\TestCase;

/**
 * Class UuidTest
 * @package LotteryEngine\Test\Util
 */
class UuidTest extends TestCase
{
    /**
     *
     */
    public function testUuid()
    {
        self::assertTrue(Uuid::isValid(Uuid::uuid()));

        self::assertTrue(Uuid::isValid('00000000-0000-0000-0000-000000000000'));
        self::assertTrue(Uuid::isValid('00000000-0000-0000-0000-000000000001'));
        self::assertTrue(Uuid::isValid('00000000-0000-0000-0000-000000000002'));
    }
}
