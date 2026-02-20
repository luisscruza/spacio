<?php

namespace Spacio\Framework\Database;

use RuntimeException;
use Spacio\Framework\Container\Container;
use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\Database\Contracts\ConnectionInterface;
use Spacio\Framework\Database\Contracts\DriverInterface;

class DatabaseManager
{
    public function __construct(
        protected ConfigRepository $config,
        protected Container $container,
    ) {}

    public function connection(?string $name = null): ConnectionInterface
    {
        $databaseConfig = $this->config->get('database', []);
        $default = $databaseConfig['default'] ?? null;
        $name = $name ?? $default;

        if (! $name) {
            throw new RuntimeException('Database connection name is not configured.');
        }

        $connections = $databaseConfig['connections'] ?? [];
        $drivers = $databaseConfig['drivers'] ?? [];

        if (! isset($connections[$name])) {
            throw new RuntimeException("Database connection [{$name}] is not defined.");
        }

        if (! isset($drivers[$name])) {
            throw new RuntimeException("Database driver for [{$name}] is not defined.");
        }

        $driverClass = $drivers[$name];
        $driver = $this->container->get($driverClass);

        if (! $driver instanceof DriverInterface) {
            throw new RuntimeException("Database driver [{$driverClass}] is invalid.");
        }

        return $driver->connect($connections[$name]);
    }
}
