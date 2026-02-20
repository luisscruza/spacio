<?php

namespace Spacio\Framework\Core\Providers;

use Spacio\Framework\Core\Config\ConfigRepository;

class ConfigProvider extends ServiceProvider
{
    public function register(): void
    {
        $configPath = BASE_PATH.'/config';
        $items = [];

        if (is_dir($configPath)) {
            $files = glob($configPath.'/*.php') ?: [];
            sort($files);

            foreach ($files as $file) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                $items[$name] = require $file;
            }
        }

        $this->container->instance(ConfigRepository::class, new ConfigRepository($items));
    }
}
