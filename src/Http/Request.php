<?php

namespace Spacio\Framework\Http;

class Request
{
    private static $instance;

    private function __construct(
        private array $server,
        private array $get,
        private array $post,
        private array $files,
        private array $cookies,
        private array $env
    ) {
        //
    }

    public static function create(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(
                $_SERVER,
                $_GET, $_POST,
                $_FILES,
                $_COOKIE,
                $_ENV);
        }

        return self::$instance;
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function getUri(): string
    {
        return $this->server['REQUEST_URI'];
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }
}
