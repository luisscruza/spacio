<?php

namespace Spacio\Framework\Components;

class ComponentResponse
{
    public function __construct(
        protected string $html,
        protected ?string $redirect = null,
    ) {}

    public function html(): string
    {
        return $this->html;
    }

    public function redirect(): ?string
    {
        return $this->redirect;
    }
}
