<?php
// admin/coleccion.php
// Motor genérico para listar registros de una colección (ej. Testimonios, Noticias)

require_once __DIR__ . '/views/layout.php';
$schema = require __DIR__ . '/schema_mock.php';

$section = $_GET['c'] ?? 'testimonios';

// Validar Whitelist del schema
if (!isset($schema[$section]) || $schema[$section]['type'] !== 'collection') {
    die("Colección no encontrada.");
}

$config = $schema[$section];

// Datos simulados (Persona 3 reemplazará esto con storage_get())
$items = [
    [
        'id' => 1,
        'nombre' => 'Carlos Mendoza',
        'carrera' => 'Tecnología en Desarrollo de Software',
        'testimonio' => 'El ITB me dio las herramientas prácticas para conseguir trabajo en el primer año.',
        'publicado' => true
    ],
    [
        'id' => 2,
        'nombre' => 'María José Delgado',
        'carrera' => 'Enfermería',
        'testimonio' => 'Excelente laboratorio de simulación clínica y docentes calificados.',
        'publicado' => true
    ],
    [
        'id' => 3,
        'nombre' => 'Javier Ortiz',
        'carrera' => 'Administración de Empresas',
        'testimonio' => 'Los horarios flexibles me permitieron trabajar y estudiar al mismo tiempo.',
        'publicado' => false
    ]
];

echo layout_start("Gestión de: " . $config['label']);
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
                <?php foreach ($config['columns'] as $col): ?>
                    <th><?= htmlspecialchars(ucfirst($col), ENT_QUOTES, 'UTF-8') ?></th>
                <?php endforeach; ?>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>#<?= $item['id'] ?></td>
                    <?php foreach ($config['columns'] as $col): ?>
                        <td><?= htmlspecialchars($item[$col] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <?php endforeach; ?>
                    <td>
                        <?php if (!empty($item['publicado'])): ?>
                            <span class="badge badge-success">Publicado</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Borrador</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <a href="editar.php?c=<?= urlencode($section) ?>&id=<?= $item['id'] ?>" class="btn btn-sm btn-secondary">✏️ Editar</a>
                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">🗑️ Eliminar</button>
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
