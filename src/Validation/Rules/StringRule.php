<?php

namespace Spacio\Framework\Validation\Rules;

class StringRule implements Rule
{
    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_string($value) ? null : "{$field} must be a string.";
    }
}
