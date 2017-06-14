<?php declare(strict_types=1);

namespace LotteryEngine\Model;

use LotteryEngine\Database\Record as DbRecord;
use LotteryEngine\Exception\ErrorException;
use LotteryEngine\Util\Uuid;

/**
 * Class Record
 * @package LotteryEngine\Model
 */
class Record extends DbRecord
{
    const ID_NULL = '00000000-0000-0000-0000-000000000001';
    const ID_AGAIN = '00000000-0000-0000-0000-000000000002';

    /**
     * Record constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Record
     */
    protected function newObject()
    {
        return new Record();
    }

    /**
     * @param string|null $id
     * @return Record|null
     */
    public static function object(string $id = null)
    {
        $obj = new Record();
        if ($id) {
            switch ($id) {
                case self::ID_NULL:
                case self::ID_AGAIN:
                    $obj->id = $id;
                    $obj->reward_id = $id;
                    $obj->winning = true;

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
     * @param string $user_id
     * @param string $play_id
     * @param string $reward_id
     * @return Record
     * @throws ErrorException
     */
    public static function create(string $user_id, string $play_id, string $reward_id)
    {
        if (!Uuid::isValid($user_id)) {
            throw new ErrorException('"user_id" should be UUID: '.$user_id);
        }
        if (!Uuid::isValid($play_id)) {
            throw new ErrorException('"play_id" should be UUID: '.$play_id);
        }
        if (!Uuid::isValid($reward_id)) {
            throw new ErrorException('"reward_id" should be UUID: '.$reward_id);
        }

        $obj = new Record();

        $obj->id = Uuid::uuid();
        $obj->user_id = $user_id;
        $obj->play_id = $play_id;
        $obj->reward_id = $reward_id;

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
        return (new Record())->select($where, $limit, $page);
    }

    /**
     * @param array|null $where
     * @return int
     */
    public static function total(array $where = null): int
    {
        return (new Record())->count($where);
    }

    /**
     * @param string $where
     * @param array $map
     * @return int
     */
    public static function totalQuery(string $where, array $map = []): int
    {
        return (new Record())->countQuery($where, $map);
    }
}
