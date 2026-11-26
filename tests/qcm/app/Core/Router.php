<?php
declare(strict_types=1);

namespace App\Core;

use App\Middleware\CsrfMiddleware;
use App\Middleware\MiddlewareInterface;

final class Router
{
    /** @var array<int, array{method:string, regex:string, class:string, action:string, middlewares:MiddlewareInterface[]}> */
    private array $routes = [];

    /**
     * @param MiddlewareInterface[] $middlewares exécutés dans l'ordre avant le contrôleur ; [] = route publique
     */
    public function add(string $method, string $path, string $class, string $action, array $middlewares = []): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path) . '$#';
        $this->routes[] = compact('method', 'regex', 'class', 'action', 'middlewares');
    }

    /**
     * Retrouve la route correspondant à la requête.
     * @return array{route: array, params: array}|null
     */
    public function match(string $method, string $uri): ?array
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method || !preg_match($route['regex'], $path, $m)) {
                continue;
            }
            // Paramètres d'URL ({id}) : chaînes numériques converties en int
            $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            $params = array_map(fn(string $v) => ctype_digit($v) ? (int) $v : $v, array_values($params));
            return ['route' => $route, 'params' => $params];
        }
        return null;
    }

    public function dispatch(string $method, string $uri): void
    {
        $found = $this->match($method, $uri);
        if ($found === null) {
            http_response_code(404);
            echo 'Page introuvable';
            return;
        }
        $route = $found['route'];

        $pipeline = $route['middlewares'];
        if ($method !== 'GET' && $method !== 'HEAD') {
            $pipeline[] = new CsrfMiddleware();
        }
        foreach ($pipeline as $middleware) {
            $middleware->handle();
        }

        (new $route['class']())->{$route['action']}(...$found['params']);
    }
}
