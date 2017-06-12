<?php declare(strict_types=1);

namespace LotteryEngine\Test\Model;

use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Model\Play;
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
     * @return Play
     */
    public function testNewPlay()
    {
        $play = new Play();
        $this->assertNotEmpty($play);

        return $play;
    }

    /**
     * @return Reward
     */
    public function testNewReward()
    {
        $reward = new Reward();
        $this->assertNotEmpty($reward);

        return $reward;
    }

    /**
     * @covers  Reward::post()
     * @depends testNewReward
     * @param Reward $reward
     * @return Reward
     */
    public function testPostReward(Reward $reward)
    {
        $this->assertNotEmpty($reward);

        if (!$reward->get([$reward::COL_NAME => 'name'])) {
            $reward->award_id = Uuid::uuid();
            $reward->level = 1;

            try {
                $this->assertFalse($reward->post());
            } catch (ErrorException $err) {
                $this->assertNotEmpty($err);
            }

            $reward->name = 'name';
            $reward->desc = 'desc';

            $this->assertTrue($reward->post());
        }

        return $reward;
    }

    /**
     * @covers  Reward::put()
     * @depends testPostReward
     * @param Reward $play
     * @return Reward
     */
    public function testPutReward(Reward $play)
    {
        $this->assertTrue($play->put('*'));

        return $play;
    }

    /**
     * @covers  Play::post()
     * @depends testNewPlay
     * @depends testPutReward
     * @param Play $play
     * @param Reward $reward
     * @return Play
     */
    public function testPostPlay(Play $play, Reward $reward)
    {
        $this->assertNotEmpty($play);
        $this->assertNotEmpty($reward);

        if (!$play->get([$play::COL_NAME => 'name'])) {
            $play->name = 'name';
            $play->desc = 'desc';
            $play->rule = 'rule';
            $play->daily = true;
            $play->limit = 3;
            $play->size = 10000;

            try {
                $this->assertFalse($play->post());
            } catch (ErrorException $err) {
                $this->assertNotEmpty($err);
            }

            $play->addReward($reward->id, 10);

            $this->assertTrue($play->post());
        }

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
        $this->assertTrue($play->put('*'));

        return $play;
    }

    /**
     * @depends testPutPlay
     * @param Play $play
     */
    public function testPlay(Play $play)
    {
        $this->assertNotEmpty($play);
    }
}
