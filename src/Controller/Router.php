<?php
require_once 'Controller.php';

class Router {
    private $routes = [];

    public function addRoute($method, $path, $callable) {
        $this->routes[] = ['method' => $method, 'path' => $path, 'callable' => $callable];
    }

    public function handleRequest() {
        // Get request method and path
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestPath) {
                $controller = new Controller;
                call_user_func_array(array($controller, $route['callable']), []);
                return;
            }
        }

        // No matching route found
        http_response_code(404);
        echo "Route not found: $requestMethod $requestPath";
    }
}
