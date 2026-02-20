<?php

namespace Spacio\Framework\Core\Providers;

use Spacio\Framework\Container\Container;
use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\Database\Contracts\ConnectionInterface;
use Spacio\Framework\Database\DatabaseManager;

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
    }
}
