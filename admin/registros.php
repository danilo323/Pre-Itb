<?php
// admin/registros.php
//
// Registros que llegan del formulario de admisión de la landing:
// se listan aquí y se pueden descargar en CSV para abrir con Excel.
//
// La descarga se atiende ANTES de que layout.php imprima nada, porque un CSV
// necesita mandar sus propias cabeceras y con HTML ya enviado no se puede.

require_once __DIR__ . '/auth.php';
auth_require();

require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/base_url.php';
require_once dirname(__DIR__) . '/includes/registros.php';

$AB = admin_base();

/* ---------- Descargar CSV ---------- */
if (isset($_GET['descargar']) && $_GET['descargar'] === 'csv') {
    $lista = registros_leer();
    $csv = registros_a_csv($lista);
    $nombre = 'registros-itb-' . date('Y-m-d') . '.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nombre . '"');
    header('Content-Length: ' . strlen($csv));
    header('Cache-Control: no-store');
    echo $csv;
    exit;
}

/* ---------- Eliminar un registro ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0 && registros_eliminar($id)) {
        $_SESSION['flash_message'] = 'Registro eliminado correctamente.';
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = 'No se encontró ese registro.';
        $_SESSION['flash_type'] = 'error';
    }
    header("Location: {$AB}/registros.php");
    exit;
}

require_once __DIR__ . '/views/layout.php';

$registros = registros_leer();
$total = count($registros);

echo layout_start('Registros del formulario', 'registros');
?>

<div class="dash-header">
    <h1>Registros del formulario</h1>
    <p>Solicitudes recibidas desde el formulario de admisión de la página pública.</p>
</div>

<div class="collection-header">
    <div class="search-box">
        <input type="text" id="search-table" placeholder="Buscar por nombre, correo, cédula, carrera..." class="form-input">
    </div>
    <?php if ($total > 0): ?>
        <a href="<?= $AB ?>/registros.php?descargar=csv" class="btn btn-primary">
            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            Descargar CSV (<?= $total ?>)
        </a>
    <?php endif; ?>
</div>

<?php if ($total === 0): ?>

    <div class="field-alert field-alert-info">
        <i class="bi bi-inbox"></i>
        <div class="field-alert-body">
            <strong>Todavía no hay registros.</strong><br>
            Aparecerán aquí en cuanto alguien complete el formulario de admisión
            de la página pública. Entonces se activará el botón para descargarlos
            en CSV y abrirlos con Excel.
        </div>
    </div>

<?php else: ?>

    <div class="table-container">
        <table class="admin-table" id="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Nombres y apellidos</th>
                    <th>Correo</th>
                    <th>Celular</th>
                    <th>Cédula</th>
                    <th>Programa de interés</th>
                    <th>Modalidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $r): ?>
                    <tr>
                        <td>#<?= (int)$r['id'] ?></td>
                        <td><?= htmlspecialchars($r['fecha'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= htmlspecialchars(trim(($r['nombre'] ?? '') . ' ' . ($r['apellido'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                            <?php if (($r['bachiller'] ?? '') === 'si'): ?>
                                <br><small style="color:#6C757D;">Bachiller</small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($r['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= htmlspecialchars($r['cedula'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            <?php if (($r['nacionalidad'] ?? '') === 'extranjero'): ?>
                                <br><small style="color:#6C757D;">Extranjero</small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['carrera'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($r['modalidad'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="actions-cell">
                            <form method="POST" action="" class="form-delete-record is-inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button type="button" class="btn btn-sm btn-outline js-delete-btn"><i class="bi bi-trash-fill"></i> Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php if (trim($r['mensaje'] ?? '') !== ''): ?>
                        <tr>
                            <td colspan="9" style="background:#FAFBFC; color:#495057; font-size:0.85rem;">
                                <strong>Comentario:</strong>
                                <?= nl2br(htmlspecialchars($r['mensaje'], ENT_QUOTES, 'UTF-8')) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>

<?php
echo layout_end();
