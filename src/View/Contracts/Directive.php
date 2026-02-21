<?php

namespace Spacio\Framework\View\Contracts;

interface Directive
{
    public function compile(string $template): string;
}
