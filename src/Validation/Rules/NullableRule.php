<?php

namespace Spacio\Framework\Validation\Rules;

class NullableRule implements Rule
{
    public function validate(string $field, mixed $value, array $data): ?string
    {
        return null;
    }
}
