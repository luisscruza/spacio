<?php

namespace Spacio\Framework\Database\Drivers;

use PDO;
use Spacio\Framework\Database\Connections\SQLiteConnection;
use Spacio\Framework\Database\Contracts\ConnectionInterface;
use Spacio\Framework\Database\Contracts\DriverInterface;

class SQLiteDriver implements DriverInterface
{
    public function connect(array $config): ConnectionInterface
    {
        $database = $config['database'] ?? ':memory:';
        $dsn = 'sqlite:'.$database;

        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return new SQLiteConnection($pdo);
    }
}
