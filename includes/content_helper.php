<?php
// includes/content_helper.php
// Lee el contenido editable: primero de la sesión del admin,
// si no existe, devuelve el valor por defecto del HTML original.

function content_get(string $section, string $field, string $default = ''): string {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $value = $_SESSION['admin_data'][$section][$field] ?? $default;
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Versión sin escapar (para URLs, src de imágenes, etc.)
function content_raw(string $section, string $field, string $default = ''): string {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    return $_SESSION['admin_data'][$section][$field] ?? $default;
}
