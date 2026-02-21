<?php

namespace Spacio\Framework\View;

class SectionExtractor
{
    public function extract(string $contents): array
    {
        $template = $contents;
        $script = '';

        if (preg_match('/<template>([\s\S]*?)<\/template>/i', $contents, $match)) {
            $template = $match[1];
        }

        if (preg_match('/<script>([\s\S]*?)<\/script>/i', $contents, $match)) {
            $script = trim($match[1]);
        }

        return [$template, $script];
    }
}
