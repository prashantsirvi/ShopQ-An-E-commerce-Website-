<?php

declare(strict_types=1);

namespace Core;

/**
 * View renderer with layout support and XSS-safe escaping helper.
 */
final class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        $viewFile = self::resolveViewPath($view);

        if (!is_file($viewFile)) {
            throw new \RuntimeException("View [{$view}] not found.");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean() ?: '';

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutFile = self::resolveViewPath($layout);

        if (!is_file($layoutFile)) {
            throw new \RuntimeException("Layout [{$layout}] not found.");
        }

        require $layoutFile;
    }

    public static function partial(string $partial, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require self::resolveViewPath($partial);
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private static function resolveViewPath(string $view): string
    {
        $view = str_replace(['..', '\\'], ['', '/'], $view);

        return VIEWS_PATH . '/' . $view . '.php';
    }
}
