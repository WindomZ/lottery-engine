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
        Lottery::setConfigPath('./tests/Config/config.yml');
        $lottery = Lottery::getInstance();

        self::assertNotEmpty($lottery);

        return $lottery;
    }

    /**
     * @depends testLottery
     * @param Lottery $lottery
     * @return Lottery
     */
    public function testLotteryConfig(Lottery $lottery)
    {
        self::assertNotEmpty($lottery);

        self::assertEquals($lottery->getConfig()->get('database_host'), '127.0.0.1');
        self::assertEquals($lottery->getConfig()->get('database_port'), 3306);
        self::assertEquals($lottery->getConfig()->get('database_type'), 'mysql');
        self::assertEquals($lottery->getConfig()->get('database_name'), 'lotterydb');
        self::assertEquals($lottery->getConfig()->get('database_username'), 'root');
        self::assertEquals($lottery->getConfig()->get('database_password'), 'root');
        self::assertEquals($lottery->getConfig()->get('database_logging'), true);
        self::assertEquals($lottery->getConfig()->get('database_prefix'), 'le_');

        return $lottery;
    }
}
