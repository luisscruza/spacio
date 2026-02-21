<?php

namespace Spacio\Framework\Http;

use ReflectionClass;
use ReflectionMethod;
use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\Http\Attributes\Route as RouteAttribute;

class RouteRegistrar
{
    public function __construct(
        protected ConfigRepository $config,
    ) {
        //
    }

    public function routes(): array
    {
        $routes = $this->loadRoutesFile();
        $routes = array_merge($routes, $this->loadAttributeRoutes());

        return $this->sortRoutes($routes);
    }

    protected function loadRoutesFile(): array
    {
        $path = BASE_PATH.'/routes/web.php';

        if (! is_file($path)) {
            return [];
        }

        $routes = require $path;

        return is_array($routes) ? $routes : [];
    }

    protected function loadAttributeRoutes(): array
    {
        $config = $this->config->get('routes', []);
        $controllers = $config['controllers'] ?? [];
        $routes = [];

        foreach ($controllers as $group) {
            $path = $group['path'] ?? null;
            $namespace = $group['namespace'] ?? null;

            if (! $path || ! $namespace || ! is_dir($path)) {
                continue;
            }

            $files = glob($path.'/*.php') ?: [];
            sort($files);

            foreach ($files as $file) {
                $class = $namespace.'\\'.pathinfo($file, PATHINFO_FILENAME);

                if (! class_exists($class)) {
                    continue;
                }

                $routes = array_merge($routes, $this->routesFromController($class));
            }
        }

        return $routes;
    }

    protected function routesFromController(string $class): array
    {
        $reflection = new ReflectionClass($class);
        $routes = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(RouteAttribute::class) as $attribute) {
                /** @var RouteAttribute $route */
                $route = $attribute->newInstance();
                $methods = $route->methods ?: ['GET'];

                $routes[] = [
                    $methods,
                    $route->path,
                    [$class, $method->getName()],
                ];
            }
        }

        return $routes;
    }

    protected function sortRoutes(array $routes): array
    {
        usort($routes, function (array $left, array $right): int {
            $leftPath = $left[1] ?? '';
            $rightPath = $right[1] ?? '';

            $leftIsVariable = $this->isVariableRoute($leftPath);
            $rightIsVariable = $this->isVariableRoute($rightPath);

            if ($leftIsVariable === $rightIsVariable) {
                return 0;
            }

            return $leftIsVariable ? 1 : -1;
        });

        return $routes;
    }

    protected function isVariableRoute(string $path): bool
    {
        return str_contains($path, '{') && str_contains($path, '}');
    }
}
