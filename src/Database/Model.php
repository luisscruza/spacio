<?php

namespace Spacio\Framework\Database;

use PDO;
use Spacio\Framework\Database\Contracts\ConnectionInterface;

abstract class Model
{
    protected string $table;

    protected string $primaryKey = 'id';

    protected ConnectionInterface $connection;

    public function __construct(?ConnectionInterface $connection = null)
    {
        $this->connection = $connection ?? app(ConnectionInterface::class);
    }

    public function all(): array
    {
        $statement = $this->connection->pdo()->query(
            "SELECT * FROM {$this->table}"
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int|string $id): ?array
    {
        $statement = $this->connection->pdo()->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1"
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
