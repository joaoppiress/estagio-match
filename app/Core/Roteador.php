<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Roteador
{
    private array $routes = [];

    public function add(string $method, string $route, string $controller, string $action): void
    {
        $this->routes[strtoupper($method)][trim($route, '/') ?: 'home'] = [$controller, $action];
    }

    public function dispatch(string $method, string $route): void
    {
        $route = trim($route, '/') ?: 'home';
        $method = strtoupper($method);

        if (!isset($this->routes[$method][$route])) {
            http_response_code(404);
            throw new RuntimeException('Rota nao encontrada.');
        }

        [$controllerClass, $action] = $this->routes[$method][$route];
        (new $controllerClass())->{$action}();
    }
}
