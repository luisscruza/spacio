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

    protected array $attributes = [];

    protected ConnectionInterface $connection;

    public function __construct(?ConnectionInterface $connection = null)
    {
        $this->connection = $connection ?? app(ConnectionInterface::class);
        $this->table = $this->resolveTable();
    }

    public function create(array $attributes): static
    {
        $this->fill($attributes);
        $this->save();

        return $this;
    }

    public function fill(array $attributes): static
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    public function save(): void
    {
        if (array_key_exists($this->primaryKey, $this->attributes)) {
            $this->performUpdate();

            return;
        }

        $this->performInsert();
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

    protected function performInsert(): void
    {
        if ($this->attributes === []) {
            return;
        }

        $columns = array_keys($this->attributes);
        $placeholders = array_map(fn (string $key) => ':'.$key, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $statement = $this->connection->pdo()->prepare($sql);
        $statement->execute($this->attributes);

        $id = $this->connection->pdo()->lastInsertId();
        if ($id !== false && $id !== '0') {
            $this->attributes[$this->primaryKey] = is_numeric($id) ? (int) $id : $id;
        }
    }

    protected function performUpdate(): void
    {
        $id = $this->attributes[$this->primaryKey] ?? null;
        if ($id === null) {
            return;
        }

        $columns = array_filter(
            array_keys($this->attributes),
            fn (string $key) => $key !== $this->primaryKey
        );

        if ($columns === []) {
            return;
        }

        $assignments = array_map(
            fn (string $key) => $key.' = :'.$key,
            $columns
        );

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :%s',
            $this->table,
            implode(', ', $assignments),
            $this->primaryKey,
            $this->primaryKey
        );

        $statement = $this->connection->pdo()->prepare($sql);
        $statement->execute($this->attributes);
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
