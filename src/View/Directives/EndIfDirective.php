<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class EndIfDirective implements Directive
{
    public function compile(string $template): string
    {
        return preg_replace('/@endif/', '<?php endif; ?>', $template);
    }
}
