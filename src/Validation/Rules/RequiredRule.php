<?php

namespace Spacio\Framework\Validation\Rules;

class RequiredRule implements Rule
{
    public function validate(string $field, mixed $value, array $data): ?string
    {
        if ($value === null || $value === '') {
            return "{$field} is required.";
        }

        return null;
    }
}
