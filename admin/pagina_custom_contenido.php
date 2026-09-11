<?php
require_once __DIR__ . '/views/layout.php';
require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/fields/_loader.php';
require_once __DIR__ . '/pagina_custom_helpers.php';

$ab = admin_base();
$id = trim($_GET['id'] ?? $_POST['id'] ?? '');
$data = storage_load();
$page = $data['_paginas_creadas'][$id] ?? null;
if (!$page) { header("Location: {$ab}/index.php"); exit; }

function pagina_custom_normalize_files(array $files): array {
    $out = [];
    foreach ($files as $top => $file) {
        if (!is_array($file['name'])) { $out[$top] = $file; continue; }
        $walk = function ($names, string $path = '') use (&$walk, &$out, $top, $file) {
            foreach ($names as $key => $name) {
                $current = $path . '[' . $key . ']';
                if (is_array($name)) { $walk($name, $current); continue; }
                $item = [];
                foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $property) {
                    $value = $file[$property];
                    preg_match_all('/\[(.*?)\]/', $current, $matches);
                    foreach ($matches[1] as $part) $value = $value[$part];
                    $item[$property] = $value;
                }
                $out[$top . $current] = $item;
            }
        };
        $walk($file['name']);
    }
    return $out;
}

$configs = pagina_custom_section_configs($page['secciones'] ?? []);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $_FILES = pagina_custom_normalize_files($_FILES);
    $content = $page['contenido'] ?? pagina_custom_snapshot($data, $page['secciones'] ?? []);
    foreach ($configs as $section => $config) {
        foreach (($config['fields'] ?? []) as $field => $field_config) {
            if (in_array($field_config['type'] ?? '', ['divider', 'alert'], true)) continue;
            $name = "contenido[{$section}][{$field}]";
            $field_config['name_path'] = $name;
            $field_config['_old_value'] = $content[$section][$field] ?? ($field_config['default'] ?? '');
            $content[$section][$field] = field_parse($field_config['type'] ?? 'text', $_POST['contenido'][$section][$field] ?? null, $field_config);
        }
        $content[$section]['_visible'] = '1';
    }
    $data['_paginas_creadas'][$id]['contenido'] = $content;
    storage_save($data);
    flash_set('Contenido de la página guardado.', 'success');
    header("Location: {$ab}/pagina_custom_contenido.php?id=" . urlencode($id));
    exit;
}

$content = $page['contenido'] ?? pagina_custom_snapshot($data, $page['secciones'] ?? []);
echo layout_start('Contenido: ' . ($page['nombre'] ?? 'Página'), 'custom_' . $id);
?>
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div><h1 style="margin:0;color:#1A3B70;">Contenido de <?= htmlspecialchars($page['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1><p style="margin:6px 0 0;color:#6B7280;">Cada sección conserva una copia independiente de sus textos e imágenes.</p></div>
        <a class="btn btn-outline" href="<?= $ab ?>/pagina_custom.php?id=<?= urlencode($id) ?>">Configuración</a>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>"><input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
        <?php foreach ($configs as $section => $config): ?>
            <details class="admin-accordion"><summary><?= htmlspecialchars($config['label'] ?? $section, ENT_QUOTES, 'UTF-8') ?></summary><div class="accordion-content">
                <?php foreach (($config['fields'] ?? []) as $field => $field_config):
                    $value = $content[$section][$field] ?? ($field_config['default'] ?? '');
                    echo field_render("contenido[{$section}][{$field}]", $value, $field_config);
                endforeach; ?>
            </div></details>
        <?php endforeach; ?>
        <div class="form-actions"><a href="<?= $ab ?>/pagina_custom.php?id=<?= urlencode($id) ?>" class="btn btn-outline">Cancelar</a><button class="btn btn-primary" type="submit"><i class="bi bi-floppy-fill"></i> Guardar contenido</button></div>
    </form>
</div>
<?php echo layout_end(); ?>
