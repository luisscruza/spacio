<?php

namespace Spacio\Framework\Components;

use ReflectionMethod;
use RuntimeException;
use Spacio\Framework\Core\Config\ConfigRepository;
use Spacio\Framework\Core\Support\Str;
use Spacio\Framework\View\ViewEngine;

class ComponentManager
{
    public function __construct(
        protected ViewEngine $views,
        protected ConfigRepository $config,
    ) {
        //
    }

    public function render(string $name, array $props = [], array $data = []): string
    {
        $component = $this->make($name, $props, $data);

        return $this->renderComponent($component, $name, $props);
    }

    public function call(string $name, string $action, array $props, array $data): ComponentResponse
    {
        $component = $this->make($name, $props, $data);

        if (! method_exists($component, $action)) {
            throw new RuntimeException("Component action {$action} not found.");
        }

        $method = new ReflectionMethod($component, $action);
        $args = $this->resolveActionArgs($method, $data);
        $method->invokeArgs($component, $args);

        $html = $this->renderComponent($component, $name, $props);

        return new ComponentResponse($html, $component->redirectTo());
    }

    protected function renderComponent(Component $component, string $name, array $props): string
    {
        $view = $this->viewName($component, $name);
        $html = $this->views->renderPartial($view, $component->data());

        $propsJson = htmlspecialchars(json_encode($props, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}', ENT_QUOTES, 'UTF-8');

        return sprintf(
            '<div data-spacio-component="%s" data-spacio-props="%s">%s</div>',
            htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            $propsJson,
            $html
        );
    }

    protected function make(string $name, array $props, array $data): Component
    {
        $class = $this->resolveClass($name);

        if (! class_exists($class)) {
            throw new RuntimeException("Component {$name} not found.");
        }

        if (! is_subclass_of($class, Component::class)) {
            throw new RuntimeException("Component {$class} must extend Component.");
        }

        $component = new $class;
        $component->mount($props, $data);

        return $component;
    }

    protected function resolveClass(string $name): string
    {
        if (str_contains($name, '\\')) {
            return $name;
        }

        $namespace = $this->config->get('components.namespace', 'App\\Components');
        $segments = array_map(fn (string $segment) => Str::studly($segment), explode('.', $name));
        $segments[count($segments) - 1] .= 'Component';

        return $namespace.'\\'.implode('\\', $segments);
    }

    protected function viewName(Component $component, string $name): string
    {
        $prefix = $this->config->get('components.view_prefix', 'components');
        $view = $component->view();

        if ($view === '') {
            $view = $name;
        }

        return trim($prefix.'.'.$view, '.');
    }

    protected function resolveActionArgs(ReflectionMethod $method, array $data): array
    {
        $params = $method->getParameters();

        if (count($params) === 0) {
            return [];
        }

        if (count($params) === 1) {
            return [$data];
        }

        $args = [];
        foreach ($params as $param) {
            $name = $param->getName();
            $args[] = $data[$name] ?? $param->getDefaultValue();
        }

        return $args;
    }
}
