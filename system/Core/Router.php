<?php

declare(strict_types=1);

namespace System\Core;

class Router
{
    public function __construct(private array $routes) {}

    public function dispatch(string $uri, string $method): void
    {
        // Limpiar la URI (quitar query string)
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Manejar el caso donde no haya rutas definidas para el método
        if (!isset($this->routes[$method])) {
            $this->handleNotFound();
            return;
        }

        $routesForMethod = $this->routes[$method];

        if (isset($routesForMethod[$path])) {
            $handler = $routesForMethod[$path];
            
            // Asumimos el formato [Controlador::class, 'metodo']
            $controllerClass = $handler[0];
            $action = $handler[1];

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }

        $this->handleNotFound();
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        echo "404 - Not Found";
    }
}
