<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Util\Uuid;

/**
 * Class Record
 * @package LotteryEngine\Database
 */
class Record extends BaseId
{
    const COL_USER_ID = 'user_id';
    const COL_PLAY_ID = 'play_id';
    const COL_REWARD_ID = 'reward_id';
    const COL_WINNING = 'winning';

    /**
     * @var string
     */
    public $user_id = '';

    /**
     * @var string
     */
    public $play_id = '';

    /**
     * @var string
     */
    public $reward_id = '';

    /**
     * @var bool
     */
    public $winning = false;

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return 'record';
    }

    /**
     * @return array
     */
    protected function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                self::COL_USER_ID => $this->user_id,
                self::COL_PLAY_ID => $this->play_id,
                self::COL_REWARD_ID => $this->reward_id,
                self::COL_WINNING => $this->winning,
            ]
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    protected function toInstance(array $data)
    {
        parent::toInstance($data);

        $this->user_id = $data[self::COL_USER_ID];
        $this->play_id = $data[self::COL_PLAY_ID];
        $this->reward_id = $data[self::COL_REWARD_ID];
        $this->winning = $data[self::COL_WINNING];

        return $this;
    }

    /**
     * @throws ErrorException
     */
    protected function beforePost()
    {
        if (!Uuid::isValid($this->user_id)) {
            throw new ErrorException('"user_id" should be valid UUID: '.$this->user_id);
        }
        if (!Uuid::isValid($this->play_id)) {
            throw new ErrorException('"play_id" should be valid UUID: '.$this->play_id);
        }
        if (!Uuid::isValid($this->reward_id)) {
            throw new ErrorException('"reward_id" should be valid UUID: '.$this->reward_id);
        }
        parent::beforePost();
    }

    /**
     * @throws ErrorException
     */
    protected function beforePut()
    {
        $this->beforePost();
        parent::beforePut();
    }
}
