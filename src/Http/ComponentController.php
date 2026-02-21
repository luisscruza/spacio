<?php

namespace Spacio\Framework\Http;

use Spacio\Framework\Components\ComponentManager;

class ComponentController
{
    public function __construct(
        protected ComponentManager $components,
    ) {}

    public function handle(Request $request): Response
    {
        $payload = $request->all();
        $component = $payload['spacio_component'] ?? null;
        $action = $payload['spacio_action'] ?? null;
        $props = $this->decodeProps($payload['spacio_props'] ?? '{}');

        unset($payload['spacio_component'], $payload['spacio_action'], $payload['spacio_props']);

        if (! $component || ! $action) {
            return new Response('Invalid component request.', 400);
        }

        $result = $this->components->call($component, $action, $props, $payload);

        if ($result->redirect()) {
            return new Response('', 204, [
                'X-Spacio-Redirect' => $result->redirect(),
            ]);
        }

        return new Response($result->html());
    }

    protected function decodeProps(string $props): array
    {
        $decoded = json_decode($props, true);

        return is_array($decoded) ? $decoded : [];
    }
}
