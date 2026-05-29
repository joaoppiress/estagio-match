<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'layouts/app'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require BASE_PATH . '/app/Views/' . $view . '.php';
        $content = ob_get_clean();

        require BASE_PATH . '/app/Views/' . $layout . '.php';
    }

    protected function redirect(string $url): never
    {
        \redirect($url);
    }

    public static function renderError(Throwable $exception): void
    {
        $debug = (bool) app_config('debug', false);
        $message = $debug
            ? $exception->getMessage()
            : 'Não foi possível carregar a página agora.';

        require BASE_PATH . '/app/Views/errors/500.php';
    }
}

