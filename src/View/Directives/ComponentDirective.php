<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class ComponentDirective implements Directive
{
    public function compile(string $template): string
    {
        return preg_replace_callback('/@component\s*\((.*?)\)/', function (array $matches): string {
            $expression = trim($matches[1]);

            return "<?php echo component({$expression}); ?>";
        }, $template);
    }
}
