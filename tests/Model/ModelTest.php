<?php declare(strict_types=1);

namespace LotteryEngine\Test\Model;

use LotteryEngine\Model\Play;
use LotteryEngine\Model\Reward;
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
     * @covers  Play::post()
     * @depends testNewPlay
     * @depends testNewReward
     * @param Play $play
     * @param Reward $reward
     * @return Play
     */
    public function testPostPlay(Play $play, Reward $reward)
    {
        if (!$play->get([$play::COL_NAME => 'name'])) {
            $play->name = 'name';
            $play->rule = 'rule';
            $play->daily = true;
            $play->limit = 3;
            $play->size = 10000;

            $this->assertFalse($play->post());

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
     * @return Reward
     */
    public function testNewReward()
    {
        $reward = new Reward();
        $this->assertNotEmpty($reward);

        return $reward;
    }

    /**
     * @depends testNewPlay
     * @depends testNewReward
     * @param Play $play
     * @param Reward $reward
     */
    public function testPlay(Play $play, Reward $reward)
    {
    }
}
