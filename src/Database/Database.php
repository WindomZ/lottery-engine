<?php declare(strict_types=1);

namespace LotteryEngine\Database;

use LotteryEngine\Config\Config;
use Medoo\Medoo;

/**
 * Class Database
 * @package LotteryEngine\Database
 */
class Database extends Medoo
{
    /**
     * @var bool
     */
    private $supportJSON = false;

    /**
     * @return bool
     */
    public function getSupportJSON()
    {
        return $this->supportJSON;
    }

    /**
     * Database constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        parent::__construct(
            [
                'database_type' => $config->get('database_type'),
                'database_name' => $config->get('database_name'),
                'server' => $config->get('database_host'),
                'username' => $config->get('database_username'),
                'password' => $config->get('database_password'),
                'port' => $config->get('database_port'),
                'charset' => $config->get('database_charset'),
                'logging' => $config->get('database_logging'),
                'prefix' => $config->get('database_prefix'),
                'option' => $config->get('database_option'),
                'command' => $config->get('database_command'),
            ]
        );

        $this->supportJSON = boolval($config->get('database_json'));
    }

    /**
     * @param $table
     * @return string
     */
    public function tableQuote($table)
    {
        return $this->prefix.$table;
    }
}
