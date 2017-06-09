<?php declare(strict_types=1);

namespace LotteryEngine\Test;

use LotteryEngine\Lottery;
use PHPUnit\Framework\TestCase;

/**
 * Class LotteryTest
 * @package LotteryEngine\Test
 */
class LotteryTest extends TestCase
{
    /**
     * @covers Lottery::getInstance()
     * @return Lottery
     */
    public function testLottery()
    {
        $lottery = Lottery::getInstance();
        $this->assertNotEmpty($lottery);

        return $lottery;
    }
}
