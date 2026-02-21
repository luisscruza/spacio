<?php

namespace Spacio\Framework\Http;

use Spacio\Framework\Validation\Validator;

abstract class FormRequest extends Request
{
    protected array $validated = [];

    abstract public function rules(): array;

    public function validateResolved(): void
    {
        $validator = new Validator;
        $this->validated = $validator->validate($this->all(), $this->rules());
    }

    public function validated(): array
    {
        return $this->validated;
    }
}
