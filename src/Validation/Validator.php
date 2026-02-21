<?php

namespace Spacio\Framework\Validation;

use RuntimeException;
use Spacio\Framework\Validation\Rules\NullableRule;
use Spacio\Framework\Validation\Rules\Rule;

class Validator
{
    public function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $rulesList = $this->normalizeRules($ruleSet);
            $isNullable = $this->hasNullable($rulesList);

            if (($value === null || $value === '') && $isNullable) {
                continue;
            }

            foreach ($rulesList as $rule) {
                if ($rule instanceof NullableRule) {
                    continue;
                }

                $result = $rule->validate($field, $value, $data);
                if ($result !== null) {
                    $errors[$field][] = $result;
                }
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return $data;
    }

    protected function normalizeRules(string|array $rules): array
    {
        if (is_string($rules)) {
            $rules = array_filter(array_map('trim', explode('|', $rules)));
        }

        $rules = array_values($rules ?? []);
        $normalized = [];

        foreach ($rules as $rule) {
            if ($rule instanceof Rule) {
                $normalized[] = $rule;

                continue;
            }

            if (is_string($rule)) {
                $normalized[] = $this->resolveRule($rule);

                continue;
            }
        }

        return $normalized;
    }

    protected function resolveRule(string $rule): Rule
    {
        [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
        $class = $this->ruleClassFromName($name);

        if (! class_exists($class)) {
            throw new RuntimeException("Unknown validation rule: {$name}");
        }

        if (! is_subclass_of($class, Rule::class)) {
            throw new RuntimeException("Validation rule {$class} must implement Rule.");
        }

        $parameter = $param !== null ? $this->castParam($param) : null;

        return $parameter === null
            ? new $class
            : new $class($parameter);
    }

    protected function ruleClassFromName(string $name): string
    {
        if (str_contains($name, '\\')) {
            return $name;
        }

        $studly = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));

        return 'Spacio\\Framework\\Validation\\Rules\\'.$studly.'Rule';
    }

    protected function castParam(string $param): mixed
    {
        if (is_numeric($param)) {
            return str_contains($param, '.') ? (float) $param : (int) $param;
        }

        return $param;
    }

    protected function hasNullable(array $rules): bool
    {
        foreach ($rules as $rule) {
            if ($rule instanceof NullableRule) {
                return true;
            }
        }

        return false;
    }
}
