<?php

declare(strict_types=1);

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';

    return trim($text, '-') ?: 'item';
}

function unique_slug(string $text, callable $exists): string
{
    $base = slugify($text);
    $slug = $base;
    $counter = 1;

    while ($exists($slug)) {
        $slug = $base . '-' . $counter;
        $counter++;
    }

    return $slug;
}
