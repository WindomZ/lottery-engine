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
        $this->assertTrue(Uuid::isValid(Uuid::uuid()));

        $this->assertTrue(Uuid::isValid('00000000-0000-0000-0000-000000000000'));
        $this->assertTrue(Uuid::isValid('00000000-0000-0000-0000-000000000001'));
        $this->assertTrue(Uuid::isValid('00000000-0000-0000-0000-000000000002'));
    }
}
