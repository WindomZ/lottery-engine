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
     * @param Play $play
     * @throws ErrorException
     */
    public static function postByPlay(Play $play)
    {
        if (!isset($play)) {
            throw new ErrorException('"play" should not be null!');
        }
        if (!is_array($play->weights)) {
            throw new ErrorException('"weights" should be array!');
        }
        foreach ($play->weights as $r => $w) {
            if (!Rule::create($play->id, $r, $w)->post()) {
                throw new ErrorException('Fail to add play rule!');
            }
        }
    }

    /**
     * @param Play $play
     * @throws ErrorException
     */
    public static function putByPlay(Play $play)
    {
        if (!isset($play)) {
            throw new ErrorException('"play" should not be null!');
        }
        if (!is_array($play->weights)) {
            throw new ErrorException('"weights" should be array!');
        }
        $list = Rule::rules($play->id);
        foreach ($play->weights as $rid => $rw) {
            $create = true;
            foreach ($list as $obj) {
                if ($obj instanceof Rule && $obj->reward_id === $rid) {
                    if ($obj->weight !== intval($rw)) {
                        $obj->weight = $rw;
                        $obj->active = $rw > 0;
                        if (!$obj->put([Rule::COL_WEIGHT, Rule::COL_ACTIVE])) {
                            throw new ErrorException('Fail to put play rule!');
                        }
                    }
                    $create = false;
                    break;
                }
            }
            if ($create && !Rule::create($play->id, $rid, $rw)->post()) {
                throw new ErrorException('Fail to post new play rule!');
            }
        }
        foreach ($list as $obj) {
            if (!($obj instanceof Rule)) {
                continue;
            }
            $delete = true;
            foreach ($play->weights as $rid => $rw) {
                if ($obj->reward_id === $rid) {
                    $delete = false;
                    break;
                }
            }
            if ($delete) {
                $play->weights[$obj->reward_id] = 0;
                $obj->weight = 0;
                $obj->active = false;
                if (!$obj->put([Rule::COL_WEIGHT, Rule::COL_ACTIVE])) {
                    throw new ErrorException('Fail to put play rule!');
                }
            }
        }
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
    private static function _weights(string $play_id)
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

    /**
     * @param Play|string $play
     * @return array|null
     */
    public static function weights($play)
    {
        if (!$play) {
            return null;
        } elseif (is_string($play)) {
            $play = Play::object($play);
        } elseif (!$play instanceof Play) {
            return null;
        }

        if ($play->hasRule() && empty($play->weights)) {
            $play->weights = self::_weights($play->id);
        }

        if ($play->sweet) {
            foreach ($play->weights as $reward_id => $weight) {
                if (!Reward::object($reward_id, true)->passSync()) {
                    $play->weights[$reward_id] = 0;
                }
            }
        }

        return $play->weights;
    }
}
