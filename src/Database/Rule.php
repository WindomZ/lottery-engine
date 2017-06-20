<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Util\Uuid;

/**
 * Class Rule
 * @package LotteryEngine\Database
 */
class Rule extends BaseId
{
    const COL_PLAY_ID = 'play_id';
    const COL_REWARD_ID = 'reward_id';
    const COL_WEIGHT = 'weight';

    /**
     * @var string
     */
    public $play_id = '';

    /**
     * @var string
     */
    public $reward_id = '';

    /**
     * @var int
     */
    public $weight = 0;

    /**
     * @return Rule
     */
    protected function newObject()
    {
        return new Rule();
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return 'rule';
    }


    /**
     * @return array
     */
    protected function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                self::COL_PLAY_ID => $this->play_id,
                self::COL_REWARD_ID => $this->reward_id,
                self::COL_WEIGHT => $this->weight,
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

        $this->play_id = $data[self::COL_PLAY_ID];
        $this->reward_id = $data[self::COL_REWARD_ID];
        $this->weight = intval($data[self::COL_WEIGHT]);

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
        if (!Uuid::isValid($this->play_id)) {
            throw new ErrorException('"play_id" should be valid UUID: '.$this->play_id);
        }
        if (!Uuid::isValid($this->reward_id)) {
            throw new ErrorException('"reward_id" should be valid UUID: '.$this->reward_id);
        }
        if ($this->weight < 0) {
            $this->weight = 0;
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
