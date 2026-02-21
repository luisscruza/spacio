<?php

namespace Spacio\Framework\Core\Providers;

use Spacio\Framework\Container\Container;
use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\Database\Contracts\ConnectionInterface;
use Spacio\Framework\Database\DatabaseManager;
use Spacio\Framework\Database\Migrations\Migrator;

class DatabaseProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(DatabaseManager::class, function (Container $container) {
            return new DatabaseManager(
                $container->get(ConfigRepository::class),
                $container
            );
        });

        $this->container->singleton(ConnectionInterface::class, function (Container $container) {
            return $container->get(DatabaseManager::class)->connection();
        });

        $this->container->singleton(Migrator::class, function (Container $container) {
            return new Migrator(
                $container->get(ConnectionInterface::class),
                $container->get(ConfigRepository::class)
            );
        });
    }
}
