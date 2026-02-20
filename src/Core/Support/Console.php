<?php

namespace Spacio\Framework\Core\Support;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class Console
{
    public static function registerCommands(Application $app, array $groups): void
    {
        foreach ($groups as $path => $namespace) {
            if (! is_dir($path)) {
                continue;
            }

            $files = glob($path.'/*.php') ?: [];
            sort($files);

            foreach ($files as $file) {
                $class = $namespace.'\\'.pathinfo($file, PATHINFO_FILENAME);

                if (! class_exists($class) || ! is_subclass_of($class, Command::class)) {
                    continue;
                }

                $app->addCommand(new $class);
            }
        }
    }
}
