<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Reward as DbReward;

/**
 * Class Reward
 * @package LotteryEngine\Model
 */
class Reward extends DbReward
{
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
        if ($id && !$obj->getById($id)) {
            return null;
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
}
