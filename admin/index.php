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
// Calcular algunas métricas reales leyendo de $_SESSION
$total_equipo = isset($_SESSION['admin_data']['equipo']['items']) ? count($_SESSION['admin_data']['equipo']['items']) : 0;
$total_testimonios = isset($_SESSION['admin_data']['testimonios']['items']) ? count($_SESSION['admin_data']['testimonios']['items']) : 0;
$total_noticias = isset($_SESSION['admin_data']['noticias']['items']) ? count($_SESSION['admin_data']['noticias']['items']) : 0;
?>
<div class="dash-metrics">
    <div class="dash-metric-card">
        <div class="dash-metric-icon">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="dash-metric-info">
            <h4><?= $total_equipo ?></h4>
            <span>Miembros del Equipo</span>
        </div>
    </div>
    <div class="dash-metric-card">
        <div class="dash-metric-icon">
            <i class="bi bi-chat-quote-fill"></i>
        </div>
        <div class="dash-metric-info">
            <h4><?= $total_testimonios ?></h4>
            <span>Testimonios Activos</span>
        </div>
    </div>
    <div class="dash-metric-card">
        <div class="dash-metric-icon">
            <i class="bi bi-newspaper"></i>
        </div>
        <div class="dash-metric-info">
            <h4><?= $total_noticias ?></h4>
            <span>Noticias Publicadas</span>
        </div>
    </div>
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
                $url = ($item['type'] === 'collection') ? "coleccion.php?c={$key}" : "singleton.php?c={$key}";
                $icon = $item['icon'] ?? 'bi bi-file-earmark-text';
                
                // Determinar subtítulo
                if ($item['type'] === 'collection') {
                    // Contar items en sesión si existen, sino 0
                    if ($key === 'equipo') {
                        $count = 2; // Hardcoded mock to match previous fix
                    } elseif ($key === 'testimonios') {
                        $count = 2; // Hardcoded mock
                    } else {
                        $count = isset($_SESSION['admin_data'][$key]['items']) ? count($_SESSION['admin_data'][$key]['items']) : 0;
                    }
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
