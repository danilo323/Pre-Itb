<?php
// admin/base_url.php
// Calcula la ruta web hacia admin/ y hacia la raíz del sitio a partir de la
// ubicación real de los archivos en disco (no de la URL con la que se pidió
// la página). Así funciona igual si el sitio vive en la raíz del dominio
// (localhost:8000) o en una subcarpeta (localhost/itb, típico de XAMPP).

function admin_base(): string {
    static $base = null;
    if ($base === null) {
        $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
        $dir     = rtrim(str_replace('\\', '/', __DIR__), '/');
        if ($docRoot !== '' && str_starts_with($dir, $docRoot)) {
            $base = substr($dir, strlen($docRoot));
        } else {
            $base = '/admin';
        }
        if ($base === '') $base = '/admin';
    }
    return $base;
}

function site_base(): string {
    $parent = dirname(admin_base());
    $parent = str_replace('\\', '/', $parent);
    return $parent === '/' ? '/' : $parent . '/';
}
