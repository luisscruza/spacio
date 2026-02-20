<?php

namespace Spacio\Framework\Http;

use Throwable;

class ExceptionRenderer
{
    public function render(Throwable $throwable, Request $request): string
    {
        $debug = $this->isDebug();
        $title = $debug ? $throwable::class : 'Server Error';
        $message = $debug ? $throwable->getMessage() : 'Something went wrong.';
        $path = $request->getUri();
        $method = $request->getMethod();
        $location = '';
        $trace = '';
        $markdown = '';

        if ($debug) {
            $location = $throwable->getFile().':'.$throwable->getLine();
            $trace = $throwable->getTraceAsString();
            $markdown = $this->buildMarkdown($throwable, $request);
        }

        $template = BASE_PATH.'/src/Http/views/exception.php';
        if (! is_file($template)) {
            return $message;
        }

        ob_start();
        include $template;

        return (string) ob_get_clean();
    }

    protected function buildMarkdown(Throwable $throwable, Request $request): string
    {
        $title = $throwable::class;
        $message = $throwable->getMessage();
        $location = $throwable->getFile().':'.$throwable->getLine();
        $method = $request->getMethod();
        $path = $request->getUri();
        $trace = $throwable->getTraceAsString();

        return <<<MD
# {$title}

**Message:** {$message}

**Request:** {$method} {$path}

**Location:** {$location}

```text
{$trace}
```
MD;
    }

    protected function isDebug(): bool
    {
        $value = env('APP_DEBUG', false);

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }
}
