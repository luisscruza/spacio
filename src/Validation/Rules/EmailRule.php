<?php

namespace Spacio\Framework\Validation\Rules;

class EmailRule implements Rule
{
    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false
            ? null
            : "{$field} must be a valid email address.";
    }
}
