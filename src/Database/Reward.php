<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Exception\ErrorException;

/**
 * Class Reward
 * @package LotteryEngine\Database
 */
class Reward extends BaseTemplate2
{
    const COL_AWARD_ID = 'award_id';
    const COL_AWARD_CLASS = 'award_class';
    const COL_AWARD_KIND = 'award_kind';
    const COL_SIZE = 'size';
    const COL_COUNT = 'count';

    /**
     * @var string
     */
    public $award_id = '';

    /**
     * @var int
     */
    public $award_class = 0;

    /**
     * @var int
     */
    public $award_kind = 0;

    /**
     * @var int
     */
    public $size = 0;

    /**
     * @var int
     */
    public $count = 0;

    /**
     * @return Reward
     */
    protected function newObject()
    {
        return new Reward();
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return 'reward';
    }

    /**
     * @return array
     */
    protected function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                self::COL_AWARD_ID => $this->award_id,
                self::COL_AWARD_CLASS => $this->award_class,
                self::COL_AWARD_KIND => $this->award_kind,
                self::COL_SIZE => $this->size,
                self::COL_COUNT => $this->count,
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

        $this->award_id = $data[self::COL_AWARD_ID];
        $this->award_class = intval($data[self::COL_AWARD_CLASS]);
        $this->award_kind = intval($data[self::COL_AWARD_KIND]);
        $this->size = intval($data[self::COL_SIZE]);
        $this->count = intval($data[self::COL_COUNT]);

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
     * @return bool
     */
    public function pass(): bool
    {
        return $this->active && $this->count < $this->size;
    }
}
