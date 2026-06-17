<?php

declare(strict_types=1);

namespace App\Controllers;

use System\Core\Controller;

class UsuariosController extends Controller
{
    /**
     * Inicializa nuestro "Store" usando las sesiones de PHP.
     */
    private function initStore(): void
    {
        // Si la sesión no existe, inicializamos nuestros datos por defecto
        if (!isset($_SESSION['usuarios'])) {
            $_SESSION['usuarios'] = [
                1 => ['id' => 1, 'nombre' => 'Ana García', 'email' => 'ana@example.com', 'rol' => 'Admin'],
                2 => ['id' => 2, 'nombre' => 'Carlos López', 'email' => 'carlos@example.com', 'rol' => 'User'],
                3 => ['id' => 3, 'nombre' => 'María Torres', 'email' => 'maria@example.com', 'rol' => 'User'],
                4 => ['id' => 4, 'nombre' => 'Javier Ruiz', 'email' => 'javier@example.com', 'rol' => 'Editor'],
            ];
        }
    }

    public function index(): void
    {
        $this->initStore();

        $this->render('usuarios', [
            'title' => 'Gestión de Usuarios',
            'usuarios' => $_SESSION['usuarios']
        ]);
    }

    public function eliminar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $this->initStore();
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        usleep(300000); // 300ms retardo para ver la animación

        // Eliminar de nuestra sesión (Store)
        if (isset($_SESSION['usuarios'][$id])) {
            unset($_SESSION['usuarios'][$id]);
        }

        // Devolver vacío causará que la fila desaparezca visualmente
        echo ""; 
    }

    /**
     * Devuelve la vista parcial del formulario para editar una fila inline.
     */
    public function editarFormulario(): void
    {
        $this->initStore();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if (!isset($_SESSION['usuarios'][$id])) {
            http_response_code(404);
            return;
        }

        $this->render('usuario_editar', [
            'u' => $_SESSION['usuarios'][$id]
        ]);
    }

    /**
     * Devuelve la vista parcial de la fila normal (lectura).
     * Útil para cancelar una edición o después de guardar.
     */
    public function fila(): void
    {
        $this->initStore();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if (!isset($_SESSION['usuarios'][$id])) {
            http_response_code(404);
            return;
        }

        $this->render('usuario_fila', [
            'u' => $_SESSION['usuarios'][$id]
        ]);
    }

    /**
     * Recibe los datos actualizados, guarda en sesión y devuelve la fila normal actualizada.
     */
    public function guardarEdicion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $this->initStore();
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if (isset($_SESSION['usuarios'][$id])) {
            // Actualizar datos
            $_SESSION['usuarios'][$id]['nombre'] = $_POST['nombre'] ?? '';
            $_SESSION['usuarios'][$id]['email']  = $_POST['email'] ?? '';
            $_SESSION['usuarios'][$id]['rol']    = $_POST['rol'] ?? '';
        }

        // Renderizar la vista de la fila normal, ahora con los datos actualizados
        $this->render('usuario_fila', [
            'u' => $_SESSION['usuarios'][$id]
        ]);
    }
}
