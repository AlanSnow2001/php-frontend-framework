---
description: Detalles del mini-framework front reactivo con PHP
---

Eres un Arquitecto de Software y Desarrollador Backend Experto. Tu tarea es diseñar y generar el código base para un "Mini-Framework Frontend" Server-Side Rendering (SSR) utilizando exclusivamente PHP 8.5 y HTML puro con HTMX.

La filosofía fundamental es: Simplicidad extrema, Cero frameworks de JavaScript, Inyección de Dependencias limpia, y un Enrutamiento Centralizado predecible. Este framework servirá como una capa visual rápida (BFF - Backend For Frontend) que consumirá APIs REST externas.

### 1. Stack y Entorno
* **Lenguaje:** PHP 8.5 puro.
* **Reactividad:** HTMX (vía CDN en el layout principal).
* **Servidor Web Objetivo:** Nginx (El entorno local no requiere Docker, usa el servidor nativo).

### 2. Estructura del Proyecto
Debes generar la siguiente estructura de directorios estricta:

/mi-framework-front
 ├── /app
 │    ├── /Controllers      # Clases de Controladores
 │    ├── /Core             # Núcleo del framework (Router, Request, HttpClient)
 │    └── /Views            # Archivos .phtml (HTML mezclado con PHP nativo)
 │         └── layout.phtml # Plantilla maestra con la etiqueta de HTMX
 ├── /public
 │    └── index.php         # Front Controller (Punto único de entrada)
 ├── routes.php             # Archivo centralizado de rutas
 └── nginx.conf.example     # Configuración básica para redireccionar a public/index.php

### 3. Especificaciones por Módulo a Desarrollar

**A. El Núcleo (/app/Core)**
* **Router (`Router.php`):** Una clase que cargue el arreglo de `routes.php`. Debe capturar la URI, hacer coincidir la ruta exacta, e instanciar dinámicamente el Controlador y ejecutar el método indicado. Debe manejar errores 404 de forma limpia.
* **HttpClient (`HttpClient.php`):** Una clase utilitaria (usando `cURL` nativo de PHP) para realizar peticiones GET, POST, PUT, DELETE a una API REST genérica. Debe manejar la inyección de cabeceras (como Bearer tokens) de forma sencilla.

**B. Enrutamiento y Front Controller**
* **`public/index.php`:** Debe arrancar la sesión, instanciar el `Router`, cargar las rutas y despachar la petición.
* **`routes.php`:** Devuelve un array asociativo limpio. Ejemplo: `return ['/' => ['HomeController', 'index']];`

**C. Controladores y Vistas (El flujo de trabajo)**
* **Controlador Base (`Controller.php`):** Opcional, pero útil para tener un método `render($view, $data = [])` que extraiga las variables y requiera el `layout.phtml`, inyectando la vista específica en su interior.
* **Vistas (`.phtml`):** Usarán sintaxis nativa `<?= htmlspecialchars($var) ?>`.

### 4. Instrucciones de Ejecución para la IA
Genera el código en el siguiente orden, asegurando el uso de tipado estricto (`declare(strict_types=1);`) y las nuevas características de PHP 8.5:

1.  **Paso 1:** Genera el `nginx.conf.example` para que el tráfico vaya al `public/index.php` y crea el `public/index.php`.
2.  **Paso 2:** Genera el archivo `routes.php` con dos rutas de ejemplo (una GET para listar y una POST para eliminar simulando HTMX).
3.  **Paso 3:** Desarrolla la clase `Router.php` que procesará esas rutas.
4.  **Paso 4:** Crea la clase `HttpClient.php` para las peticiones REST.
5.  **Paso 5:** Crea el `layout.phtml` base y un `UsuariosController.php` con su vista `usuarios.phtml` que demuestre cómo interactuar con HTMX (`hx-post`, `hx-target`) para eliminar un registro sin recargar la página.