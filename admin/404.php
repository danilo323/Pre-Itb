<?php
// admin/404.php
require_once __DIR__ . '/base_url.php';
$SB = site_base();
// Con Apache/.htaccess esta página se sirve por RewriteRule y nadie fija el
// código de estado (router.php sí lo hace en php -S). Sin esto responde 200.
if (!headers_sent() && http_response_code() === 200) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars($SB, ENT_QUOTES, 'UTF-8') ?>css/404.css">
</head>
<body>
    <h1 class="error-code">404</h1>
    <h2 class="error-title">Page Not Found</h2>
    <p class="error-desc">La página que buscas no existe o ha sido movida.</p>
    <a href="<?= htmlspecialchars($SB, ENT_QUOTES, 'UTF-8') ?>" class="btn-back">Back To Homepage</a>
</body>
</html>
