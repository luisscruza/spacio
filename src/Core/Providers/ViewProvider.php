<?php

namespace Spacio\Framework\Core\Providers;

use ReflectionClass;
use Spacio\Framework\Container\Container;
use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\View\Compilers\TemplateCompiler;
use Spacio\Framework\View\DirectiveRegistry;
use Spacio\Framework\View\Directives\Directive;
use Spacio\Framework\View\SectionExtractor;
use Spacio\Framework\View\ViewEngine;

class ViewProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(DirectiveRegistry::class, function (Container $container) {
            $registry = new DirectiveRegistry;

            $groups = [
                [
                    'path' => BASE_PATH.'/src/View/Directives',
                    'namespace' => 'Spacio\\Framework\\View\\Directives',
                ],
                [
                    'path' => BASE_PATH.'/app/View/Directives',
                    'namespace' => 'App\\View\\Directives',
                ],
            ];

            foreach ($groups as $group) {
                if (! is_dir($group['path'])) {
                    continue;
                }

                $files = glob($group['path'].'/*.php') ?: [];
                sort($files);

                foreach ($files as $file) {
                    $class = $group['namespace'].'\\'.pathinfo($file, PATHINFO_FILENAME);

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
