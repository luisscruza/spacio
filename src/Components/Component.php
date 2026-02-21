<?php

namespace Spacio\Framework\Components;

use ReflectionClass;

abstract class Component
{
    protected array $props = [];

    protected array $data = [];

    protected ?string $redirectTo = null;

    public function mount(array $props = [], array $data = []): void
    {
        $this->props = $props;
        $this->data = $data;

        foreach ($props as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function view(): string
    {
        $reflection = new ReflectionClass($this);
        $short = $reflection->getShortName();

        if (str_ends_with($short, 'Component')) {
            $short = substr($short, 0, -9);
        }

        return strtolower($short);
    }

    public function data(): array
    {
        $vars = [];
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $vars[$property->getName()] = $property->getValue($this);
        }

        return array_merge($this->props, $this->data, $vars);
    }

    public function redirect(string $url): void
    {
        $this->redirectTo = $url;
    }

    public function redirectTo(): ?string
    {
        return $this->redirectTo;
    }
}
