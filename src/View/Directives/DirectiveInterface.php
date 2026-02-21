<?php

namespace Spacio\Framework\View\Directives;

interface DirectiveInterface
{
    public function compile(string $template): string;
}
