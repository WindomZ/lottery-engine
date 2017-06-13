<?php declare(strict_types=1);

namespace LotteryEngine\Database;

/**
 * Class BaseList
 * @package LotteryEngine\Database
 */
abstract class BaseList extends Base
{
    /**
     * @var array
     */
    protected $select_list = array();

    /**
     * @return array
     */
    protected function getList(): array
    {
        return $this->select_list;
    }

    /**
     * @param $obj
     */
    protected function addList($obj)
    {
        if (!empty($obj)) {
            array_push($this->select_list, $obj);
        }
    }

    /**
     * @var int
     */
    protected $select_size = 0;

    /**
     * @return int
     */
    protected function getSize(): int
    {
        return $this->select_size;
    }

    /**
     * @param $data
     * @return object
     */
    abstract protected function addInstance($data);

    /**
     * @param array|null $where
     * @param int $limit
     * @param int $page
     * @return bool
     */
    private function _select(array $where = null, int $limit = 0, int $page = 0)
    {
        $this->select_size = $this->DB()->count($this->getTableName(), $where);
        if (!$this->select_size) {
            return true;
        }

        if ($limit > 0) {
            if (!$where) {
                $where = array();
            }
            if ($page > 0) {
                $where['LIMIT'] = [$limit * $page, $limit];
            } else {
                $where['LIMIT'] = $limit;
            }
        }

        $data = $this->DB()->select($this->getTableName(), '*', $where);

        if (!$data || gettype($data) !== 'array') {
            return false;
        }

        foreach ($data as $item) {
            if (empty($this->addInstance($item))) {
                return false;
            }
        }

        return true;
    }

    const ARG_DATA = 'data';
    const ARG_SIZE = 'size';
    const ARG_LIMIT = 'limit';
    const ARG_PAGE = 'page';

    /**
     * @param array|null $where
     * @param int $limit
     * @param int $page
     * @return array|null
     */
    public function select(array $where = null, int $limit = 0, int $page = 0)
    {
        if ($this->_select($where, $limit, $page)) {
            return [
                self::ARG_DATA => $this->getList(),
                self::ARG_SIZE => $this->getSize(),
                self::ARG_LIMIT => $limit,
                self::ARG_PAGE => $page,
            ];
        }

        return null;
    }
}