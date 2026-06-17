# Mini-Framework Frontend Reactivo (PHP 8.5 + HTMX + Vite + Tailwind)

Bienvenido a este Mini-Framework PHP diseñado para servir como un **Backend for Frontend (BFF)**. Su principal filosofía es la extrema simplicidad, eliminando la necesidad de frameworks complejos de JavaScript (como React, Angular o Vue) y delegando la interactividad y reactividad directamente al HTML utilizando **HTMX**.

Ahora integrado con **Vite** y **Tailwind CSS v4** de forma local, proporcionando una experiencia de desarrollo moderna con Hot Module Replacement (HMR) y construcciones óptimas para producción.

---

## 📦 Conceptos Clave: ¿Cómo funcionan Node.js y PHP juntos?

Es completamente normal confundirse sobre cómo funciona Node.js en un proyecto PHP. Aquí te lo aclaramos:

**¿El proyecto trae Node.js o PHP instalados por defecto?**
**No.** Tanto PHP como Node.js son programas (motores) que deben estar instalados en tu sistema operativo (Windows, Mac, Linux).
El proyecto solo trae **archivos de texto**. Entre ellos, el archivo `package.json`, que actúa como una "lista de compras" que le dice a Node.js qué herramientas frontend descargar (como Vite, Tailwind y HTMX).

**La separación de roles:**
1. **Node.js y NPM (Solo para Desarrollo):** Se usa estrictamente para el frontend. Al ejecutar `npm install`, Node descarga las dependencias listadas en `package.json` hacia una carpeta llamada `node_modules` (esta carpeta nunca se sube a producción). Node.js compilará tu CSS de Tailwind y empaquetará HTMX.
2. **PHP (Desarrollo y Producción):** Es tu backend. Ejecuta el enrutador (`Router`), los controladores y renderiza las vistas (`.phtml`).

> [!IMPORTANT]
> **No necesitas Node.js en tu servidor de producción.** Una vez que compilas tus assets con `npm run build`, se genera una carpeta `public/build/`. Esa carpeta compilada es lo único de frontend que necesitas subir a tu hosting (junto con el código PHP). 

---

## 🚀 Requisitos e Instalación

### Requisitos del Sistema
- **PHP 8.0 o superior** (Se recomienda PHP 8.5 para tipado estricto).
- **Node.js 18+** y NPM (Solo necesario en la computadora donde programas).
- Servidor web (Nginx, Apache) o simplemente usar el servidor integrado de PHP para desarrollo local.

### Levantar el Proyecto (Modo Desarrollo)

1. **Instala las dependencias de Node.js**:
   Abre tu terminal en la raíz del proyecto y ejecuta:
   ```bash
   npm install
   ```
   *Esto creará la carpeta `node_modules` e instalará Vite, Tailwind y HTMX.*

2. **Levanta el servidor de assets (Vite)**:
   ```bash
   npm run dev
   ```
   *Deja esta terminal abierta. Vite vigilará tus cambios en CSS y JS para inyectarlos en tiempo real.*

3. **Levanta el servidor de PHP**:
   Abre **otra ventana de terminal** y ejecuta:
   ```bash
   php -S localhost:8000 -t public/
   ```
4. Visita `http://localhost:8000` en tu navegador. 

### Modo Producción
Cuando estés listo para subir tu página al servidor real, debes ejecutar:
```bash
npm run build
```
Esto creará archivos minificados y optimizados en `public/build/`. Sube todo el proyecto a tu servidor, excepto la carpeta `node_modules`.

---

## 🔄 ¿Cómo mantengo actualizado el framework?

Mantener tu proyecto al día es muy sencillo:

1. **Actualizar Vite, Tailwind y HTMX (Frontend)**:
   Abre tu terminal y ejecuta `npm update`. Esto buscará versiones menores o parches nuevos (según lo definido en tu `package.json`) de forma segura. Si quieres forzar la instalación de una versión mayor nueva de un paquete, puedes hacer `npm install nombre_paquete@latest`.
2. **Actualizar PHP o Node.js**:
   Deberás descargar e instalar las nuevas versiones directamente desde sus páginas oficiales hacia tu sistema operativo.
3. **Actualizar el Núcleo del Framework (`system/Core`)**:
   El código dentro de la carpeta `system/Core` es el corazón de este mini-framework. Para actualizarlo, simplemente reemplaza esa carpeta con la versión más reciente del repositorio oficial del framework. ¡Por esta razón es vital que **nunca modifiques** los archivos dentro de `system/Core`!

---

## 📖 Guía de Uso Completa (Paso a Paso)

El marco impone un patrón estricto MVC simplificado. Para crear una nueva página (ejemplo: un panel de "Productos"), siempre debes seguir 3 pasos:

