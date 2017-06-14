<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Reward as DbReward;
use LotteryEngine\Exception\ErrorException;

/**
 * Class Reward
 * @package LotteryEngine\Model
 */
class Reward extends DbReward
{
    const ID_NULL = '00000000-0000-0000-0000-000000000001';
    const ID_AGAIN = '00000000-0000-0000-0000-000000000002';

    /**
     * @var bool
     */
    protected $fake = false;

    /**
     * Reward constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Reward
     */
    protected function newObject()
    {
        return new Reward();
    }

    /**
     * @param string|null $id
     * @return Reward|null
     */
    public static function object(string $id = null)
    {
        $obj = new Reward();
        if ($id) {
            switch ($id) {
                case self::ID_NULL:
                case self::ID_AGAIN:
                    $obj->id = $id;
                    $obj->fake = true;

                    return $obj;
                default:
                    if (!$obj->getById($id)) {
                        return null;
                    }
            }
        }

        return $obj;
    }

    /**
     * @param array|null $where
     * @param int $limit
     * @param int $page
     * @return array|null
     */
    public static function list(array $where = null, int $limit = 0, int $page = 0)
    {
        return (new Reward())->select($where, $limit, $page);
    }

    /**
     * @return bool
     */
    public function post(): bool
    {
        if ($this->fake) {
            return true;
        }

        return parent::post();
    }

    /**
     * @param array|string $columns
     * @param array|null $where
     * @return bool
     * @throws ErrorException
     */
    public function put($columns, array $where = null): bool
    {
        if ($this->fake) {
            return true;
        }

        return parent::put($columns, $where);
    }

    /**
     * @return bool
     */
    protected function refresh(): bool
    {
        if ($this->fake) {
            return true;
        }

        return parent::refresh();
    }

    /**
     * @return bool
     */
    public function pass(): bool
    {
        if ($this->fake) {
            return true;
        }

        return parent::pass();
    }

    /**
     * @param string $column
     * @param int $count
     * @param array $where
     * @param array $data
     * @return bool
     * @throws ErrorException
     */
    protected function increase(string $column, int $count = 1, array $where = [], array $data = []): bool
    {
        if ($this->fake) {
            return true;
        }

        return parent::increase($column, $count, $where, $data);
    }
}
