<?php

namespace Spacio\Framework\View\Compilers;

use Spacio\Framework\View\DirectiveRegistry;

class TemplateCompiler
{
    public function __construct(
        protected DirectiveRegistry $registry,
    ) {}

    public function compile(string $template): string
    {
        $template = $this->registry->compile($template);

        return $this->compileEchoes($template);
    }

    protected function compileEchoes(string $template): string
    {
        return preg_replace_callback('/\{\{\s*(.*?)\s*\}\}/', function (array $matches): string {
            $expression = trim($matches[1]);

            if ($expression === '') {
                return '';
            }

            if (preg_match('/^[A-Za-z_][A-Za-z0-9_.\[\]"\'\$]+$/', $expression) === 1) {
                return "<?= e(view_get('{$expression}', get_defined_vars())) ?>";
            }

            return "<?= e({$expression}) ?>";
        }, $template);
    }
}
