<?php

namespace Spacio\Framework\View\Directives;

use Spacio\Framework\View\Contracts\Directive;

class SectionDirective implements Directive
{
    public function compile(string $template): string
    {
        $template = preg_replace_callback('/@section\s*\(\s*[\"\'](.+?)[\"\']\s*\)/', function (array $matches): string {
            $name = trim($matches[1]);

            return "<?php view_section_start('{$name}'); ?>";
        }, $template);

        return preg_replace('/@endsection/', '<?php view_section_end(); ?>', $template);
    }
}
