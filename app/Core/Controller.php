<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    /**
     * Renderiza una vista específica dentro del layout principal.
     * Si la petición viene de HTMX (header HX-Request), podríamos no enviar el layout.
     */
    protected function render(string $view, array $data = [], string $layout = 'layout'): void
    {
        // Extraemos el array asociativo a variables (ej: ['nombre' => 'Juan'] pasa a $nombre)
        extract($data);

        // Comprobamos si es una petición HTMX
        $isHtmxRequest = isset($_SERVER['HTTP_HX_REQUEST']) && $_SERVER['HTTP_HX_REQUEST'] === 'true';

        $viewFile = __DIR__ . '/../Views/' . $view . '.phtml';
        
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Vista no encontrada: " . htmlspecialchars($view);
            return;
        }

        // Si es HTMX, solo renderizamos el pedazo de vista
        if ($isHtmxRequest) {
            require $viewFile;
            return;
        }

        // Si es una petición normal, capturamos la vista y la inyectamos en el layout
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = __DIR__ . '/../Views/' . $layout . '.phtml';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }
}
