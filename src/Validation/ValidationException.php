<?php

namespace Spacio\Framework\Validation;

use Spacio\Framework\Http\Exceptions\HttpException;

class ValidationException extends HttpException
{
    public function __construct(
        protected array $errors,
        string $message = 'Validation failed.'
    ) {
        parent::__construct(422, $message);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
