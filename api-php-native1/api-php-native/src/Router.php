<?php
namespace Src;

class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch($method, $uri) {
        foreach ($this->routes as $route) {
            $pattern = "@^" . preg_replace('/\{([^}]+)\}/', '(?P<\1>[^/]+)', $route['path']) . "$@";
            if ($method === $route['method'] && preg_match($pattern, $uri, $matches)) {
                return call_user_func_array($route['handler'], array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
            }
        }
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Not Found']);
    }
}
