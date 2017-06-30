<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Exception\ErrorException;

/**
 * Class BaseTemplate3
 * @package LotteryEngine\Database
 */
abstract class BaseTemplate3 extends BaseTemplate2
{
    const ID_ALL = '00000000-0000-0000-0000-000000000000';

    const COL_OWNER_ID = 'owner_id';
    const COL_SHARED = 'shared';

    /**
     * @var string
     */
    public $owner_id = self::ID_ALL;

    /**
     * @var bool
     */
    public $shared = false;

    /**
     * @return array
     */
    protected function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                self::COL_OWNER_ID => $this->owner_id,
                self::COL_SHARED => $this->shared,
            ]
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    protected function toInstance(array $data)
    {
        parent::toInstance($data);

        $this->owner_id = $data[self::COL_OWNER_ID];
        $this->shared = boolval($data[self::COL_SHARED]);

        return $this;
    }

    /**
     * @throws ErrorException
     */
    protected function beforePost()
    {
        parent::beforePost();
    }

    /**
     * @throws ErrorException
     */
    protected function beforePut()
    {
        parent::beforePut();
    }

    /**
     * @param string $owner_id
     * @param bool $shared
     */
    public function setOwner(string $owner_id, bool $shared = false)
    {
        $this->owner_id = $owner_id;
        $this->shared = $shared;
    }
}
