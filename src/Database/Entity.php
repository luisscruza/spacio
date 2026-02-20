<?php

namespace Spacio\Framework\Database;

use PDO;
use ReflectionClass;
use Spacio\Framework\Database\Attributes\Table;
use Spacio\Framework\Database\Contracts\ConnectionInterface;

abstract class Entity
{
    protected ?string $table = null;

    protected string $primaryKey = 'id';

    protected ConnectionInterface $connection;

    public function __construct(?ConnectionInterface $connection = null)
    {
        $this->connection = $connection ?? app(ConnectionInterface::class);
        $this->table = $this->resolveTable();
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

    protected function resolveTable(): string
    {
        if (! empty($this->table)) {
            return $this->table;
        }

        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes(Table::class);

        if ($attributes) {
            $table = $attributes[0]->newInstance()->name;

            if ($table !== '') {
                return $table;
            }
        }

        $shortName = $reflection->getShortName();

        return $this->pluralize(strtolower($shortName));
    }

    protected function pluralize(string $name): string
    {
        if (preg_match('/(s|x|z|ch|sh)$/', $name) === 1) {
            return $name.'es';
        }

        if (preg_match('/[^aeiou]y$/', $name) === 1) {
            return substr($name, 0, -1).'ies';
        }

        return $name.'s';
    }
}
