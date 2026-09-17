<?php
// admin/editar.php
// Motor genérico para editar o crear un registro individual de una colección

require_once __DIR__ . '/views/layout.php';
require_once __DIR__ . '/fields/_loader.php';

$schema = require __DIR__ . '/schema_mock.php';
$AB = admin_base();

// Igual que en coleccion.php: sin ?c= no hay nada que editar. El antiguo
// valor por defecto, 'testimonios', ya no existe en el esquema.
$section = trim((string) ($_GET['c'] ?? ''));
$id = $_GET['id'] ?? 'new';

// Whitelist del schema
if (!isset($schema['items'][$section]) || $schema['items'][$section]['type'] !== 'collection') {
    die("Colección no encontrada.");
}

$config = $schema['items'][$section];
$is_new = ($id === 'new');

// Cargar datos en sesión (siempre sincronizado desde BD/JSON)
require_once __DIR__ . '/../includes/content_helper.php';
content_ensure_session_loaded();
$items = collection_items($section);

// Manejar POST (Crear o Actualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'delete')) {
    csrf_check();

    // 0. Normalizar $_FILES para soportar nombres como foto[file] generados por el admin
    if (!function_exists('normalize_files_array')) {
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
    }
    $_FILES = normalize_files_array($_FILES);

    $data_to_save = [];
    
    // 0. Preservar campos internos (como el orden) si es edición
    if (!$is_new) {
        foreach ($items as $itm) {
            if ($itm['id'] === (int)$id) {
                $data_to_save = $itm;
                break;
            }
        }
    } else {
        require_once __DIR__ . '/storage.php';
        // Asegurar que la estructura de la colección exista en sesión
        if (!isset($_SESSION['admin_data'][$section])) {
            $_SESSION['admin_data'][$section] = ['items' => []];
        }
        if (!isset($_SESSION['admin_data'][$section]['items'])) {
            $_SESSION['admin_data'][$section]['items'] = [];
        }
        $data_to_save['id'] = storage_next_id($section, $_SESSION['admin_data']);
    }
    
    // 1. Procesar todos los campos genéricamente
    foreach ($config['fields'] as $key => $field_config) {
        $raw_value = $_POST[$key] ?? null;
        
        // Inyectar contexto al field_config para que el parser sepa de qué campo se trata y su valor anterior
        $field_config['name_path'] = $key;
        
        $old_val = '';
        if (!$is_new) {
            foreach ($items as $itm) {
                if ($itm['id'] === (int)$id) {
                    $old_val = $itm[$key] ?? '';
                    break;
                }
            }
        }
        $field_config['_old_value'] = $old_val;
        
        $data_to_save[$key] = field_parse($field_config['type'], $raw_value, $field_config);
    }

    // 3. Guardar en memoria (siempre asegurar que la estructura exista)
    if (!isset($_SESSION['admin_data'][$section])) {
        $_SESSION['admin_data'][$section] = ['items' => []];
    }
    if (!isset($_SESSION['admin_data'][$section]['items'])) {
        $_SESSION['admin_data'][$section]['items'] = [];
    }

    if ($is_new) {
        $_SESSION['admin_data'][$section]['items'][] = $data_to_save;
    } else {
        $found = false;
        foreach ($_SESSION['admin_data'][$section]['items'] as $idx => $itm) {
            if ((int)($itm['id'] ?? 0) === (int)$id) {
                $_SESSION['admin_data'][$section]['items'][$idx] = $data_to_save;
                $found = true;
                break;
            }
        }
        // Si no estaba en sesión (sesión expirada), agregarlo
        if (!$found) {
            $_SESSION['admin_data'][$section]['items'][] = $data_to_save;
        }
    }

    // Guardar permanentemente en disco (data/content.json)
    content_storage_save($_SESSION['admin_data']);

    flash_set($is_new ? "Registro creado exitosamente" : "Registro actualizado exitosamente");
    header("Location: {$AB}/coleccion.php?c=" . urlencode($section));
    exit;
}

// Datos actuales
$current_data = [];
if (!$is_new) {
    foreach ($items as $item) {
        if ($item['id'] === (int)$id) {
            $current_data = $item;
            break;
        }
    }
}

$title_label = $is_new ? "Crear Nuevo " . $config['label'] : "Editar " . $config['label'] . " #" . htmlspecialchars($id);

echo layout_start($title_label);
?>

<div class="form-container">
    <div class="form-header-bar">
        <a href="<?= $AB ?>/coleccion.php?c=<?= urlencode($section) ?>" class="btn btn-secondary">← Volver al Listado</a>
    </div>

    <form method="POST" action="" class="admin-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <?php
        foreach ($config['fields'] as $key => $field_config) {
            $value = $current_data[$key] ?? null;
            echo field_render($key, $value, $field_config);
        }
        ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill"></i> <?= $is_new ? 'Crear Registro' : 'Guardar Cambios' ?></button>
            <a href="<?= $AB ?>/coleccion.php?c=<?= urlencode($section) ?>" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>

<?php
echo layout_end();
?>
