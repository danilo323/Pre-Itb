<?php
// admin/guardar.php
// Guarda los cambios de singletons y páginas en persistencia permanente (MySQL / JSON) y en $_SESSION

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$section_key = $_POST['section'] ?? '';
$schema = require __DIR__ . '/schema_mock.php';
require_once __DIR__ . '/fields/_loader.php';
require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/../includes/content_helper.php';

if (!isset($schema['items'][$section_key])) {
    $_SESSION['flash_message'] = 'Sección no válida.';
    $_SESSION['flash_type']    = 'error';
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['admin_data'])) {
    $_SESSION['admin_data'] = [];
}

// 1. Normalizar $_FILES para soportar repeaters y subidas anidadas
function normalize_files_array($files) {
    $out = [];
    foreach ($files as $top_key => $f) {
        if (!is_array($f['name'])) {
            $out[$top_key] = $f;
            continue;
        }
        $keys = ['name', 'type', 'tmp_name', 'error', 'size'];
        $walker = function($data, $path) use (&$walker, &$out, $keys, $top_key, $f) {
            foreach ($data as $k => $v) {
                $cur = $path . '[' . $k . ']';
                if (is_array($v)) {
                    $walker($v, $cur);
                } else {
                    $item = [];
                    foreach ($keys as $prop) {
                        $val = $f[$prop];
                        preg_match_all('/\[(.*?)\]/', $cur, $m);
                        foreach ($m[1] as $pk) { $val = $val[$pk]; }
                        $item[$prop] = $val;
                    }
                    $out[$top_key . $cur] = $item;
                }
            }
        };
        $walker($f['name'], '');
    }
    return $out;
}
$_FILES = normalize_files_array($_FILES);

$config = $schema['items'][$section_key];

// 2. Procesar visibilidad (para páginas con secciones)
if ($config['type'] === 'page' && !empty($config['sections'])) {
    foreach ($config['sections'] as $sub_key => $sub_config) {
        if (isset($_POST[$sub_key . '___visible'])) {
            $vis = (string)$_POST[$sub_key . '___visible'];
            $_SESSION['admin_data'][$sub_key]['_visible'] = $vis;
            storage_set($sub_key, '_visible', $vis);
        }
    }
}

// 3. Procesar campos usando field_parse() y persistir en storage
if ($config['type'] === 'page' && !empty($config['sections'])) {
    // Es un page con acordeones (ej: Inicio con Hero, Trayectoria, etc.)
    foreach ($config['sections'] as $sub_key => $sub_config) {
        if (!isset($_SESSION['admin_data'][$sub_key])) {
            $_SESSION['admin_data'][$sub_key] = [];
        }
        foreach ($sub_config['fields'] as $fk => $fc) {
            $raw_value = $_POST[$sub_key . '__' . $fk] ?? null;
            $fc['name_path'] = $sub_key . '__' . $fk;
            $old_val = $_SESSION['admin_data'][$sub_key][$fk] ?? storage_get_raw($sub_key, $fk, $fc['default'] ?? '');
            $fc['_old_value'] = $old_val;

            $parsed_value = field_parse($fc['type'], $raw_value, $fc);
            $_SESSION['admin_data'][$sub_key][$fk] = $parsed_value;

            // Guardar permanentemente en almacenamiento
            $val_to_save = is_array($parsed_value) 
                ? json_encode($parsed_value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) 
                : (string)$parsed_value;
            storage_set($sub_key, $fk, $val_to_save);
        }
    }
} else {
    // Es un singleton plano (ej: Ajustes, Footer)
    if (!isset($_SESSION['admin_data'][$section_key])) {
        $_SESSION['admin_data'][$section_key] = [];
    }
    foreach ($config['fields'] as $fk => $fc) {
        $raw_value = $_POST[$section_key . '__' . $fk] ?? null;
        $fc['name_path'] = $section_key . '__' . $fk;
        $old_val = $_SESSION['admin_data'][$section_key][$fk] ?? storage_get_raw($section_key, $fk, $fc['default'] ?? '');
        $fc['_old_value'] = $old_val;

        $parsed_value = field_parse($fc['type'], $raw_value, $fc);
        $_SESSION['admin_data'][$section_key][$fk] = $parsed_value;

        // Guardar permanentemente en almacenamiento
        $val_to_save = is_array($parsed_value) 
            ? json_encode($parsed_value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) 
            : (string)$parsed_value;
        storage_set($section_key, $fk, $val_to_save);
    }
}

storage_clear_cache();

$_SESSION['flash_message'] = 'Cambios guardados correctamente.';
$_SESSION['flash_type']    = 'success';

// Volver al singleton
header('Location: singleton.php?c=' . urlencode($section_key));
exit;
