<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Rule as DbRule;
use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Util\Uuid;

/**
 * Class Rule
 * @package LotteryEngine\Model
 */
class Rule extends DbRule
{
    /**
     * Rule constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Rule
     */
    protected function newObject()
    {
        return new Rule();
    }

    /**
     * @param string|null $id
     * @return Rule|null
     */
    public static function object(string $id = null)
    {
        $obj = new Rule();
        if ($id && !$obj->getById($id)) {
            return null;
        }

        return $obj;
    }

    /**
     * @param string $play_id
     * @param string $reward_id
     * @param int $weight
     * @return Rule
     * @throws ErrorException
     */
    public static function create(string $play_id, string $reward_id, int $weight)
    {
        if (!Uuid::isValid($play_id)) {
            throw new ErrorException('"play_id" should be UUID: '.$play_id);
        }
        if (!Uuid::isValid($reward_id)) {
            throw new ErrorException('"reward_id" should be UUID: '.$reward_id);
        }
        $reward = Reward::object($reward_id);
        if (empty($reward)) {
            throw new ErrorException('"reward_id" should be existed: '.$reward_id);
        }
        if ($weight < 0) {
            $weight = 0;
        }

        $obj = new Rule();

        $obj->play_id = $play_id;
        $obj->reward_id = $reward_id;
        $obj->name = $reward->name;
        $obj->active = $reward->active;
        $obj->weight = $weight;

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
        return (new Rule())->select($where, $limit, $page, $order);
    }

    /**
     * @param string $play_id
     * @return array|null
     */
    public static function rules(string $play_id)
    {
        $list = Rule::list([Rule::COL_PLAY_ID => $play_id]);
        if (!empty($list) && $list[Rule::ARG_SIZE]) {
            return $list[Rule::ARG_DATA];
        }

        return null;
    }

    /**
     * @param string $play_id
     * @return array|null
     */
    public static function weights(string $play_id)
    {
        $list = Rule::rules($play_id);
        if (empty($list)) {
            return null;
        }
        $result = array();
        foreach ($list as $l) {
            if ($l instanceof Rule) {
                $result[$l->reward_id] = $l->weight;
            }
        }

        return $result;
    }
}
