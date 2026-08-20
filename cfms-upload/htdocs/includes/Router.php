<?php

declare(strict_types=1);

final class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function patch(string $path, callable|array $handler): void
    {
        $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper((string) $_POST['_method']);
        }

        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[0-9]+)', $route['path']);
            if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $action] = $handler;
                    (new $class())->$action(...array_map('intval', array_values($params)));
                    return;
                }
                $handler(...array_map('intval', array_values($params)));
                return;
            }
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Not Found'], Auth::check() ? 'app' : 'public');
    }

    private function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[$method][] = ['path' => $path, 'handler' => $handler];
    }
}
