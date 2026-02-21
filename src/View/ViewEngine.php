<?php

namespace Spacio\Framework\View;

use RuntimeException;
use Spacio\Framework\View\Compilers\TemplateCompiler;

class ViewEngine
{
    public function __construct(
        protected string $basePath,
        protected string $extension,
        protected SectionExtractor $extractor,
        protected TemplateCompiler $compiler,
    ) {}

    public function render(string $name, array $data = []): string
    {
        $view = str_replace('.', '/', $name);
        $path = rtrim($this->basePath, '/').'/'.$view.$this->extension;

        if (! is_file($path)) {
            throw new RuntimeException("View not found: {$path}");
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException("Unable to read view: {$path}");
        }

        [$template, $script] = $this->extractor->extract($contents);
        $compiled = $this->compiler->compile($template);

        ob_start();
        extract($data, EXTR_SKIP);
        eval('?>'.$compiled);
        $output = (string) ob_get_clean();

        if ($script !== '') {
            $output .= "\n<script>\n{$script}\n</script>";
        }

        return $output;
    }
}
