<?php

namespace Spacio\Framework\View;

use RuntimeException;
use Spacio\Framework\View\Compilers\TemplateCompiler;

class ViewEngine
{
    protected static array $sections = [];

    protected static array $sectionStack = [];

    protected static ?string $extends = null;

    public function __construct(
        protected string $basePath,
        protected string $extension,
        protected SectionExtractor $extractor,
        protected TemplateCompiler $compiler,
    ) {}

    public function render(string $name, array $data = []): string
    {
        return $this->renderView($name, $data, true);
    }

    public function renderPartial(string $name, array $data = []): string
    {
        return $this->renderView($name, $data, false);
    }

    protected function renderView(string $name, array $data, bool $fresh): string
    {
        if ($fresh) {
            self::$extends = null;
            self::$sections = [];
            self::$sectionStack = [];
        }

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

        $output = $this->evaluate($compiled, $data);

        if (self::$extends && $fresh) {
            $output = $this->renderView(self::$extends, $data, false);
        }

        if ($script !== '') {
            $output .= "\n<script>\n{$script}\n</script>";
        }

        return $output;
    }

    protected function evaluate(string $compiled, array $data): string
    {
        ob_start();
        extract($data, EXTR_SKIP);
        eval('?>'.$compiled);

        return (string) ob_get_clean();
    }

    public static function extend(string $view): void
    {
        self::$extends = $view;
    }

    public static function sectionStart(string $name): void
    {
        self::$sectionStack[] = $name;
        ob_start();
    }

    public static function sectionEnd(): void
    {
        $name = array_pop(self::$sectionStack);
        if ($name === null) {
            return;
        }

        self::$sections[$name] = (string) ob_get_clean();
    }

    public static function yield(string $name, string $default = ''): string
    {
        return self::$sections[$name] ?? $default;
    }
}
