<?php
// admin/editar.php
// Motor genérico para editar o crear un registro individual de una colección

require_once __DIR__ . '/views/layout.php';
require_once __DIR__ . '/fields/_loader.php';

$schema = require __DIR__ . '/schema_mock.php';

$section = $_GET['c'] ?? 'testimonios';
$id = $_GET['id'] ?? 'new';

// Whitelist del schema
if (!isset($schema['items'][$section]) || $schema['items'][$section]['type'] !== 'collection') {
    die("Colección no encontrada.");
}

$config = $schema['items'][$section];
$is_new = ($id === 'new');

// Leer sesión (vía helper)
require_once __DIR__ . '/../includes/content_helper.php';
$items = collection_items($section);

// Manejar POST (Crear o Actualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'delete')) {
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
        $data_to_save['id'] = (count($items) > 0 ? max(array_column($items, 'id')) + 1 : 1);
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

    // 3. Guardar en memoria
    if ($is_new) {
        $_SESSION['admin_data'][$section]['items'][] = $data_to_save;
    } else {
        foreach ($_SESSION['admin_data'][$section]['items'] as $idx => $itm) {
            if ($itm['id'] === (int)$id) {
                $_SESSION['admin_data'][$section]['items'][$idx] = $data_to_save;
                break;
            }
        }
    }

    flash_set($is_new ? "Registro creado exitosamente (Memoria)" : "Registro actualizado exitosamente (Memoria)");
    header("Location: coleccion.php?c=" . urlencode($section));
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
        <a href="coleccion.php?c=<?= urlencode($section) ?>" class="btn btn-secondary">← Volver al Listado</a>
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
            <a href="coleccion.php?c=<?= urlencode($section) ?>" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>

<?php
echo layout_end();
?>
