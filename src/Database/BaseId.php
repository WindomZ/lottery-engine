<?php declare(strict_types=1);

namespace LotteryEngine\Database;

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
     * @return bool
     */
    protected function beforePut()
    {
        return Uuid::isValid($this->id);
    }

    /**
     * @param array|string $columns
     * @return bool
     */
    public function put($columns = []): bool
    {
        if ($columns !== '*') {
            $columns = array_diff($columns, [self::COL_ID]);
            $columns = array_diff($columns, [self::COL_POST_TIME]);
            array_push($columns, self::COL_PUT_TIME);
        }

        $this->put_time = 'NOW()';

        return parent::put($columns);
    }
}
