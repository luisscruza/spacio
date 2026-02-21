<?php

namespace Spacio\Framework\Http\Exceptions;

use RuntimeException;

class HttpException extends RuntimeException
{
    public function __construct(
        protected int $statusCode,
        string $message = '',
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
