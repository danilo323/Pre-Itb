<?php
// admin/singleton.php
// Renderiza páginas y singletons (Versión 2.0)
require_once __DIR__ . '/views/layout.php';
require_once __DIR__ . '/fields/_loader.php';

$schema = require __DIR__ . '/schema_mock.php';
$AB = admin_base();
$section_key = $_GET['c'] ?? '';

if (!isset($schema['items'][$section_key])) {
    header("Location: {$AB}/index.php");
    exit;
}

$config = $schema['items'][$section_key];

if ($config['type'] === 'collection') {
    header("Location: {$AB}/coleccion.php?c=" . urlencode($section_key));
    exit;
}

// Simulador de storage_get para la V2
// Lee los datos guardados en la sesión, si no existen usa los 'default' del schema
function get_saved_data($sec_key, $field_key, $default) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (isset($_SESSION['admin_data'][$sec_key][$field_key])) {
        return $_SESSION['admin_data'][$sec_key][$field_key];
    }
    return $default;
}

// Comienza el HTML
echo layout_start($config['label'], $section_key);
?>

<form method="POST" action="<?= $AB ?>/guardar.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="section" value="<?= htmlspecialchars($section_key, ENT_QUOTES, 'UTF-8') ?>">
    
    <?php if ($config['type'] === 'page' && !empty($config['sections'])): ?>
        
        <!-- MÓDULO DE ORDEN Y VISIBILIDAD -->
        <div class="order-block">
            <h3>ORDEN DE LAS SECCIONES DE LA PÁGINA</h3>
            <p>Desmarca "Visible" para ocultar una sección sin borrar su contenido. Se guarda al dar clic en Guardar cambios.</p>
            <div class="order-list">
                <?php 
                $i = 1;
                foreach ($config['sections'] as $sub_key => $sub_config): 
                    $is_visible = $_SESSION['admin_data'][$sub_key]['_visible'] ?? '1';
                ?>
                <div class="order-item">
                    <div class="order-item-left">
                        <span class="order-number"><?= $i++ ?></span>
                        <span class="order-name"><?= htmlspecialchars($sub_config['label']) ?></span>
                    </div>
                    <label class="bool-toggle-label">
                        <input type="hidden" name="<?= $sub_key ?>___visible" value="0">
                        <input type="checkbox" name="<?= $sub_key ?>___visible" value="1" class="bool-toggle-input" <?= $is_visible ? 'checked' : '' ?>>
                        <span class="bool-toggle-switch"></span>
                        <span class="bool-toggle-text">Visible</span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ACORDEONES (SUBSECCIONES) -->
        <?php foreach ($config['sections'] as $sub_key => $sub_config): ?>
            <details class="admin-accordion">
                <summary><?= htmlspecialchars($sub_config['label']) ?></summary>
                <div class="accordion-content">
                    <?php 
                    foreach ($sub_config['fields'] as $fk => $fc) {
                        $val = get_saved_data($sub_key, $fk, $fc['default'] ?? '');
                        // Formato: hero__titulo
                        $input_name = "{$sub_key}__{$fk}";
                        echo field_render($input_name, $val, $fc);
                    }
                    ?>
                </div>
            </details>
        <?php endforeach; ?>

    <?php else: ?>
        <!-- SINGLETON PLANO (Ej: Ajustes, Footer) -->
        <div class="singleton-panel">
            <?php 
            foreach ($config['fields'] as $fk => $fc) {
                $val = get_saved_data($section_key, $fk, $fc['default'] ?? '');
                // Formato: footer__logo_blanco
                $input_name = "{$section_key}__{$fk}";
                echo field_render($input_name, $val, $fc);
            }
            ?>
        </div>
    <?php endif; ?>
    
    <div class="form-actions">
        <a href="<?= $AB ?>/index.php" class="btn btn-outline">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill"></i> Guardar cambios</button>
    </div>
</form>

<?php
echo layout_end();
?>
