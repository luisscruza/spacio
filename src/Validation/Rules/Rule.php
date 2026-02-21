<?php

namespace Spacio\Framework\Validation\Rules;

interface Rule
{
    public function validate(string $field, mixed $value, array $data): ?string;
}
