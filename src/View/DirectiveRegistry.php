<?php

namespace Spacio\Framework\View;

use Spacio\Framework\View\Contracts\Directive;

class DirectiveRegistry
{
    protected array $directives = [];

    public function add(Directive $directive): void
    {
        $this->directives[] = $directive;
    }

    public function compile(string $template): string
    {
        foreach ($this->directives as $directive) {
            $template = $directive->compile($template);
        }

        return $template;
    }
}
