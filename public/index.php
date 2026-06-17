<?php

declare(strict_types=1);

session_start();

// Autoloader básico para clases de app/
spl_autoload_register(function (string $class) {
    // Reemplaza los namespaces por rutas de directorio
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;

// Cargar las rutas
$routes = require __DIR__ . '/../routes.php';

// Inicializar el Router y despachar
$router = new Router($routes);
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
