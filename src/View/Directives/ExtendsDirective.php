<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class ExtendsDirective implements Directive
{
    public function compile(string $template): string
    {
        return preg_replace_callback('/@extends\s*\(\s*[\"\'](.+?)[\"\']\s*\)/', function (array $matches): string {
            $view = trim($matches[1]);

            return "<?php view_extend('{$view}'); ?>";
        }, $template);
    }
}
