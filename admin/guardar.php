<?php
// admin/guardar.php
// Guarda permanentemente en MySQL (textos e imágenes) y gestiona el ciclo de vida de archivos

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/storage.php';

auth_session_start();
auth_require();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Verificación CSRF
if (!csrf_verify()) {
    $_SESSION['flash_message'] = 'Token de seguridad inválido o expirado.';
    $_SESSION['flash_type']    = 'error';
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
    if ($post_key === 'section' || $post_key === 'csrf_token') continue;

    if (strpos($post_key, '__') !== false) {
        list($sub_key, $field) = explode('__', $post_key, 2);
    } else {
        $sub_key = $section_key;
        $field   = $post_key;
    }

    if (!isset($_SESSION['admin_data'][$sub_key])) {
        $_SESSION['admin_data'][$sub_key] = [];
    }

    $val = is_string($raw_value) ? trim($raw_value) : $raw_value;
    $cleanVal = is_string($val) ? htmlspecialchars($val, ENT_QUOTES, 'UTF-8') : $val;

    $_SESSION['admin_data'][$sub_key][$field] = $cleanVal;
    storage_set($sub_key, $field, is_string($val) ? $val : json_encode($val));
}

// 2. Procesar imágenes / archivos enviados con eliminación automática de archivo anterior
foreach ($_FILES as $file_key => $file_info) {
    if (str_ends_with($file_key, '_file')) {
        $base_key = substr($file_key, 0, -5); // quitar '_file'

        if (strpos($base_key, '__') !== false) {
            list($sub_key, $field) = explode('__', $base_key, 2);
        } else {
            $sub_key = $section_key;
            $field   = $base_key;
        }

        if ($file_info['error'] === UPLOAD_ERR_OK) {
            // Consultar la imagen previa en MySQL
            $oldImage = storage_get($sub_key, $field, '');

            $tmp_name = $file_info['tmp_name'];
            $name     = basename($file_info['name']);

            $upload_dir = dirname(__DIR__) . '/img/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $new_name = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', $name);
            $dest     = $upload_dir . $new_name;

            if (move_uploaded_file($tmp_name, $dest)) {
                $newRelPath = 'img/' . $new_name;

                // Eliminar archivo físico anterior si fue subido por el panel
                if (!empty($oldImage) && $oldImage !== $newRelPath) {
                    storage_delete_old_file($oldImage);
                }

                if (!isset($_SESSION['admin_data'][$sub_key])) {
                    $_SESSION['admin_data'][$sub_key] = [];
                }
                $_SESSION['admin_data'][$sub_key][$field] = $newRelPath;
                storage_set($sub_key, $field, $newRelPath);
            }
        }
    }
}

$_SESSION['flash_message'] = '✅ Cambios guardados con éxito en la base de datos.';
$_SESSION['flash_type']    = 'success';

// Volver al singleton
header('Location: singleton.php?c=' . urlencode($section_key));
exit;
