<?php

namespace Spacio\Framework\Database\Connections;

use PDO;
use Spacio\Framework\Database\Contracts\ConnectionInterface;

class SQLiteConnection implements ConnectionInterface
{
    public function __construct(
        protected PDO $pdo,
    ) {
        //
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}
