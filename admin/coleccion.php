<?php
// admin/coleccion.php
// Motor genérico para listar registros de una colección (ej. Equipo, Testimonios)

require_once __DIR__ . '/views/layout.php';
require_once __DIR__ . '/storage.php';

$schema  = require __DIR__ . '/schema_mock.php';
$section = $_GET['c'] ?? 'testimonios';

// Validar Whitelist del schema
if (!isset($schema['items'][$section]) || $schema['items'][$section]['type'] !== 'collection') {
    header("Location: index.php");
    exit;
}

$config = $schema['items'][$section];

// ── Función auxiliar: leer items SIEMPRE desde MySQL (fuente de verdad) ─────
function coleccion_load_items(string $section): array {
    $raw = storage_get($section, 'items', '');
    if ($raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded) && !empty($decoded)) {
            return array_values($decoded);      // índices 0-based limpios
        }
    }
    // Default hardcoded solo para equipo
    if ($section === 'equipo') {
        return [
            ['id'=>1,'orden'=>'1','nombre'=>'Roberto Tolozano Benites','nombre_completo'=>'PhD. Roberto Tolozano Benites','cargo'=>'Canciller','linkedin'=>'#','email'=>'#','foto'=>'img/autoridad_1.png','mostrar_en_home'=>true,'publicado'=>true],
            ['id'=>2,'orden'=>'2','nombre'=>'Elena Tolozano Benites','nombre_completo'=>'PhD. Elena Tolozano Benites','cargo'=>'Rectora','linkedin'=>'#','email'=>'#','foto'=>'img/autoridad_2.png','mostrar_en_home'=>true,'publicado'=>true],
            ['id'=>3,'orden'=>'3','nombre'=>'Luis Alzate Peralta','nombre_completo'=>'PhD. Luis Alzate Peralta','cargo'=>'Vicerrector Académico y de Investigación','linkedin'=>'#','email'=>'#','foto'=>'img/autoridad_3.png','mostrar_en_home'=>true,'publicado'=>true],
            ['id'=>4,'orden'=>'4','nombre'=>'Michelle Tolozano Lapierre','nombre_completo'=>'PhD. Michelle Tolozano Lapierre','cargo'=>'Vicerrectora de Extensión y Gestión Administrativa','linkedin'=>'#','email'=>'#','foto'=>'img/autoridad_4.png','mostrar_en_home'=>true,'publicado'=>true],
        ];
    }
    return [];
}

$items       = coleccion_load_items($section);
$is_sortable = !empty($config['sortable']);

// Ordenar por campo 'orden'
if ($is_sortable) {
    usort($items, function($a, $b) {
        return (int)($a['orden'] ?? 999) <=> (int)($b['orden'] ?? 999);
    });
}

