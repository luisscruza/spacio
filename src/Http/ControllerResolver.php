<?php

namespace Spacio\Framework\Http;

use ReflectionNamedType;
use ReflectionParameter;
use Spacio\Framework\Container\Container;
use Spacio\Framework\Container\Exceptions\ContainerException;
use Spacio\Framework\Database\Entity;
use Spacio\Framework\Http\Exceptions\NotFoundHttpException;

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
            $value = $vars[$name];
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                $typeName = $type->getName();

                if (is_subclass_of($typeName, Entity::class)) {
                    $entity = $typeName::resolveRouteBinding($value);

                    if (! $entity) {
                        throw new NotFoundHttpException("{$typeName} not found.");
                    }

                    return $entity;
                }
            }

            return $value;
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
