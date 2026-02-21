<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class ErrorsDirective implements Directive
{
    public function compile(string $template): string
    {
        $template = preg_replace('/@errors\s*\(\s*\)/', '<?php echo view_errors(); ?>', $template);

        return preg_replace_callback('/@errors\s*\(\s*[\"\'](.+?)[\"\']\s*\)/', function (array $matches): string {
            $key = trim($matches[1]);

            return "<?php echo view_errors('{$key}'); ?>";
        }, $template);
    }
}
