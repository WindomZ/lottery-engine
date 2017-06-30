<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Exception\ErrorException;

/**
 * Class Play
 * @package LotteryEngine\Database
 */
class Play extends BaseTemplate3
{
    const COL_DAILY = 'daily';
    const COL_LIMIT = 'limit';
    const COL_SIZE = 'size';
    const COL_COUNT = 'count';
    const COL_WEIGHTS = 'weights';
    const COL_RULE = 'rule';
    const COL_SWEET = 'sweet';

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
     * @var bool
     */
    public $rule = false;

    /**
     * @var bool
     */
    public $sweet = false;

    /**
     * Play constructor.
     */
    protected function __construct()
    {
        parent::__construct();

        $this->rule = !self::DB()->getSupportJSON();
    }

    /**
     * @return Play
     */
    protected function newObject()
    {
        return new Play();
    }

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
        $arr = [
            self::COL_DAILY => $this->daily,
            self::COL_LIMIT => $this->limit,
            self::COL_SIZE => $this->size,
            self::COL_COUNT => $this->count,
            self::COL_RULE => $this->hasRule(),
            self::COL_SWEET => $this->sweet,
        ];

        if (self::DB()->getSupportJSON()) {
            $arr[self::COL_WEIGHTS] = json_encode($this->weights);
        } else {
            $this->rule = true;
            $arr[self::COL_RULE] = true;
        }

        return array_merge(
            parent::toArray(),
            $arr
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    protected function toInstance(array $data)
    {
        parent::toInstance($data);

        $this->daily = $data[self::COL_DAILY];
        $this->limit = intval($data[self::COL_LIMIT]);
        $this->size = intval($data[self::COL_SIZE]);
        $this->count = intval($data[self::COL_COUNT]);
        if (self::DB()->getSupportJSON()) {
            $this->weights = json_decode($data[self::COL_WEIGHTS], true);
            $this->rule = boolval($data[self::COL_RULE]);
        } else {
            $this->rule = true;
        }
        $this->sweet = boolval($data[self::COL_SWEET]);

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
        if ($this->limit < 0) {
            $this->limit = 0;
        }
        if ($this->size < 0) {
            throw new ErrorException('"size" should be positive!');
        }
        if ($this->count < 0) {
            throw new ErrorException('"count" should be positive!');
        }
        if (!$this->hasRule() && empty($this->weights)) {
            throw new ErrorException('"weights" should not be empty!');
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
     * @return bool
     */
    public function pass(): bool
    {
        return $this->active && $this->count < $this->size;
    }

    /**
     * @return bool
     */
    public function hasRule(): bool
    {
        return $this->rule || !self::DB()->getSupportJSON();
    }
}
