<?php
// admin/guardar.php
// Guarda temporalmente en $_SESSION hasta que Persona 3 conecte el storage.php real.

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$section = $_POST['section'] ?? '';
$schema  = require __DIR__ . '/schema_mock.php';

// Validar que la sección existe en el schema (whitelist)
if (!array_key_exists($section, $schema)) {
    $_SESSION['flash_message'] = 'Sección no válida.';
    $_SESSION['flash_type']    = 'error';
    header('Location: /');
    exit;
}

// Guardar los campos en la sesión (temporal, sin BD)
if (!isset($_SESSION['admin_data'])) {
    $_SESSION['admin_data'] = [];
}

$campos_validos = array_keys($schema[$section]['fields']);
foreach ($campos_validos as $campo) {
    // 1. Campos de texto normales
    if (isset($_POST[$campo])) {
        $_SESSION['admin_data'][$section][$campo] = htmlspecialchars(trim($_POST[$campo]), ENT_QUOTES, 'UTF-8');
    }
    
    // 2. Archivos subidos (imágenes)
    $file_field = $campo . '_file';
    if (isset($_FILES[$file_field]) && $_FILES[$file_field]['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$file_field]['tmp_name'];
        $name     = basename($_FILES[$file_field]['name']);
        
        // Carpeta destino (frontend img/)
        $upload_dir = dirname(__DIR__) . '/img/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        // Renombrar archivo para evitar colisiones
        $new_name = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', $name);
        $dest     = $upload_dir . $new_name;
        
        if (move_uploaded_file($tmp_name, $dest)) {
            // Guardar la ruta relativa para el frontend
            $_SESSION['admin_data'][$section][$campo] = 'img/' . $new_name;
        }
    }
}

$_SESSION['flash_message'] = '✅ Sección "' . htmlspecialchars($schema[$section]['label'], ENT_QUOTES, 'UTF-8') . '" guardada temporalmente.';
$_SESSION['flash_type']    = 'success';

// Volver a la landing haciendo scroll a la sección y abriendo su panel
header('Location: ../index.php?panel_section=' . urlencode($section) . '#' . urlencode($section));
exit;
