<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Lottery;

/**
 * Class base
 * @package LotteryEngine\Database
 */
abstract class Base
{
    /**
     * Base constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return Database
     */
    protected function DB()
    {
        return Lottery::getInstance()->getDatabase();
    }

    /**
     * @return string
     */
    abstract protected function getTableName(): string;

    /**
     * @return array
     */
    abstract protected function toArray(): array;

    /**
     * @param array $data
     * @return object
     */
    abstract protected function toInstance(array $data);

    /**
     * @throws ErrorException
     */
    abstract protected function beforePost();

    /**
     * @throws ErrorException
     */
    abstract protected function beforePut();

    /**
     * @return bool
     * @throws ErrorException
     */
    public function post(): bool
    {
        $this->beforePost();

        $query = $this->DB()->insert($this->getTableName(), $this->toArray());
        if ($query->errorCode() !== '00000') {
            throw new ErrorException($query->errorInfo()[2]);
        }

        return true;
    }

    /**
     * @param array|string $columns
     * @return array
     */
    abstract protected function columns2data($columns): array;

    /**
     * @param array|string $columns
     * @param array|null $where
     * @return bool
     * @throws ErrorException
     */
    public function put($columns, array $where = null): bool
    {
        $this->beforePut();

        $data = $this->columns2data($columns);
        if (empty($data)) {
            return false;
        }

        $query = $this->DB()->update($this->getTableName(), $data, $where);
        if ($query->errorCode() !== '00000') {
            throw new ErrorException($query->errorInfo()[2]);
        }

        return true;
    }

    /**
     * @param array|null $where
     * @return bool
     */
    public function get(array $where = null): bool
    {
        $data = $this->DB()->get($this->getTableName(), '*', $where);
        if (!$data) {
            return false;
        }

        return !empty($this->toInstance($data));
    }

    /**
     * @param array|null $where
     * @return int
     */
    public function count(array $where = null): int
    {
        $count = $this->DB()->count($this->getTableName(), $where);
        if (!$count) {
            return -1;
        }

        return $count;
    }

    /**
     * @param string $column
     * @param int $count
     * @param array $where
     * @param array $data
     * @return bool
     */
    public function increase(string $column, int $count, array $where, array $data = []): bool
    {
        if (empty($column) || empty($count)) {
            return false;
        }

        return $this->put(array_merge($data, [$column.'[+]' => $count]), $where);
    }
}