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
    const COL_RELATED_ID = 'related_id';
    const COL_WINNING = 'winning';
    const COL_PASSING = 'passing';

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
     * @var string
     */
    public $related_id = '';

    /**
     * @var bool
     */
    public $winning = false;

    /**
     * @var bool
     */
    public $passing = true;

    /**
     * @return Record
     */
    protected function newObject()
    {
        return new Record();
    }

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
                self::COL_RELATED_ID => $this->related_id,
                self::COL_WINNING => $this->winning,
                self::COL_PASSING => $this->passing,
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
        $this->related_id = $data[self::COL_RELATED_ID];
        $this->winning = boolval($data[self::COL_WINNING]);
        $this->passing = boolval($data[self::COL_PASSING]);

        return $this;
    }

    /**
     * @param $data
     * @return object
     */
    protected function addInstance($data)
    {
        $obj = $this->newObject()->toInstance($data);
        $this->addList($obj);

        return $obj;
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

    /**
     * @param string $related_id
     * @return bool
     * @throws ErrorException
     */
    public function putRelated(string $related_id): bool
    {
        if (!Uuid::isValid($related_id)) {
            throw new ErrorException('"related_id" should be UUID: '.$related_id);
        }
        $this->related_id = $related_id;

        return $this->put([self::COL_RELATED_ID]);
    }
}
