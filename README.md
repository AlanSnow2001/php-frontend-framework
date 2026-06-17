# Mini-Framework Frontend Reactivo (PHP 8.5 + HTMX)

Bienvenido a este Mini-Framework PHP diseñado para servir como un **Backend for Frontend (BFF)**. Su principal filosofía es la extrema simplicidad, eliminando la necesidad de frameworks complejos de JavaScript (como React, Angular o Vue) y delegando la interactividad y reactividad directamente al HTML utilizando **HTMX**.

Este framework está pensado para consumir APIs REST externas y servir vistas HTML rápidas y reactivas.

---

## 🚀 ¿Cómo empezar?

### Requisitos
- PHP 8.0 o superior (recomendado PHP 8.5 para aprovechar tipado estricto y features modernos).
- Servidor web (Nginx, Apache) o simplemente usar el servidor integrado de PHP para desarrollo.

### Levantar el proyecto (Modo Desarrollo)
Puedes levantar el proyecto rápidamente usando el servidor integrado de PHP. Abre tu terminal en la raíz del proyecto y ejecuta:

```bash
php -S localhost:8000 -t public/
```

Luego, visita `http://localhost:8000` en tu navegador.

---

## 📁 Estructura del Proyecto y Dónde Escribir tu Código

El framework impone una estructura de directorios estricta y predecible:

```text
/mi-framework-front
 ├── /app
 │    ├── /Controllers      👉 Aquí creas los Controladores (lógica de negocio y vistas).
 │    ├── /Core             👉 Núcleo del framework (¡No modificar a menos que sepas lo que haces!).
 │    └── /Views            👉 Aquí creas los archivos .phtml (HTML con variables PHP).
 │         └── layout.phtml 
 ├── /public
 │    └── index.php         👉 Punto de entrada principal (Front Controller).
 ├── routes.php             👉 Aquí registras todas tus URLs.
 └── nginx.conf.example     👉 Configuración sugerida para Nginx.
```

### ¿Cómo crear una nueva página/funcionalidad?

Para crear una nueva página (por ejemplo, una página de "Productos"), sigue estos 3 pasos:

#### 1. Crea la Vista (`app/Views/productos.phtml`)
Crea un archivo con el diseño en HTML. Puedes usar `<?= $variable ?>` para imprimir datos pasados por el controlador.

#### 2. Crea el Controlador (`app/Controllers/ProductosController.php`)
Los controladores extienden de `App\Core\Controller`. Crea una clase y añade un método (ej. `index()`) que obtenga los datos y llame a `$this->render()`:

```php
<?php
namespace App\Controllers;
use App\Core\Controller;

class ProductosController extends Controller {
    public function index(): void {
        // Aquí podrías usar HttpClient::get('https://api.tu-backend.com/productos')
        $datos = ['title' => 'Lista de Productos', 'productos' => ['Manzana', 'Pera']];
        $this->render('productos', $datos); 
    }
}
```

#### 3. Registra la Ruta (`routes.php`)
Dile al framework qué URL debe apuntar a tu nuevo controlador:

```php
return [
    'GET' => [
        '/' => [UsuariosController::class, 'index'],
        '/productos' => [\App\Controllers\ProductosController::class, 'index'], // <--- Nueva ruta
    ],
    // ...
];
```

---

## ⚡ ¿Qué es HTMX y cómo funciona aquí?

**HTMX** es una pequeña librería de JavaScript (incluida en el `<head>` de `layout.phtml`) que te permite acceder a AJAX, transiciones CSS, WebSockets y Server Sent Events directamente desde atributos en tu HTML.

**El Rol de HTMX:**
En lugar de escribir código JavaScript (fetch/axios) para enviar datos o actualizar partes de la pantalla, HTMX lo hace declarativamente por ti. 

### ¿Cómo sabe el botón "Eliminar" a dónde ir? (El caso de `usuarios.phtml`)

Si observas el botón de eliminar en `app/Views/usuarios.phtml`:

```html
<button class="btn-delete"
        hx-post="/usuarios/eliminar" 
        hx-vals='{"id": <?= $u['id'] ?>}'
        hx-target="closest tr" 
        hx-swap="outerHTML swap:0.3s">
    Eliminar
</button>
```

Aquí está la magia explicada paso a paso:

1. **`hx-post="/usuarios/eliminar"`**: Esto le dice a HTMX: *"Cuando alguien haga clic en este botón, haz una petición HTTP POST a la URL `/usuarios/eliminar` en segundo plano (AJAX)"*.
2. **`hx-vals='{"id": 1}'`**: Adjunta datos a la petición POST. En este caso, envía el ID del usuario.
3. **El enrutador (`routes.php`) entra en acción**: 
   La petición llega a `public/index.php`, que llama al `Router`. El Router revisa el archivo `routes.php` y encuentra esto:
   ```php
   'POST' => [
       '/usuarios/eliminar' => [UsuariosController::class, 'eliminar'],
   ]
   ```
   ¡Ahí está la conexión! El router ve que la URL `/usuarios/eliminar` bajo el método `POST` está conectada al método `eliminar()` de la clase `UsuariosController`.
4. **El Controlador responde (`UsuariosController::eliminar()`)**:
   El método recibe la petición, realiza la acción (ej. borrar en base de datos) y retorna HTML (o en este caso, una respuesta vacía `""`).
5. **`hx-target="closest tr"` y `hx-swap="outerHTML"`**: 
   HTMX recibe la respuesta del controlador. `hx-target` le dice: *"Busca el elemento `<tr>` (la fila de la tabla) más cercano a este botón"*. Luego, `hx-swap` le dice: *"Reemplaza todo ese `<tr>` (outerHTML) con la respuesta del servidor"*. Como el servidor respondió en blanco (`""`), la fila simplemente desaparece de la pantalla de forma reactiva, **¡sin necesidad de recargar toda la página!**

---

## 🛠️ Utilidades del Core

### Realizar llamadas a APIs REST Externas (`HttpClient`)
Como este framework es un BFF (Backend For Frontend), es muy común que necesites pedir datos a otros microservicios. Para ello, utiliza la clase `App\Core\HttpClient`:

```php
use App\Core\HttpClient;

// Petición GET simple
$jsonResponse = HttpClient::get('https://api.ejemplo.com/users');

// Petición POST con body JSON
$respuesta = HttpClient::post('https://api.ejemplo.com/users', [
    'nombre' => 'Juan',
    'rol' => 'Admin'
]);

// Peticiones con headers de Autorización (Bearer Token)
$seguro = HttpClient::get('https://api.ejemplo.com/perfil', [
    'Authorization: Bearer mi_token_secreto'
]);
```

### El Controlador Base (`App\Core\Controller`)
El controlador base posee la función protegida `$this->render($vista, $datos)`. Esta función es inteligente gracias a HTMX:
- Si visitas la URL de forma tradicional desde el navegador, empaquetará tu vista (ej. `usuarios.phtml`) dentro del cascarón `layout.phtml` (el cual tiene el `<html>`, `<head>`, etc.).
- Si la petición la hizo HTMX (revisando si existe la cabecera HTTP `HX-Request`), el controlador **no** incluirá el `layout.phtml` y devolverá solo el trozo de HTML necesario. Esto hace que la aplicación sea increíblemente rápida.
