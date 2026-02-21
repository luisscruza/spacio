<?php

namespace Spacio\Framework\Database;

use PDO;
use RuntimeException;
use Spacio\Framework\Database\Contracts\ConnectionInterface;

class StatementBuilder
{
    protected array $wheres = [];

    protected array $bindings = [];

    protected int $bindingIndex = 0;

    public function __construct(
        protected ConnectionInterface $connection,
        protected string $table,
        protected string $entityClass,
        protected string $primaryKey,
    ) {
        //
    }

    public function where(string $column, mixed $operator, mixed $value = null): static
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $key = $this->newBindingKey($column);

        $this->wheres[] = sprintf('%s %s :%s', $column, $operator, $key);

        $this->bindings[$key] = $value;

        return $this;
    }

    public function get(): array
    {
        $sql = $this->compileSelect();
        $statement = $this->connection->pdo()->prepare($sql);
        $statement->execute($this->bindings);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first(): ?array
    {
        $sql = $this->compileSelect().' LIMIT 1';
        
        $statement = $this->connection->pdo()->prepare($sql);

        $statement->execute($this->bindings);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(array $attributes): Entity
    {
        if ($attributes === []) {
            throw new RuntimeException('Create requires at least one attribute.');
        }

        $columns = array_keys($attributes);
        $placeholders = array_map(fn (string $key) => ':'.$key, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $statement = $this->connection->pdo()->prepare($sql);

        $statement->execute($attributes);

        $id = $this->connection->pdo()->lastInsertId();

        if ($id !== false && $id !== '0') {
            $attributes[$this->primaryKey] = is_numeric($id) ? (int) $id : $id;
        }

        $entityClass = $this->entityClass;

        if (! is_subclass_of($entityClass, Entity::class)) {
            throw new RuntimeException("{$entityClass} is not an Entity.");
        }

        $entity = new $entityClass($this->connection);
        
        $entity->fill($attributes);

        return $entity;
    }

    protected function compileSelect(): string
    {
        $sql = sprintf('SELECT * FROM %s', $this->table);

        if ($this->wheres) {
            $sql .= ' WHERE '.implode(' AND ', $this->wheres);
        }

        return $sql;
    }

    protected function newBindingKey(string $column): string
    {
        $this->bindingIndex++;

        $column = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);

        return $column.'_'.$this->bindingIndex;
    }
}
