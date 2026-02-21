<?php

namespace Spacio\Framework\Validation\Rules;

use RuntimeException;
use Spacio\Framework\Database\Contracts\ConnectionInterface;

class UniqueRule implements Rule
{
    public function __construct(
        protected string $table,
        protected ?string $column = null,
    ) {
        //
    }

    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $column = $this->column ?: $field;
        $connection = app(ConnectionInterface::class);

        $statement = $connection->pdo()->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE {$column} = :value"
        );
        $statement->execute(['value' => $value]);

        $count = (int) $statement->fetchColumn();

        return $count > 0 ? ':attribute has already been taken.' : null;
    }

    public static function fromParameter(string $parameter): self
    {
        [$table, $column] = array_pad(explode(',', $parameter, 2), 2, null);

        if (! $table) {
            throw new RuntimeException('Unique rule requires a table name.');
        }

        return new self($table, $column ?: null);
    }
}