### 1. Registrar la Ruta (`routes.php`)
Este archivo es el "mapa" de tu aplicación. Recibe la URL del navegador y decide a qué Controlador enviarla.

Abre `routes.php` y añade tu ruta:
```php
<?php
use App\Controllers\ProductosController;

return [
    'GET' => [
        '/' => [\App\Controllers\UsuariosController::class, 'index'],
        '/productos' => [ProductosController::class, 'index'], // <--- Nueva ruta GET
    ],
    'POST' => [
        '/productos/guardar' => [ProductosController::class, 'guardar'], // <--- Ruta POST para formularios o HTMX
    ]
];
```

### 2. Crear el Controlador (`app/Controllers/ProductosController.php`)
El controlador es el cerebro. Obtiene datos (de una API o base de datos) y se los pasa a la vista. Todo controlador **debe heredar** de `System\Core\Controller`.

Crea el archivo:
```php
<?php
namespace App\Controllers;
use System\Core\Controller;

class ProductosController extends Controller 
{
    public function index(): void 
    {
        // 1. Aquí podrías pedir datos usando HttpClient
        $datos = [
            'title' => 'Lista de Productos',
            'productos' => ['Teclado', 'Ratón', 'Monitor']
        ];
        
        // 2. Renderizar la vista
        // Formato: $this->render('nombre_vista', $datos_array, 'nombre_layout_opcional');
        $this->render('productos/index', $datos); 
    }
}
```

### 3. Crear las Vistas y usar Layouts (`app/Views/`)
El framework usa archivos `.phtml` (HTML que permite inyectar variables PHP). 
Basado en el paso anterior, debemos crear el archivo `app/Views/productos/index.phtml`:

```html
<!-- app/Views/productos/index.phtml -->
<div class="glass-panel">
    <h2>Catálogo de Productos</h2>
    <ul>
        <!-- Usamos sintaxis corta de PHP para imprimir variables -->
        <?php foreach ($productos as $producto): ?>
            <li class="text-indigo-400 font-bold"><?= htmlspecialchars($producto) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
```

**¿Cómo funcionan los Layouts?**
Si te fijas, el código de arriba no tiene la etiqueta `<html>` ni `<body>`. Eso es porque el Controlador Base (`System\Core\Controller`) automáticamente envuelve tu vista dentro de un "Layout".

- El layout por defecto está en `app/Views/Layouts/main.phtml`. Contiene el menú de navegación, la importación de Tailwind y Vite, etc.
- Puedes crear múltiples layouts (ej. `admin_layout.phtml`). Para usar uno específico, pásalo como tercer parámetro en tu controlador:
  ```php
  $this->render('productos/index', $datos, 'admin_layout');
  ```

---

## ⚡ HTMX: Reactividad sin escribir JavaScript

HTMX está preinstalado. La magia ocurre cuando añades atributos `hx-*` a tus elementos HTML.

**Ejemplo de un botón Eliminar (sin recargar la página):**
En tu vista (`.phtml`), puedes poner:
```html
<button 
    hx-post="/productos/eliminar" 
    hx-vals='{"id": 5}'
    hx-target="closest tr" 
    hx-swap="outerHTML">
    Eliminar
</button>
```

**¿Qué sucede al hacer clic?**
1. **`hx-post`**: HTMX hace una petición AJAX (en segundo plano) tipo POST a `/productos/eliminar`.
2. **`hx-vals`**: Adjunta los datos `{id: 5}` a la petición.
3. El Router en `routes.php` dirige esa URL a un método en tu Controlador.
4. El Controlador borra el registro y, como fue una llamada HTMX, **el framework es lo suficientemente inteligente para NO inyectar el Layout maestro**. Retornará solo un texto vacío o una pequeña porción de HTML.
5. **`hx-target` y `hx-swap`**: HTMX toma esa respuesta del servidor, busca la fila (`<tr>` más cercano) y la reemplaza (`outerHTML`) por la respuesta (al ser vacía, la fila simplemente desaparece de la pantalla).

Todo esto sin escribir ni una sola línea de JavaScript, logrando una interfaz ultra rápida.

---

## 🛡️ Utilidades Extra: Llamadas a APIs REST

Dado que este framework actúa como Backend for Frontend (BFF), es probable que no te conectes directo a una base de datos, sino a un microservicio externo en Java, Python, Go, etc.

Para eso usamos `System\Core\HttpClient`:

```php
use System\Core\HttpClient;

// Petición GET simple
$jsonResponse = HttpClient::get('https://api.empresa.com/users');

// Petición POST con body JSON
$respuesta = HttpClient::post('https://api.empresa.com/users', [
    'nombre' => 'Juan'
]);

// Peticiones seguras con Headers (Bearer Tokens)
$seguro = HttpClient::get('https://api.empresa.com/perfil', [
    'Authorization: Bearer mi_token_secreto'
]);
```
