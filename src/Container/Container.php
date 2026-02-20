<?php

namespace Spacio\Framework\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Spacio\Framework\Container\Exceptions\ContainerException;
use Spacio\Framework\Container\Exceptions\NotFoundException;

class Container
{
    protected array $bindings = [];

    protected array $instances = [];

    public function bind(string $id, Closure|string|null $concrete = null): void
    {
        $this->bindings[$id] = [
            'concrete' => $concrete ?? $id,
            'shared' => false,
        ];
    }

    public function singleton(string $id, Closure|string|null $concrete = null): void
    {
        $this->bindings[$id] = [
            'concrete' => $concrete ?? $id,
            'shared' => true,
        ];
    }

    public function instance(string $id, mixed $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->instances)
            || array_key_exists($id, $this->bindings)
            || class_exists($id);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (array_key_exists($id, $this->bindings)) {
            $binding = $this->bindings[$id];
            $object = $this->build($binding['concrete']);

            if ($binding['shared'] === true) {
                $this->instances[$id] = $object;
            }

            return $object;
        }

        if (! class_exists($id)) {
            throw new NotFoundException("No entry or class found for {$id}.");
        }

        return $this->build($id);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    protected function build(Closure|string $concrete): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        $reflector = new ReflectionClass($concrete);

        if (! $reflector->isInstantiable()) {
            throw new ContainerException("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $concrete;
        }

        $dependencies = array_map(
            fn (ReflectionParameter $parameter) => $this->resolveDependency($parameter),
            $constructor->getParameters()
        );

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @throws NotFoundException
     * @throws ContainerException
     */
    protected function resolveDependency(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
            return $this->get($type->getName());
        }

        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof ReflectionNamedType && ! $unionType->isBuiltin()) {
                    return $this->get($unionType->getName());
                }
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        $name = $parameter->getName();
        throw new ContainerException("Unable to resolve parameter {$name}.");
    }
}
