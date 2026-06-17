<?php

declare(strict_types=1);

namespace System\Core;

class Vite
{
    private static bool $isDev = false;
    private static string $devServer = 'http://localhost:5173';
    private static ?array $manifest = null;

    public static function use(string $entry): string
    {
        // Simple check if Vite dev server might be running
        // Para simplificar, en desarrollo, confiaremos en que el servidor esté activo.
        // En producción, buscaremos el manifest.json
        $manifestPath = __DIR__ . '/../../public/build/.vite/manifest.json';
        
        if (!file_exists($manifestPath)) {
            // Modo Desarrollo (Hot Module Replacement)
            return <<<HTML
<script type="module" src="{$devServer}/@vite/client"></script>
<script type="module" src="{$devServer}/{$entry}"></script>
HTML;
        }

        // Modo Producción
        if (self::$manifest === null) {
            self::$manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (!isset(self::$manifest[$entry])) {
            return "<!-- Vite entry '{$entry}' not found in manifest -->";
        }

        $file = self::$manifest[$entry]['file'];
        $cssFiles = self::$manifest[$entry]['css'] ?? [];

        $html = '<script type="module" src="/build/' . $file . '"></script>';
        foreach ($cssFiles as $css) {
            $html .= '<link rel="stylesheet" href="/build/' . $css . '">';
        }

        return $html;
    }
}
