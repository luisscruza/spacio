<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class YieldDirective implements Directive
{
    public function compile(string $template): string
    {
        return preg_replace_callback('/@yield\s*\(\s*[\"\'](.+?)[\"\']\s*\)/', function (array $matches): string {
            $name = trim($matches[1]);

            return "<?php echo view_yield('{$name}'); ?>";
        }, $template);
    }
}
