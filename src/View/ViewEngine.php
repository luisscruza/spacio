<?php

namespace Spacio\Framework\View;

use RuntimeException;

class ViewEngine
{
    public function __construct(
        protected string $basePath,
    ) {
        //
    }

    public function render(string $name, array $data = []): string
    {
        $view = str_replace('.', '/', $name);
        $path = rtrim($this->basePath, '/').'/'.$view.'.spacio.php';

        if (! is_file($path)) {
            throw new RuntimeException("View not found: {$path}");
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException("Unable to read view: {$path}");
        }

        [$template, $script] = $this->extractSections($contents);
        $compiled = $this->compile($template);

        ob_start();
        extract($data, EXTR_SKIP);
        eval('?>'.$compiled);
        $output = (string) ob_get_clean();

        if ($script !== '') {
            $output .= "\n<script>\n{$script}\n</script>";
        }

        return $output;
    }

    protected function extractSections(string $contents): array
    {
        $template = $contents;
        $script = '';

        if (preg_match('/<template>([\s\S]*?)<\/template>/i', $contents, $match)) {
            $template = $match[1];
        }

        if (preg_match('/<script>([\s\S]*?)<\/script>/i', $contents, $match)) {
            $script = trim($match[1]);
        }

        return [$template, $script];
    }

    protected function compile(string $template): string
    {
        $template = $this->compileDirectives($template);
        $template = $this->compileEchoes($template);

        return $template;
    }

    protected function compileDirectives(string $template): string
    {
        $template = preg_replace('/@foreach\s*\((.*?)\)/', '<?php foreach ($1): ?>', $template);
        $template = preg_replace('/@endforeach/', '<?php endforeach; ?>', $template);
        $template = preg_replace('/@if\s*\((.*?)\)/', '<?php if ($1): ?>', $template);
        $template = preg_replace('/@elseif\s*\((.*?)\)/', '<?php elseif ($1): ?>', $template);
        $template = preg_replace('/@else/', '<?php else: ?>', $template);
        $template = preg_replace('/@endif/', '<?php endif; ?>', $template);

        return $template;
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
