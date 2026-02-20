<?php

namespace Spacio\Framework\Database\Contracts;

use PDO;

interface ConnectionInterface
{
    public function pdo(): PDO;
}
