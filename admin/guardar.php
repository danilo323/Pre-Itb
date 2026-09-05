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

// Snapshot de datos actuales en MySQL antes de aplicar los cambios
$currentSnapshot = storage_load_all();

// Acumular todos los cambios en un batch para guardar en una sola transacción
$batch = []; // [[$section, $field, $value], ...]

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

    // Si el usuario vació deliberadamente un campo de imagen que antes tenía un archivo subido
    $prevValue = $currentSnapshot[$sub_key][$field] ?? '';
    if ($val === '' && !empty($prevValue) && (str_starts_with($prevValue, 'img/') || str_starts_with($prevValue, 'uploads/'))) {
        storage_delete_old_file($prevValue);
    }

    $_SESSION['admin_data'][$sub_key][$field] = $cleanVal;
    $batch[] = [$sub_key, $field, is_string($val) ? $val : json_encode($val)];
}

// 2. Procesar imágenes / archivos enviados con eliminación automática de archivo anterior
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif', 'bmp', 'tiff', 'tif', 'ico', 'heic', 'heif'];

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
            // Imagen previa en MySQL (tomada del snapshot inicial para máxima seguridad)
            $oldImage = $currentSnapshot[$sub_key][$field] ?? storage_get($sub_key, $field, '');

            $tmp_name = $file_info['tmp_name'];
            $name     = basename($file_info['name']);
            $ext      = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_ext)) {
                continue; // Saltar archivos con extensiones no permitidas
            }

            $upload_dir = dirname(__DIR__) . '/img/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $cleanBase = preg_replace('/[^a-zA-Z0-9_\-]/', '', pathinfo($name, PATHINFO_FILENAME));
            if (empty($cleanBase)) $cleanBase = 'img';
            $new_name = time() . '_' . $cleanBase . '.' . $ext;
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

                // Reemplazar o añadir en el batch (imagen prevalece sobre texto vacío)
                $found = false;
                foreach ($batch as &$entry) {
                    if ($entry[0] === $sub_key && $entry[1] === $field) {
                        $entry[2] = $newRelPath;
                        $found = true;
                        break;
                    }
                }
                unset($entry);
                if (!$found) {
                    $batch[] = [$sub_key, $field, $newRelPath];
                }
            }
        }
    }
}

// 3. Persistir todo el batch en UNA sola transacción (mucho más rápido)
if (!empty($batch)) {
    try {
        $pdo = db_connect();
        $pdo->beginTransaction();

        $sql = "INSERT INTO `site_content` (`section`, `field_key`, `field_value`)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE `field_value` = VALUES(`field_value`), `updated_at` = CURRENT_TIMESTAMP";
        $stmt = $pdo->prepare($sql);

        foreach ($batch as [$sec, $fld, $val]) {
            $stmt->execute([$sec, $fld, $val]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error en batch guardar.php: " . $e->getMessage());
    }

    // Refrescar caché en memoria para que la landing vea los cambios al instante
    storage_clear_cache();
}

$_SESSION['flash_message'] = 'Cambios guardados correctamente.';
$_SESSION['flash_type']    = 'success';

// Volver al singleton
header('Location: singleton.php?c=' . urlencode($section_key));
exit;
