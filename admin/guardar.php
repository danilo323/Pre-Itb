<?php
// admin/guardar.php
// Guarda temporalmente en $_SESSION (Versión 2.0)

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$section_key = $_POST['section'] ?? '';
$schema = require __DIR__ . '/schema_mock.php';

if (!isset($schema['items'][$section_key])) {
    $_SESSION['flash_message'] = 'Sección no válida.';
    $_SESSION['flash_type']    = 'error';
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['admin_data'])) {
    $_SESSION['admin_data'] = [];
}

// 1. Procesar campos de texto enviados
foreach ($_POST as $post_key => $raw_value) {
    // Si la clave tiene doble guion bajo, ej: hero__titulo
    if (strpos($post_key, '__') !== false) {
        list($sub_key, $field) = explode('__', $post_key, 2);
        
        // Si el valor es de la sección plana (ej: footer__logo_blanco)
        // O si es de un acordeón (hero)
        if (!isset($_SESSION['admin_data'][$sub_key])) {
            $_SESSION['admin_data'][$sub_key] = [];
        }
        
        // Limpiamos (podemos llamar a field_parse si importamos _loader.php)
        $_SESSION['admin_data'][$sub_key][$field] = is_string($raw_value) 
            ? htmlspecialchars(trim($raw_value), ENT_QUOTES, 'UTF-8') 
            : $raw_value;
    }
}

// 2. Procesar imágenes / archivos enviados
foreach ($_FILES as $file_key => $file_info) {
    // Los inputs de archivo se llaman, por ejemplo: hero__imagen_1_file
    if (strpos($file_key, '__') !== false && str_ends_with($file_key, '_file')) {
        // Extraemos hero y imagen_1
        $base_key = substr($file_key, 0, -5); // quitamos '_file'
        list($sub_key, $field) = explode('__', $base_key, 2);
        
        if ($file_info['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $file_info['tmp_name'];
            $name     = basename($file_info['name']);
            
            $upload_dir = dirname(__DIR__) . '/img/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $new_name = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', $name);
            $dest     = $upload_dir . $new_name;
            
            if (move_uploaded_file($tmp_name, $dest)) {
                if (!isset($_SESSION['admin_data'][$sub_key])) {
                    $_SESSION['admin_data'][$sub_key] = [];
                }
                $_SESSION['admin_data'][$sub_key][$field] = 'img/' . $new_name;
            }
        }
    }
}

$_SESSION['flash_message'] = '✅ Cambios guardados correctamente.';
$_SESSION['flash_type']    = 'success';

// Volver al singleton (y mantener la sección abierta si quisiéramos)
header('Location: singleton.php?c=' . urlencode($section_key));
exit;
