<?php
// admin/coleccion.php
// Motor genérico para listar registros de una colección (ej. Testimonios, Noticias)

require_once __DIR__ . '/views/layout.php';
$schema = require __DIR__ . '/schema_mock.php';

$section = $_GET['c'] ?? 'testimonios';

// Validar Whitelist del schema (V2)
if (!isset($schema['items'][$section]) || $schema['items'][$section]['type'] !== 'collection') {
    header("Location: index.php");
    exit;
}

$config = $schema['items'][$section];

// Leer de la sesión (vía helper)
require_once __DIR__ . '/../includes/content_helper.php';
$items = collection_items($section);

$is_sortable = !empty($config['sortable']);

// Si es sortable, garantizar que todos tengan 'orden' y ordenar el array
if ($is_sortable) {
    foreach ($items as $idx => &$itm) {
        if (!isset($itm['orden'])) {
            $itm['orden'] = (string)($idx + 1);
        }
    }
    unset($itm);

    usort($items, function($a, $b) {
        $oa = (int)($a['orden'] ?? 999);
        $ob = (int)($b['orden'] ?? 999);
        return $oa <=> $ob;
    });
}

// Manejar Guardado de Orden (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_order') {
    $order_data = json_decode($_POST['order_data'] ?? '[]', true);
    
    if (is_array($order_data) && !empty($order_data)) {
        foreach ($order_data as $index => $id) {
            $id = (int)$id;
            foreach ($_SESSION['admin_data'][$section]['items'] as &$sess_item) {
                if ($sess_item['id'] === $id) {
                    $sess_item['orden'] = (string)($index + 1);
                    break;
                }
            }
            unset($sess_item);
        }
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['flash_message'] = "Orden guardado exitosamente";
            $_SESSION['flash_type'] = "success";
        }
    }
    
    header("Location: coleccion.php?c=" . urlencode($section));
    exit;
}

// Manejar eliminación (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_to_delete = (int)$_POST['id'];
    // Buscar el índice del id
    foreach ($items as $idx => $item) {
        if ($item['id'] === $id_to_delete) {
            unset($_SESSION['admin_data'][$section]['items'][$idx]);
            // Reindexar arreglo para mantener orden limpio
            $_SESSION['admin_data'][$section]['items'] = array_values($_SESSION['admin_data'][$section]['items']);
            
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['flash_message'] = "Registro eliminado correctamente (Memoria)";
                $_SESSION['flash_type'] = "success";
            }
            break;
        }
    }
    header("Location: coleccion.php?c=" . urlencode($section));
    exit;
}

$title_label = "Gestión de " . $config['label'];
echo layout_start($title_label);
?>

<div class="collection-header">
    <div class="search-box">
        <input type="text" id="search-table" placeholder="🔍 Buscar en la lista..." class="form-input">
    </div>
    <a href="editar.php?c=<?= urlencode($section) ?>&id=new" class="btn btn-primary">+ Nuevo Registro</a>
</div>

<div class="table-container">
    <table class="admin-table" id="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <?php if ($is_sortable): ?>
                    <th>Orden</th>
                <?php endif; ?>
                <?php 
                $columnas = $config['columns'] ?? array_keys($config['fields'] ?? []);
                foreach ($columnas as $col): 
                ?>
                    <th><?= htmlspecialchars(ucfirst($col), ENT_QUOTES, 'UTF-8') ?></th>
                <?php endforeach; ?>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $idx => $item): ?>
                <tr data-id="<?= $item['id'] ?>">
                    <td>#<?= $item['id'] ?></td>
                    <?php if ($is_sortable): ?>
                        <td class="actions-cell">
                            <button type="button" class="btn btn-sm btn-outline js-move-up"><i class="bi bi-arrow-up"></i></button>
                            <button type="button" class="btn btn-sm btn-outline js-move-down"><i class="bi bi-arrow-down"></i></button>
                        </td>
                    <?php endif; ?>
                    <?php foreach ($columnas as $col): ?>
                        <td><?= htmlspecialchars($item[$col] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <?php endforeach; ?>
                    <td class="actions-cell">
                        <a href="editar.php?c=<?= urlencode($section) ?>&id=<?= $item['id'] ?>" class="btn btn-sm btn-secondary"><i class="bi bi-pencil-fill"></i> Editar</a>
                        <form method="POST" action="" style="display:inline;" class="form-delete-record">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <button type="button" class="btn btn-sm btn-outline js-delete-btn"><i class="bi bi-trash-fill"></i> Eliminar</button>
                        </form>
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

<div id="order-actions" style="display: none; margin-top: 24px; padding: 16px; background: #fffbe2; border: 1px solid #fde68a; border-radius: 8px;">
    <p style="margin-bottom: 12px; font-weight: 500; color: #b45309;"><i class="bi bi-info-circle-fill"></i> Has modificado el orden de los registros. No olvides guardar.</p>
    <button type="button" class="btn btn-primary" onclick="submitOrder()"><i class="bi bi-floppy-fill"></i> Guardar Cambios de Orden</button>
    <button type="button" class="btn btn-outline" onclick="location.reload()">Cancelar</button>
</div>

<script>
document.querySelectorAll('.js-move-up').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        if (row.previousElementSibling) {
            row.parentNode.insertBefore(row, row.previousElementSibling);
            document.getElementById('order-actions').style.display = 'block';
        }
    });
});
document.querySelectorAll('.js-move-down').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        if (row.nextElementSibling) {
            row.parentNode.insertBefore(row.nextElementSibling, row);
            document.getElementById('order-actions').style.display = 'block';
        }
    });
});
function submitOrder() {
    const ids = Array.from(document.querySelectorAll('tr[data-id]')).map(tr => tr.getAttribute('data-id'));
    document.getElementById('order-data-input').value = JSON.stringify(ids);
    document.getElementById('save-order-form').submit();
}
</script>
<?php endif; ?>

<?php
echo layout_end();
?>
