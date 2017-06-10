<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Exception\ErrorException;

/**
 * Class Play
 * @package LotteryEngine\Database
 */
class Play extends BaseTemplate2
{
    const COL_RULE = 'rule';
    const COL_DAILY = 'daily';
    const COL_LIMIT = 'limit';
    const COL_SIZE = 'size';
    const COL_COUNT = 'count';
    const COL_WEIGHTS = 'weights';

    /**
     * @var string
     */
    public $rule = '';

    /**
     * @var bool
     */
    public $daily = true;

    /**
     * @var int
     */
    public $limit = 0;

    /**
     * @var int
     */
    public $size = 0;

    /**
     * @var int
     */
    public $count = 0;

    /**
     * @var array
     */
    public $weights = array();

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return 'play';
    }

    /**
     * @return array
     */
    protected function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                self::COL_RULE => $this->rule,
                self::COL_DAILY => $this->daily,
                self::COL_LIMIT => $this->limit,
                self::COL_SIZE => $this->size,
                self::COL_COUNT => $this->count,
                self::COL_WEIGHTS => $this->weights,
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

        $this->rule = $data[self::COL_RULE];
        $this->daily = $data[self::COL_DAILY];
        $this->limit = $data[self::COL_LIMIT];
        $this->size = $data[self::COL_SIZE];
        $this->count = $data[self::COL_COUNT];
        $this->weights = $data[self::COL_WEIGHTS];

        return $this;
    }

    /**
     * @throws ErrorException
     */
    protected function beforePost()
    {
        if ($this->limit < 0) {
            $this->limit = 0;
        }
        if ($this->size < 0) {
            throw new ErrorException('"size" should be positive!');
        }
        if ($this->count < 0) {
            throw new ErrorException('"count" should be positive!');
        }
        if (empty($this->weights)) {
            throw new ErrorException('"weights" should be empty!');
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
