<?php

namespace Spacio\Framework\Validation\Rules;

class MaxRule implements Rule
{
    public function __construct(
        protected int $max,
    ) {}

    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null) {
            return null;
        }

        $length = is_string($value) ? strlen($value) : (is_numeric($value) ? (float) $value : 0);

        return $length <= $this->max ? null : "{$field} must be at most {$this->max}.";
    }
}
