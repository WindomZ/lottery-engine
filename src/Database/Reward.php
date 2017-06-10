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
    const COL_SIZE = 'size';
    const COL_COUNT = 'count';

    /**
     * @var string
     */
    public $award_id = '';

    /**
     * @var int
     */
    public $size = 0;

    /**
     * @var int
     */
    public $count = 0;

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
        $this->size = $data[self::COL_SIZE];
        $this->count = $data[self::COL_COUNT];

        return $this;
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
}
