<?php
// router.php
// Usado SOLO con el servidor embebido de PHP para desarrollo local:
//   php -S localhost:8000 router.php
// Traduce URLs "limpias" (sin .php) a los archivos reales del panel.
// En un hosting real (Apache), esto lo hace el .htaccess de la raíz.

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');

$routes = [
    '/admin'       => __DIR__ . '/admin/index.php',
    '/admin/login' => __DIR__ . '/admin/login.php',
    '/admin/logout' => __DIR__ . '/admin/logout.php',
];

if (isset($routes[$path])) {
    require $routes[$path];
    return true;
}

// Página raíz (landing): que la sirva el servidor embebido normalmente.
if ($path === '') {
    return false;
}

// Cualquier archivo real que exista (otros .php, CSS, JS, imágenes, SVG...)
// lo sirve el servidor embebido normalmente.
$file = __DIR__ . $path;
if (is_file($file)) {
    return false;
}

// Nada de lo anterior coincidió: URL inválida -> página 404 personalizada.
http_response_code(404);
require __DIR__ . '/admin/404.php';
return true;
