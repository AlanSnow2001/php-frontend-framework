<?php

declare(strict_types=1);

use App\Controllers\UsuariosController;

return [
    'GET' => [
        '/' => [UsuariosController::class, 'index'],
        '/usuarios/editar' => [UsuariosController::class, 'editarFormulario'],
        '/usuarios/fila' => [UsuariosController::class, 'fila'],
    ],
    'POST' => [
        '/usuarios/eliminar' => [UsuariosController::class, 'eliminar'],
        '/usuarios/guardar' => [UsuariosController::class, 'guardarEdicion'],
    ],
];
