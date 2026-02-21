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

        return $this->renderTemplate([
            'debug' => $debug,
            'title' => $title,
            'message' => $message,
            'path' => $path,
            'method' => $method,
            'location' => $location,
            'trace' => $trace,
            'markdown' => $markdown,
        ]);
    }

    public function renderStatus(int $status, string $message, Request $request): string
    {
        $title = match ($status) {
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            default => 'Server Error',
        };

        return $this->renderTemplate([
            'debug' => false,
            'title' => $title,
            'message' => $message,
            'path' => $request->getUri(),
            'method' => $request->getMethod(),
            'location' => '',
            'trace' => '',
            'markdown' => '',
        ]);
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

    protected function renderTemplate(array $data): string
    {
        $template = BASE_PATH.'/src/Http/views/exception.php';
        if (! is_file($template)) {
            return $data['message'] ?? 'Server Error';
        }

        $debug = (bool) ($data['debug'] ?? false);
        $title = (string) ($data['title'] ?? 'Server Error');
        $message = (string) ($data['message'] ?? 'Something went wrong.');
        $path = (string) ($data['path'] ?? '');
        $method = (string) ($data['method'] ?? '');
        $location = (string) ($data['location'] ?? '');
        $trace = (string) ($data['trace'] ?? '');
        $markdown = (string) ($data['markdown'] ?? '');

        ob_start();
        include $template;

        return (string) ob_get_clean();
    }
}
