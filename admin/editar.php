<?php
// admin/editar.php
// Motor genérico para editar o crear un registro individual de una colección

require_once __DIR__ . '/views/layout.php';
require_once __DIR__ . '/fields/_loader.php';

$schema = require __DIR__ . '/schema_mock.php';

$section = $_GET['c'] ?? 'testimonios';
$id = $_GET['id'] ?? 'new';

// Whitelist del schema
if (!isset($schema[$section]) || $schema[$section]['type'] !== 'collection') {
    die("Colección no encontrada.");
}

$config = $schema[$section];
$is_new = ($id === 'new');

// Manejar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_to_save = [];
    foreach ($config['fields'] as $key => $field_config) {
        $raw_value = $_POST[$key] ?? null;
        $data_to_save[$key] = field_parse($field_config['type'], $raw_value, $field_config);
    }

    flash_set($is_new ? "Registro creado exitosamente (Simulado)" : "Registro actualizado exitosamente (Simulado)");
    header("Location: coleccion.php?c=" . urlencode($section));
    exit;
}

// Datos de muestra según ID
$current_data = [];
if (!$is_new) {
    $current_data = [
        'nombre' => 'Carlos Mendoza',
        'carrera' => 'Tecnología en Desarrollo de Software',
        'testimonio' => 'El ITB me dio las herramientas prácticas para conseguir trabajo en el primer año.'
    ];
}

$title_label = $is_new ? "Crear Nuevo " . $config['label'] : "Editar " . $config['label'] . " #" . htmlspecialchars($id);

echo layout_start($title_label);
?>

<div class="form-container">
    <div class="form-header-bar">
        <a href="coleccion.php?c=<?= urlencode($section) ?>" class="btn btn-secondary">← Volver al Listado</a>
    </div>

    <form method="POST" action="" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <?php
        foreach ($config['fields'] as $key => $field_config) {
            $value = $current_data[$key] ?? null;
            echo field_render($key, $value, $field_config);
        }
        ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 <?= $is_new ? 'Crear Registro' : 'Guardar Cambios' ?></button>
            <a href="coleccion.php?c=<?= urlencode($section) ?>" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>

<?php
echo layout_end();
?>
