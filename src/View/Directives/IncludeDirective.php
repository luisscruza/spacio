<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class IncludeDirective implements Directive
{
    public function compile(string $template): string
    {
        return preg_replace_callback('/@include\s*\(\s*[\"\'](.+?)[\"\']\s*\)/', function (array $matches): string {
            $view = trim($matches[1]);

            return "<?php echo view_render('{$view}', get_defined_vars()); ?>";
        }, $template);
    }
}
