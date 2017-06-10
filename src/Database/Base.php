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
        return false;
    }

    /**
     * @param array|string $columns
     * @return bool
     * @throws ErrorException
     */
    public function put($columns = []): bool
    {
        return false;
    }

    /**
     * @param $where
     * @return bool
     */
    public function get($where): bool
    {
        return false;
    }
}
