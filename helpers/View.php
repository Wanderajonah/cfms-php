<?php

declare(strict_types=1);

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'app'): void
    {
        extract($data, EXTR_OVERWRITE);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/' . $layout . '.php';
    }
}
