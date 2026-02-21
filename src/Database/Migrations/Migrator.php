<?php

namespace Spacio\Framework\Database\Migrations;

use PDO;
use RuntimeException;
use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\Database\Contracts\ConnectionInterface;

class Migrator
{
    public function __construct(
        protected ConnectionInterface $connection,
        protected ConfigRepository $config,
    ) {}

    public function run(): array
    {
        $path = $this->config->get('migrations.path', BASE_PATH.'/database/migrations');
        $table = $this->config->get('migrations.table', 'migrations');

        if (! is_dir($path)) {
            throw new RuntimeException("Migrations path not found: {$path}");
        }

        $this->ensureMigrationsTable($table);
        $applied = $this->getAppliedMigrations($table);

        $files = glob($path.'/*.php') ?: [];
        sort($files);

        $ran = [];
        foreach ($files as $file) {
            $name = basename($file);
            if (in_array($name, $applied, true)) {
                continue;
            }

            $migration = require $file;
            if (! $migration instanceof Migration) {
                throw new RuntimeException("Migration {$name} must return a Migration instance.");
            }

            $migration->up($this->connection);
            $this->recordMigration($table, $name);
            $ran[] = $name;
        }

        return $ran;
    }

    protected function ensureMigrationsTable(string $table): void
    {
        $pdo = $this->connection->pdo();

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS {$table} (".
            'id INTEGER PRIMARY KEY AUTOINCREMENT, '.
            'migration TEXT NOT NULL UNIQUE, '.
            'executed_at TEXT NOT NULL'.
            ')'
        );
    }

    protected function getAppliedMigrations(string $table): array
    {
        $pdo = $this->connection->pdo();
        $statement = $pdo->query("SELECT migration FROM {$table} ORDER BY id ASC");

        if (! $statement) {
            return [];
        }

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    protected function recordMigration(string $table, string $name): void
    {
        $pdo = $this->connection->pdo();
        $statement = $pdo->prepare(
            "INSERT INTO {$table} (migration, executed_at) VALUES (:migration, :executed_at)"
        );

        $statement->execute([
            'migration' => $name,
            'executed_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