// ── Manejar POST: guardar nuevo orden ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_order') {
    $order_ids = json_decode($_POST['order_data'] ?? '[]', true);
    if (is_array($order_ids) && !empty($order_ids)) {
        // Reasignar 'orden' según la nueva secuencia recibida
        $map = [];
        foreach ($items as $itm) { $map[$itm['id']] = $itm; }
        $reordered = [];
        foreach ($order_ids as $newPos => $id) {
            $id = (int)$id;
            if (isset($map[$id])) {
                $map[$id]['orden'] = (string)($newPos + 1);
                $reordered[] = $map[$id];
            }
        }
        storage_set($section, 'items', json_encode($reordered, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        storage_clear_cache();
        flash_set("Orden guardado correctamente");
    }
    header("Location: coleccion.php?c=" . urlencode($section));
    exit;
}

// ── Manejar POST: eliminar registro ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $del_id = (int)($_POST['id'] ?? 0);
    $new_items = array_values(array_filter($items, fn($i) => (int)$i['id'] !== $del_id));
    storage_set($section, 'items', json_encode($new_items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    storage_clear_cache();
    flash_set("Registro eliminado correctamente");
    header("Location: coleccion.php?c=" . urlencode($section));
    exit;
}

// ── Mapa de tipos de campo para renderizar correctamente en la tabla ─────────
$field_types = [];
foreach ($config['fields'] ?? [] as $fk => $fc) {
    $field_types[$fk] = $fc['type'] ?? 'text';
}
$columnas = $config['columns'] ?? array_keys($config['fields'] ?? []);

echo layout_start("Gestión de " . $config['label'], $section);
?>

<div class="collection-header">
    <div class="search-box">
        <input type="text" id="search-table" placeholder="Buscar en la lista..." class="form-input">
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="/" target="_blank" class="btn btn-outline" style="display:flex;align-items:center;gap:6px;">
            <i class="bi bi-eye-fill"></i> Previsualizar sitio
        </a>
        <a href="editar.php?c=<?= urlencode($section) ?>&id=new" class="btn btn-primary">+ Nuevo Registro</a>
    </div>
</div>

<div class="table-container">
    <table class="admin-table" id="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <?php if ($is_sortable): ?>
                    <th>Orden</th>
                <?php endif; ?>
                <?php foreach ($columnas as $col):
                    $col_label = $config['fields'][$col]['label'] ?? ucfirst(str_replace('_', ' ', $col));
                ?>
                    <th><?= htmlspecialchars($col_label, ENT_QUOTES, 'UTF-8') ?></th>
                <?php endforeach; ?>
                <th style="text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $idx => $item): ?>
                <tr data-id="<?= (int)$item['id'] ?>">
                    <td>#<?= (int)$item['id'] ?></td>
                    <?php if ($is_sortable): ?>
                        <td>
                            <div class="actions-cell">
                                <button type="button" class="btn btn-sm btn-outline js-move-up" <?= ($idx === 0) ? 'disabled style="opacity:0.3;"' : '' ?>>
                                    <i class="bi bi-arrow-up"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline js-move-down" <?= ($idx === count($items) - 1) ? 'disabled style="opacity:0.3;"' : '' ?>>
                                    <i class="bi bi-arrow-down"></i>
                                </button>
                            </div>
                        </td>
                    <?php endif; ?>
                    <?php foreach ($columnas as $col):
                        $col_type  = $field_types[$col] ?? 'text';
                        $col_value = $item[$col] ?? '';
                    ?>
                        <td>
                        <?php if ($col_type === 'image' && !empty($col_value)):
                            $thumb_src = $col_value;
                            if (strpos($thumb_src, 'http') !== 0 && strpos($thumb_src, '/') !== 0) {
                                $thumb_src = '/' . $thumb_src;
                            }
                        ?>
                            <img src="<?= htmlspecialchars($thumb_src, ENT_QUOTES, 'UTF-8') ?>" alt="foto"
                                 style="width:52px;height:52px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;display:block;">
                        <?php elseif ($col_type === 'bool'): ?>
                            <?php if ($col_value === '1' || $col_value === true || $col_value === 1): ?>
                                <span style="color:#10b981;font-weight:600;">Sí</span>
                            <?php else: ?>
                                <span style="color:#94a3b8;">No</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?= htmlspecialchars((string)$col_value, ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td style="text-align: right;">
                        <div class="actions-cell" style="justify-content: flex-end;">
                            <a href="editar.php?c=<?= urlencode($section) ?>&id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-secondary">
                                <i class="bi bi-pencil-fill"></i> Editar
                            </a>
                            <form method="POST" action="" style="display:inline-block;margin:0;" class="form-delete-record">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <button type="button" class="btn btn-sm btn-danger js-delete-btn">
                                    <i class="bi bi-trash-fill"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($is_sortable): ?>
<form id="save-order-form" method="POST" action="" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="action" value="save_order">
    <input type="hidden" name="order_data" id="order-data-input" value="">
</form>

<div id="order-actions" style="display:none;margin-top:24px;padding:16px;background:#fffbe2;border:1px solid #fde68a;border-radius:8px;">
    <p style="margin-bottom:12px;font-weight:500;color:#b45309;">
        <i class="bi bi-info-circle-fill"></i> Has modificado el orden. No olvides guardar.
    </p>
    <button type="button" class="btn btn-primary" onclick="submitOrder()">
        <i class="bi bi-floppy-fill"></i> Guardar Orden
    </button>
    <button type="button" class="btn btn-outline" onclick="location.reload()">Cancelar</button>
</div>

<script>
document.querySelectorAll('.js-move-up').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        if (row.previousElementSibling) {
            row.parentNode.insertBefore(row, row.previousElementSibling);
            showOrderActions();
        }
    });
});
document.querySelectorAll('.js-move-down').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        if (row.nextElementSibling) {
            row.parentNode.insertBefore(row.nextElementSibling, row);
            showOrderActions();
        }
    });
});
function showOrderActions() {
    document.getElementById('order-actions').style.display = 'block';
}
function submitOrder() {
    const ids = Array.from(document.querySelectorAll('tbody tr[data-id]')).map(tr => tr.getAttribute('data-id'));
    document.getElementById('order-data-input').value = JSON.stringify(ids);
    document.getElementById('save-order-form').submit();
}
document.querySelectorAll('.js-delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('¿Estás seguro de eliminar este registro?')) {
            this.closest('form').submit();
        }
    });
});
// Búsqueda en tabla
document.getElementById('search-table').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#data-table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
<?php endif; ?>

<?php
echo layout_end();
?>
