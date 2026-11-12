<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var list<array{string,string,array|callable}> */
    private array $routes = [];

    public function get(string $pattern, array|callable $handler): void
    {
        $this->routes[] = ['GET', $pattern, $handler];
    }

    public function post(string $pattern, array|callable $handler): void
    {
        $this->routes[] = ['POST', $pattern, $handler];
    }

    public function dispatch(Request $request): void
    {
        // /ruta/ -> /ruta (redirección permanente, mejor para SEO)
        if ($request->method() === 'GET' && $request->hasTrailingSlash()) {
            $qs = $_SERVER['QUERY_STRING'] ?? '';
            Http::redirect($request->path() . ($qs !== '' ? '?' . $qs : ''), 301);
        }

        $path = $request->path();
        $pathMatched = false;
        foreach ($this->routes as [$method, $pattern, $handler]) {
            $regex = '#^' . preg_replace('#\{[a-z_]+\}#', '([a-z0-9-]+)', $pattern) . '$#';
            if (!preg_match($regex, $path, $m)) {
                continue;
            }
            $pathMatched = true;
            if ($method !== $request->method()) {
                continue;
            }
            array_shift($m);
            if (is_array($handler)) {
                [$class, $action] = $handler;
                (new $class())->$action($request, ...$m);
            } else {
                $handler($request, ...$m);
            }
            return;
        }

        if ($pathMatched) {
            http_response_code(405);
            header('Allow: GET, POST');
            echo 'Método no permitido';
            return;
        }
        Http::notFound();
    }
}
