<?php

namespace Spacio\Framework\Validation\Rules;

class StringRule implements Rule
{
    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            return null;
        }

        return ':attribute must be a string.';
    }
}
