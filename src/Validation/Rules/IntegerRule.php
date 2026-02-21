<?php

namespace Spacio\Framework\Validation\Rules;

class IntegerRule implements Rule
{
    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return null;
        }

        return ':attribute must be an integer.';
    }
}
