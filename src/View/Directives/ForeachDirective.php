<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class ForeachDirective implements Directive
{
    public function compile(string $template): string
    {
        return preg_replace('/@foreach\s*\((.*?)\)/', '<?php foreach ($1): ?>', $template);
    }
}
