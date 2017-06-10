<?php declare(strict_types=1);

namespace LotteryEngine\Config;

/**
 * Class Config
 * @package LotteryEngine\Config
 */
class Config extends \Noodlehaus\Config
{
    /**
     * @return array
     */
    protected function getDefaults()
    {
        return [
            'database_host' => '127.0.0.1',
            'database_port' => 3306,
            'database_type' => 'mysql',
            'database_name' => 'lotterydb',
            'database_username' => 'root',
            'database_password' => 'root',
            'database_logging' => false,
            'database_prefix' => 'le_',
        ];
    }
}
