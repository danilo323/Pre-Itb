<?php
// router.php
// Usado SOLO con el servidor embebido de PHP para desarrollo local:
//   php -S localhost:8000 router.php
// Traduce URLs "limpias" (sin .php) a los archivos reales del panel o a páginas dinámicas.

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');
$clean_slug = ltrim($path, '/');
$clean_slug = preg_replace('/\.php$/', '', $clean_slug);

$routes = [
    '/admin'          => __DIR__ . '/admin/index.php',
    '/admin/login'    => __DIR__ . '/admin/login.php',
    '/admin/logout'   => __DIR__ . '/admin/logout.php',
    '/sobre-nosotros' => __DIR__ . '/sobre-nosotros.php',
    '/noticias'       => __DIR__ . '/noticias.php',
    '/oferta-academica' => __DIR__ . '/oferta-academica.php',
];

if (isset($routes[$path])) {
    require $routes[$path];
    return true;
}

// Cargar páginas personalizadas creadas dinámicamente desde el panel
require_once __DIR__ . '/admin/storage.php';
$saved_data = storage_load();
$paginas_creadas = $saved_data['_paginas_creadas'] ?? [];

if (!empty($clean_slug)) {
    foreach ($paginas_creadas as $p) {
        if (($p['slug'] ?? '') === $clean_slug) {
            $GLOBALS['CURRENT_DYNAMIC_PAGE'] = $p;
            require __DIR__ . '/pagina_dinamica.php';
            return true;
        }
    }
}

// Página raíz (landing): que la sirva el servidor embebido normalmente.
if ($path === '') {
    return false;
}

// Cualquier archivo real que exista (otros .php, CSS, JS, imágenes, SVG...)
$file = __DIR__ . $path;
if (is_file($file)) {
    return false;
}

// Nada de lo anterior coincidió: URL inválida -> página 404 personalizada.
http_response_code(404);
require __DIR__ . '/admin/404.php';
return true;
