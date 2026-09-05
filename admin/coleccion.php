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

// Manejar Reordenamiento (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reorder') {
    $id_to_move = (int)$_POST['id'];
    $direction = $_POST['direction'] ?? 'up';
    
    // Encontrar el índice actual en el array ordenado
    $current_index = -1;
    foreach ($items as $idx => $item) {
        if ($item['id'] === $id_to_move) {
            $current_index = $idx;
            break;
        }
    }
    
    if ($current_index !== -1) {
        $swap_index = ($direction === 'up') ? $current_index - 1 : $current_index + 1;
        
        if (isset($items[$swap_index])) {
            $item_a = $items[$current_index];
            $item_b = $items[$swap_index];
            
            // Intercambiar campo orden
            $temp_orden = $item_a['orden'] ?? (string)($current_index + 1);
            $item_a['orden'] = $item_b['orden'] ?? (string)($swap_index + 1);
            $item_b['orden'] = $temp_orden;
            
            // Guardar en sesión y MySQL
            foreach ($_SESSION['admin_data'][$section]['items'] as &$sess_item) {
                if ($sess_item['id'] === $item_a['id']) $sess_item['orden'] = $item_a['orden'];
                if ($sess_item['id'] === $item_b['id']) $sess_item['orden'] = $item_b['orden'];
            }
            unset($sess_item);

            require_once __DIR__ . '/storage.php';
            $allCollectionItems = array_values($_SESSION['admin_data'][$section]['items']);
            storage_set($section, 'items', json_encode($allCollectionItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['flash_message'] = "Orden actualizado exitosamente en la base de datos";
                $_SESSION['flash_type'] = "success";
            }
        }
    }
    
    header("Location: coleccion.php?c=" . urlencode($section));
    exit;
}

// Manejar eliminación (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_to_delete = (int)$_POST['id'];
    require_once __DIR__ . '/storage.php';

    // Buscar el índice del id
    foreach ($items as $idx => $item) {
        if ($item['id'] === $id_to_delete) {
            // Eliminar foto física si fue subida por el panel
            if (!empty($item['foto'])) {
                storage_delete_old_file($item['foto']);
            }
            
            unset($_SESSION['admin_data'][$section]['items'][$idx]);
            // Reindexar arreglo para mantener orden limpio
            $_SESSION['admin_data'][$section]['items'] = array_values($_SESSION['admin_data'][$section]['items']);
            
            // Persistir en MySQL
            storage_set($section, 'items', json_encode($_SESSION['admin_data'][$section]['items'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['flash_message'] = "Registro eliminado correctamente de la base de datos";
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
        <input type="text" id="search-table" placeholder="Buscar en la lista..." class="form-input">
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
                <tr>
                    <td>#<?= $item['id'] ?></td>
                    <?php if ($is_sortable): ?>
                        <td class="actions-cell">
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="action" value="reorder">
                                <input type="hidden" name="direction" value="up">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline" <?= ($idx === 0) ? 'disabled style="opacity: 0.3;"' : '' ?>><i class="bi bi-arrow-up"></i></button>
                            </form>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="action" value="reorder">
                                <input type="hidden" name="direction" value="down">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline" <?= ($idx === count($items) - 1) ? 'disabled style="opacity: 0.3;"' : '' ?>><i class="bi bi-arrow-down"></i></button>
                            </form>
                        </td>
                    <?php endif; ?>
                    <?php foreach ($columnas as $col): ?>
                        <td><?= htmlspecialchars($item[$col] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <?php endforeach; ?>
                    <td class="actions-cell">
                        <a href="editar.php?c=<?= urlencode($section) ?>&id=<?= $item['id'] ?>" class="btn btn-sm btn-secondary"><i class="bi bi-pencil-fill"></i> Editar</a>
                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i> Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
echo layout_end();
?>
