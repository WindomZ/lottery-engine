<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Util\Uuid;

/**
 * Class BaseId
 * @package LotteryEngine\Database
 */
abstract class BaseId extends Base
{
    const COL_ID = 'id';
    const COL_POST_TIME = 'post_time';
    const COL_PUT_TIME = 'put_time';

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $post_time;

    /**
     * @var string
     */
    public $put_time;

    /**
     * @return array
     */
    protected function toArray(): array
    {
        return [
            self::COL_ID => $this->id,
            self::COL_POST_TIME => $this->post_time,
            self::COL_PUT_TIME => $this->put_time,
        ];
    }

    /**
     * @param array $data
     * @return $this
     */
    protected function toInstance(array $data)
    {
        $this->id = $data[self::COL_ID];
        $this->post_time = $data[self::COL_POST_TIME];
        $this->put_time = $data[self::COL_PUT_TIME];

        return $this;
    }

    /**
     * @throws ErrorException
     */
    protected function beforePost()
    {
    }

    /**
     * @return bool
     */
    public function post(): bool
    {
        $this->id = Uuid::uuid();
        $this->post_time = 'NOW()';
        $this->put_time = 'NOW()';

        return parent::post();
    }

    /**
     * @throws ErrorException
     */
    protected function beforePut()
    {
        Uuid::isValid($this->id);
    }

    /**
     * @param array|string $columns
     * @return array
     */
    protected function columns2data($columns): array
    {
        if (empty($columns)) {
            return [];
        }

        $data = $this->toArray();

        if ($columns !== '*') {
            $columns = array_diff($columns, [self::COL_ID]);
            $data = array_intersect_key($data, array_flip($columns));
        }

        return $data;
    }

    /**
     * @param array|string $columns
     * @param array|null $where
     * @return bool
     * @throws ErrorException
     */
    public function put($columns, array $where = null): bool
    {
        if ($columns !== '*') {
            $columns = array_diff($columns, [self::COL_ID]);
            $columns = array_diff($columns, [self::COL_POST_TIME]);
            array_push($columns, self::COL_PUT_TIME);
        }

        $this->put_time = 'NOW()';

        return parent::put($columns, $where);
    }
}