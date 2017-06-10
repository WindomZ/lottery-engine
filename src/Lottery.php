<?php declare(strict_types=1);

namespace LotteryEngine;

use LotteryEngine\Config\Config;

/**
 * Class Lottery
 * @package LotteryEngine
 */
class Lottery
{
    /**
     * @var Lottery
     */
    private static $_instance;

    private function __clone()
    {
    }

    /**
     * @return Lottery
     */
    public static function getInstance(): Lottery
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new Lottery();
        }

        return self::$_instance;
    }

    protected $config;

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    protected $database;

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @var string
     */
    private static $configPath = __DIR__.'config.yml';

    /**
     * @param string $configPath
     */
    public static function setConfigPath(string $configPath)
    {
        self::$configPath = $configPath;
    }

    /**
     * Lottery constructor.
     */
    private function __construct()
    {
        $this->config = new Config(self::$configPath);
        self::$_instance = $this;
    }
}
