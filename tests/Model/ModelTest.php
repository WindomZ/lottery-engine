<?php declare(strict_types=1);

namespace LotteryEngine\Test\Model;

use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Model\Play;
use LotteryEngine\Model\Record;
use LotteryEngine\Model\Reward;
use LotteryEngine\Util\Uuid;
use PHPUnit\Framework\TestCase;

/**
 * Class ModelTest
 * @package LotteryEngine\Test\Model
 */
class ModelTest extends TestCase
{
    /**
     * @covers  Reward::post()
     * @return Reward
     */
    public function testPostReward()
    {
        $reward = null;

        $list = Reward::list([Reward::COL_NAME => 'name'], 10, 0);
        if (!$list || !$list[Reward::ARG_SIZE]) {
            $reward = new Reward();

            $reward->setAward(Uuid::uuid(), 1, 2);
            $reward->level = 1;

            try {
                self::assertFalse($reward->post());
            } catch (ErrorException $err) {
                self::assertNotEmpty($err);
            }

            $reward->name = 'name';
            $reward->desc = 'desc';
            $reward->size = 6;

            self::assertTrue($reward->post());
        } else {
            $reward = $list[Reward::ARG_DATA][0];
        }

        self::assertNotEmpty($reward);

        return $reward;
    }

    /**
     * @covers  Reward::put()
     * @depends testPostReward
     * @param Reward $reward
     * @return Reward
     */
    public function testPutReward(Reward $reward)
    {
        self::assertTrue($reward->put('*'));

        return $reward;
    }

    /**
     * @covers  Play::post()
     * @depends testPutReward
     * @param Reward $reward
     * @return Play
     */
    public function testPostPlay(Reward $reward)
    {
        self::assertNotEmpty($reward);

        $play = null;

        $list = Play::list([Play::COL_NAME => 'name'], 10, 0);
        if (!$list || !$list[Play::ARG_SIZE]) {
            $play = new Play();

            $play->name = 'name';
            $play->desc = 'desc';
            $play->daily = true;
            $play->limit = 3;
            $play->size = 6;

            $play->setReward($reward->id, 10);
            $play->setReward(Reward::ID_NULL, 20);
            $play->setReward(Reward::ID_AGAIN, 30);

            self::assertTrue($play->post());
        } else {
            $play = $list[Play::ARG_DATA][0];
        }

        self::assertNotEmpty($play);

        return $play;
    }

    /**
     * @covers  Play::put()
     * @depends testPostPlay
     * @param Play $play
     * @return Play
     */
    public function testPutPlay(Play $play)
    {
        self::assertTrue($play->put('*'));

        return $play;
    }

    /**
     * @depends testPutPlay
     * @param Play $play
     */
    public function testPlay(Play $play)
    {
        self::assertNotEmpty($play);

        $user_id = Uuid::uuid();

        $recordId1 = $play->play($user_id);
        self::assertNotEmpty($recordId1);
        $recordId2 = $play->play($user_id);
        self::assertNotEmpty($recordId2);
        $recordId3 = $play->play($user_id);
        self::assertNotEmpty($recordId3);

        self::assertTrue(Record::object($recordId1)->winning);
        self::assertTrue(Record::object($recordId2)->winning);
        self::assertTrue(Record::object($recordId3)->winning);
    }
}
