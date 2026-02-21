<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class ElseDirective implements Directive
{
    public function compile(string $template): string
    {
        return preg_replace('/@else/', '<?php else: ?>', $template);
    }
}
