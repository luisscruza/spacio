<?php

namespace Spacio\Framework\Http;

use ReflectionNamedType;
use ReflectionParameter;
use Spacio\Framework\Container\Container;
use Spacio\Framework\Container\Exceptions\ContainerException;

class ControllerResolver
{
    public function __construct(
        protected Container $container,
    ) {}

    public function resolve(string $controller, string $method, array $vars): array
    {
        $controllerInstance = $this->container->get($controller);
        $arguments = $this->resolveMethodArguments($controllerInstance, $method, $vars);

        return [$controllerInstance, $method, $arguments];
    }

    protected function resolveMethodArguments(object $controller, string $method, array $vars): array
    {
        $reflection = new \ReflectionMethod($controller, $method);

        return array_map(
            fn (ReflectionParameter $parameter) => $this->resolveMethodParameter($parameter, $vars),
            $reflection->getParameters()
        );
    }

    protected function resolveMethodParameter(ReflectionParameter $parameter, array $vars): mixed
    {
        $name = $parameter->getName();
        if (array_key_exists($name, $vars)) {
            return $vars[$name];
        }

        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
            return $this->container->get($type->getName());
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new ContainerException("Unable to resolve parameter {$name}.");
    }
}
