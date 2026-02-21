<?php

namespace Spacio\Framework\Core\Providers;

use ReflectionClass;
use Spacio\Framework\Container\Container;
use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\View\Compilers\TemplateCompiler;
use Spacio\Framework\View\DirectiveRegistry;
use Spacio\Framework\View\Contracts\Directive;
use Spacio\Framework\View\SectionExtractor;
use Spacio\Framework\View\ViewEngine;

class ViewProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(DirectiveRegistry::class, function (Container $container) {
            $registry = new DirectiveRegistry;
            $config = $container->get(ConfigRepository::class)->get('view', []);
            $paths = $config['directives']['paths'] ?? [];
            $namespaces = $config['directives']['namespaces'] ?? [];

            foreach ($paths as $index => $path) {
                $namespace = $namespaces[$index] ?? null;
                if (! $namespace || ! is_dir($path)) {
                    continue;
                }

                $files = glob($path.'/*.php') ?: [];
                sort($files);

                foreach ($files as $file) {
                    $class = $namespace.'\\'.pathinfo($file, PATHINFO_FILENAME);

                    if (! class_exists($class)) {
                        continue;
                    }

                    if (! is_subclass_of($class, Directive::class)) {
                        continue;
                    }

                    $registry->add((new ReflectionClass($class))->newInstance());
                }
            }

            return $registry;
        });

        $this->container->singleton(TemplateCompiler::class, function (Container $container) {
            return new TemplateCompiler($container->get(DirectiveRegistry::class));
        });

        $this->container->singleton(SectionExtractor::class, function () {
            return new SectionExtractor;
        });

        $this->container->singleton(ViewEngine::class, function (Container $container) {
            $config = $container->get(ConfigRepository::class)->get('view', []);
            $path = $config['path'] ?? BASE_PATH.'/views';
            $extension = $config['extension'] ?? '.spacio.php';

            return new ViewEngine(
                $path,
                $extension,
                $container->get(SectionExtractor::class),
                $container->get(TemplateCompiler::class)
            );
        });
    }
}
