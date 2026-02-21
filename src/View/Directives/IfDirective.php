<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class IfDirective implements Directive
{
    public function compile(string $template): string
    {
        return preg_replace('/@if\s*\((.*?)\)/', '<?php if ($1): ?>', $template);
    }
}
