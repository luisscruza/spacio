<?php

namespace Spacio\Framework\Validation\Rules;

class MinRule implements Rule
{
    public function __construct(
        protected int $min,
    ) {}

    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null) {
            return null;
        }

        $length = is_string($value) ? strlen($value) : (is_numeric($value) ? (float) $value : 0);

        if ($length >= $this->min) {
            return null;
        }

        return ":attribute must be at least {$this->min}.";
    }
}
