<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Reward as DbReward;
use LotteryEngine\Exception\ErrorException;
use SHMCache\Block;

/**
 * Class Reward
 * @package LotteryEngine\Model
 */
class Reward extends DbReward
{
    const ID_NULL = '00000000-0000-0000-0000-000000000001';
    const ID_AGAIN = '00000000-0000-0000-0000-000000000002';

    /**
     * @var Block
     */
    private static $cache;

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

        if (!self::$cache) {
            self::$cache = new Block(3600);
        }
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
                    $obj->id = $id;
                    $obj->name = 'Not winning';
                    $obj->active = true;
                    $obj->fake = true;

                    return $obj;
                case self::ID_AGAIN:
                    $obj->id = $id;
                    $obj->name = 'Try again';
                    $obj->active = true;
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
     * @param array|null $order
     * @return array|null
     */
    public static function list(array $where = null, int $limit = 0, int $page = 0, array $order = null)
    {
        return (new Reward())->select($where, $limit, $page, $order);
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
    public function put($columns, array $where = []): bool
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
     * @return bool
     */
    public function passSync()
    {
        if ($this->fake) {
            return true;
        }

        if (!$this->pass()) {
            return false;
        }

        $data = self::$cache->get($this->id);
        if (!is_array($data)) {
            if (!$this->refresh()) {
                return false;
            }
            $data = array(
                'active' => $this->active,
                'count' => $this->count,
                'size' => $this->size,
            );
            self::$cache->save($this->id, $data);

            return true;
        }

        return boolval($data['active']) && intval($data['count']) < intval($data['size']);
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

        if ($column === self::COL_COUNT) {
            $arr = self::$cache->get($this->id);
            if (is_array($arr)) {
                $arr['count'] = intval($arr['count']) + 1;
                self::$cache->save($this->id, $arr);
            }
        }

        return parent::increase($column, $count, $where, $data);
    }

    /**
     * @param string $award_id
     * @param int $award_class
     * @param int $award_kind
     * @return Reward
     */
    public function setAward(string $award_id = '', int $award_class = 0, int $award_kind = 0)
    {
        $this->award_id = $award_id;
        $this->award_class = $award_class;
        $this->award_kind = $award_kind;

        return $this;
    }
}
