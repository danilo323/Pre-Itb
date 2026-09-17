<?php
// admin/index.php
// Dashboard Principal

require_once __DIR__ . '/views/layout.php';
$schema = require __DIR__ . '/schema_mock.php';

$user = $_SESSION['user'] ?? 'Admin';

// Saludo según la hora
$hora = (int)date('H');
if ($hora < 12) {
    $saludo = 'Buenos días';
} elseif ($hora < 19) {
    $saludo = 'Buenas tardes';
} else {
    $saludo = 'Buenas noches';
}

echo layout_start('Dashboard');
?>

<div class="dash-header">
    <h1><?= $saludo ?>, <?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Bienvenido al panel administrativo de ITB. Aquí tienes un resumen de tu sitio.</p>
</div>

<!-- Métricas Rápidas -->
<?php
// Las cifras salen del esquema, no de una lista escrita a mano: una tarjeta
// por cada colección que exista de verdad.
//
// Antes eran tres llamadas fijas y dos de ellas contaban 'testimonios' y
// 'noticias', que NO son colecciones del esquema: collection_items() devolvía
// un array vacío, así que el panel enseñaba un 0 permanente y nadie podía
// arreglarlo desde el panel. Derivándolas, una colección nueva aparece sola y
// una que se retire deja de contarse.
require_once __DIR__ . '/../includes/content_helper.php';
require_once dirname(__DIR__) . '/includes/registros.php';

$metricas = [];
foreach ($schema['items'] as $m_key => $m_item) {
    if (($m_item['type'] ?? '') !== 'collection') continue;
    $metricas[] = [
        'n'     => count(collection_items($m_key)),
        'label' => $m_item['label'] ?? $m_key,
        'icon'  => $m_item['icon'] ?? 'bi bi-collection-fill',
    ];
}

// Los registros del formulario no son una colección del esquema, pero son la
// cifra que más se mira, así que va con las demás.
$metricas[] = [
    'n'     => count(registros_leer()),
    'label' => 'Registros del formulario',
    'icon'  => 'bi bi-inbox-fill',
];
?>
<div class="dash-metrics">
    <?php foreach ($metricas as $m): ?>
        <div class="dash-metric-card">
            <div class="dash-metric-icon">
                <i class="<?= htmlspecialchars($m['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
            </div>
            <div class="dash-metric-info">
                <h4><?= (int) $m['n'] ?></h4>
                <span><?= htmlspecialchars($m['label'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
$groups = $schema['groups'] ?? [];
$items_by_group = [];
foreach ($schema['items'] as $key => $item) {
    $g = $item['group'] ?? 'otros';
    $items_by_group[$g][$key] = $item;
}

foreach ($groups as $group_key => $group_label):
    if (empty($items_by_group[$group_key])) continue;
?>
    <div class="dash-section">
        <h2 class="dash-section-title"><?= htmlspecialchars($group_label, ENT_QUOTES, 'UTF-8') ?></h2>
        
        <div class="dash-grid">
            <?php foreach ($items_by_group[$group_key] as $key => $item): 
                // Igual que en el menu lateral: si el schema trae su propia
                // pagina ('url'), se usa esa.
                if (!empty($item['url'])) {
                    $url = ltrim($item['url'], '/');
                } else {
                    $url = ($item['type'] === 'collection') ? "coleccion.php?c={$key}" : "singleton.php?c={$key}";
                }
                $icon = $item['icon'] ?? 'bi bi-file-earmark-text';
                
                // Determinar subtítulo
                if (!empty($item['subtitulo'])) {
                    $subtitle = $item['subtitulo'];
                } elseif ($item['type'] === 'collection') {
                    $count = count(collection_items($key));
                    $subtitle = $count . ' item' . ($count !== 1 ? 's' : '');
                } else {
                    $subtitle = 'Editar contenido';
                }
            ?>
                <a href="<?= $url ?>" class="dash-card">
                    <div class="dash-card-icon">
                        <i class="<?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>"></i>
                    </div>
                    <div class="dash-card-info">
                        <h3><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <span><?= $subtitle ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<?php
echo layout_end();
?>
