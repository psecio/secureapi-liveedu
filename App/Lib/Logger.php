<?php

namespace App\Lib;
use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\WebProcessor;
use MySQLHandler\MySQLHandler;

class Logger
{
    private $log;

    public function __construct()
    {
        $dsn = 'mysql:hostname=localhost;dbname='.$_ENV['DB_NAME'];
        $pdo = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $mysqlHandler = new MySQLHandler($pdo, 'log', ['addl']);

        $this->log = new MonoLogger('name');
        $this->log->pushHandler($mysqlHandler);
        $this->log->pushProcessor(new WebProcessor);
    }

    public function getLogger()
    {
        return $this->log;
    }

    public function error($message, array $extra = [])
    {
        $this->getLogger()->error($message, $extra);
    }
    public function info($message, array $extra = [])
    {
        $this->getLogger()->info($message, $extra);
    }
}
