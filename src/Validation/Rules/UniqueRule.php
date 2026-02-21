<?php

namespace Spacio\Framework\Validation\Rules;

use RuntimeException;
use Spacio\Framework\Database\Contracts\ConnectionInterface;

class UniqueRule implements Rule
{
    protected string $table;

    protected ?string $column;

    public function __construct(string $table, ?string $column = null)
    {
        if ($column === null && str_contains($table, ',')) {
            [$table, $column] = array_pad(array_map('trim', explode(',', $table, 2)), 2, null);
        }

        $table = trim($table);
        $column = $column !== null ? trim($column) : null;

        if ($table === '') {
            throw new RuntimeException('UniqueRule requires a table name.');
        }

        $this->table = $table;
        $this->column = $column !== '' ? $column : null;
    }

    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $column = $this->column ?? $field;
        $connection = app(ConnectionInterface::class);

        $sql = sprintf(
            'SELECT 1 FROM %s WHERE %s = :value LIMIT 1',
            $this->quoteIdentifier($this->table),
            $this->quoteIdentifier($column)
        );

        $statement = $connection->pdo()->prepare($sql);
        $statement->execute(['value' => $value]);

        if ($statement->fetchColumn() !== false) {
            return ':attribute must be unique.';
        }

        return null;
    }

    protected function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
}
