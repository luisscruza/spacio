<?php

namespace Spacio\Framework\Http;

class Request
{
    private static $instance;

    protected function __construct(
        protected array $server,
        protected array $get,
        protected array $post,
        protected array $files,
        protected array $cookies,
        protected array $env
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

    public static function from(Request $request): static
    {
        return new static(
            $request->server,
            $request->get,
            $request->post,
            $request->files,
            $request->cookies,
            $request->env
        );
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
