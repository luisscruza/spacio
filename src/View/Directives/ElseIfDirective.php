<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class ElseIfDirective implements Directive
{
    public function compile(string $template): string
    {
        return preg_replace('/@elseif\s*\((.*?)\)/', '<?php elseif ($1): ?>', $template);
    }
}
