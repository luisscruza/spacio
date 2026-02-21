<?php

namespace Spacio\Framework\Database\Schema;

use RuntimeException;
use Spacio\Framework\Database\Contracts\ConnectionInterface;

class Table
{
    protected const TYPE_ALIASES = [
        'integer' => ColumnType::Integer,
        'int' => ColumnType::Integer,
        'string' => ColumnType::String,
        'text' => ColumnType::Text,
        'boolean' => ColumnType::Boolean,
        'bool' => ColumnType::Boolean,
        'datetime' => ColumnType::Datetime,
        'date' => ColumnType::Date,
        'float' => ColumnType::Float,
        'double' => ColumnType::Double,
    ];

    public static function create(string $name, array $columns, ?ConnectionInterface $connection = null): void
    {
        $connection ??= app(ConnectionInterface::class);

        $definitions = [];
        foreach ($columns as $column => $definition) {
            $definitions[] = self::buildColumnDefinition($column, $definition);
        }

        if (count($definitions) === 0) {
            throw new RuntimeException('Table::create requires at least one column definition.');
        }

        $sql = sprintf(
            'CREATE TABLE IF NOT EXISTS %s (%s)',
            self::quote($name),
            implode(', ', $definitions)
        );

        $connection->pdo()->exec($sql);
    }

    protected static function buildColumnDefinition(string $name, array $definition): string
    {
        $definition = self::normalizeDefinition($definition);
        $type = array_shift($definition);

        if (! $type || ! (is_string($type) || $type instanceof ColumnType)) {
            throw new RuntimeException("Column {$name} is missing a type.");
        }

        $sqlType = null;
        if ($type instanceof ColumnType) {
            $sqlType = $type->value;
        } else {
            $type = strtolower($type);
            $sqlType = self::TYPE_ALIASES[$type]->value ?? null;
        }

        if (! $sqlType) {
            throw new RuntimeException("Unsupported column type: {$type}.");
        }

        $clauses = [self::quote($name), $sqlType];
        $modifiers = $definition;

        $isPrimary = in_array('pk', $modifiers, true) || in_array('primary', $modifiers, true);
        $isAuto = in_array('autoincrement', $modifiers, true) || in_array('auto', $modifiers, true);
        $isUnique = in_array('unique', $modifiers, true);
        $isNullable = in_array('nullable', $modifiers, true);

        $default = null;
        foreach ($definition as $key => $value) {
            if ($key === 'default') {
                $default = $value;
                break;
            }
        }

        if ($isPrimary) {
            $clauses[] = 'PRIMARY KEY';
        }

        if ($isAuto && $sqlType === 'INTEGER') {
            $clauses[] = 'AUTOINCREMENT';
        }

        if (! $isNullable && ! $isPrimary) {
            $clauses[] = 'NOT NULL';
        }

        if ($isUnique) {
            $clauses[] = 'UNIQUE';
        }

        if ($default !== null) {
            $clauses[] = 'DEFAULT '.self::quoteDefault($default);
        }

        return implode(' ', $clauses);
    }

    protected static function normalizeDefinition(array $definition): array
    {
        $normalized = [];

        foreach ($definition as $key => $value) {
            if (is_int($key)) {
                $normalized[] = $value;

                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    protected static function quote(string $value): string
    {
        return '"'.str_replace('"', '""', $value).'"';
    }

    protected static function quoteDefault(mixed $value): string
    {
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return "'".str_replace("'", "''", (string) $value)."'";
    }
}
