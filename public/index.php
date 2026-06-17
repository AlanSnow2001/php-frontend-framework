<?php

declare(strict_types=1);

session_start();

// Autoloader básico para clases de app/ y system/
spl_autoload_register(function (string $class) {
    $prefixes = [
        'App\\' => __DIR__ . '/../app/',
        'System\\' => __DIR__ . '/../system/',
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

use System\Core\Router;

// Cargar las rutas
$routes = require __DIR__ . '/../routes.php';

// Inicializar el Router y despachar
$router = new Router($routes);
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
