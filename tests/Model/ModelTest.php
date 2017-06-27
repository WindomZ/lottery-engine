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

        $list = Reward::list([Reward::COL_NAME => '这是名称name'], 10, 0);
        if (!$list || !$list[Reward::ARG_SIZE]) {
            $reward = new Reward();

            $reward->setAward(Uuid::uuid(), 1, 2);
            $reward->level = 1;

            try {
                self::assertFalse($reward->post());
            } catch (ErrorException $err) {
                self::assertNotEmpty($err);
            }

            $reward->name = '这是名称name';
            $reward->desc = '这是描述desc';
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

        $list = Play::list([Play::COL_NAME => '这是名称name'], 10, 0);
        if (!$list || !$list[Play::ARG_SIZE]) {
            $play = new Play();

            $play->name = '这是名称name';
            $play->desc = '这是描述desc';
            $play->daily = true;
            $play->limit = 3;
            $play->size = 6;

            $play->setReward($reward->id, 10);
            $play->setReward(Reward::ID_NULL, 20);

            self::assertTrue($play->post());

            $play->setReward($reward->id, 10);
            $play->setReward(Reward::ID_NULL, 11);
            $play->setReward(Reward::ID_AGAIN, 12);

            self::assertTrue($play->put([Play::COL_WEIGHTS]));
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

        $SIZE = 20;
        $user_id = Uuid::uuid();
        $recordIds = array();
        $count = 0;
        $count_win = 0;

        for ($i = 0; $i < $SIZE; $i++) {
            $recordId = $play->play(
                $user_id,
                function ($err, Record $record) use (&$count, &$count_win) {
                    if (isset($err)) {
                        self::assertEmpty($err, (string)$err);
                    }
                    self::assertNotEmpty($record);
                    $count++;
                    if ($record->isWinning()) {
                        $count_win++;
                    }
                }
            );
            self::assertNotEmpty($recordId);
            array_push($recordIds, $recordId);
        }

        sleep(1);
        self::assertEquals($count, $SIZE);

        for ($i = 0; $i < $SIZE; $i++) {
            $record = Record::object($recordIds[$i]);
            self::assertNotEmpty($record);
            if ($record->isWinning()) {
                self::assertTrue($record->putRelated($user_id));
                $count_win--;
            }
        }

        self::assertEmpty($count_win);
    }
}
