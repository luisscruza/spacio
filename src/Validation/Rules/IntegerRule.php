<?php

namespace Spacio\Framework\Validation\Rules;

class IntegerRule implements Rule
{
    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_INT) !== false
            ? null
            : "{$field} must be an integer.";
    }
}
