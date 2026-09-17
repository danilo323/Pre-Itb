<?php
// admin/documentos.php
//
// Pantalla de la Galería de Documentos: el único sitio del panel donde se
// suben documentos (PDF, DOCX, XLSX, XML...). Desde aquí se ven todos, se
// buscan y se eliminan; los campos de archivo de las secciones solo ELIGEN
// de esta lista.
//
// Atiende tres cosas:
//   - ?ajax=lista .... JSON con los documentos, para el selector.
//   - POST subir ..... uno o varios documentos a la vez.
//   - POST eliminar .. un documento, siempre que no esté en uso.

require_once __DIR__ . '/auth.php';
auth_require();

require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/base_url.php';
require_once __DIR__ . '/documentos_lib.php';

$AB = admin_base();

/* ---------- Lista en JSON para el selector de las secciones ---------- */
if (($_GET['ajax'] ?? '') === 'lista') {
    $items = array_map(function (array $i) {
        return [
            'ruta'   => $i['ruta'],
            'nombre' => $i['nombre'],
            'src'    => '../' . $i['ruta'],
            'peso'   => documentos_peso_legible($i['peso']),
        ];
    }, documentos_listar((string) ($_GET['q'] ?? '')));

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['ok' => true, 'items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- Subir UN documento y responder en JSON ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'subir_una') {
    csrf_check();

    $res = documentos_guardar_subida($_FILES['documento'] ?? []);

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');

    if (!$res['ok']) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => $res['error']], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $nombre  = documentos_nombre_de_ruta($res['ruta']);
    $fisica  = documentos_dir() . '/' . $nombre;
    echo json_encode([
        'ok'        => true,
        'documento' => [
            'ruta'   => $res['ruta'],
            'nombre' => $nombre,
            'src'    => '../' . $res['ruta'],
            'peso'   => documentos_peso_legible((int) @filesize($fisica)),
            'fecha'  => date('d/m/Y'),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- Eliminar varios a la vez, en JSON ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'eliminar_varias') {
    csrf_check();

    $rutas    = $_POST['rutas'] ?? [];
    $borradas = [];
    $fallos   = [];

    foreach ((array) $rutas as $ruta) {
        $res = documentos_eliminar((string) $ruta);
        if ($res['ok']) $borradas[] = (string) $ruta;
        else $fallos[] = documentos_nombre_de_ruta((string) $ruta) . ': ' . $res['error'];
    }

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode([
        'ok'       => empty($fallos),
        'borradas' => $borradas,
        'fallos'   => $fallos,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- Subir documentos (sin JavaScript) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'subir') {
    csrf_check();

    $subidas  = $_FILES['documentoes'] ?? null;
    $total_ok = 0;
    $errores  = [];

    if ($subidas && is_array($subidas['name'])) {
        foreach (array_keys($subidas['name']) as $i) {
            if (($subidas['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;

            $res = documentos_guardar_subida([
                'name'     => $subidas['name'][$i],
                'type'     => $subidas['type'][$i]     ?? '',
                'tmp_name' => $subidas['tmp_name'][$i] ?? '',
                'error'    => $subidas['error'][$i]    ?? UPLOAD_ERR_NO_FILE,
                'size'     => $subidas['size'][$i]     ?? 0,
            ]);

            if ($res['ok']) $total_ok++;
            else $errores[] = $res['error'];
        }
    }

    if ($total_ok > 0 && empty($errores)) {
        $_SESSION['flash_message'] = $total_ok === 1
            ? 'Documento subido a la galería.'
            : "{$total_ok} documentos subidos a la galería.";
        $_SESSION['flash_type'] = 'success';
    } elseif ($total_ok > 0) {
        $_SESSION['flash_message'] = "{$total_ok} subido(s). " . implode(' ', $errores);
        $_SESSION['flash_type'] = 'warning';
    } else {
        $_SESSION['flash_message'] = $errores ? implode(' ', $errores) : 'No se seleccionó ningún documento.';
        $_SESSION['flash_type'] = 'error';
    }

    header("Location: {$AB}/documentos.php");
    exit;
}

/* ---------- Eliminar un documento ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'eliminar') {
    csrf_check();

    $res = documentos_eliminar((string) ($_POST['ruta'] ?? ''));
    $_SESSION['flash_message'] = $res['ok'] ? 'Documento eliminado de la galería.' : $res['error'];
    $_SESSION['flash_type']    = $res['ok'] ? 'success' : 'error';

    header("Location: {$AB}/documentos.php");
    exit;
}

require_once __DIR__ . '/views/layout.php';

$busqueda   = trim((string) ($_GET['q'] ?? ''));
$documentos = documentos_listar($busqueda);
$total      = count($documentos);

$peso_total = 0;
$n_sin_usar = 0;
foreach ($documentos as $i => $doc) {
    $usos = documentos_usos($doc['ruta']);
    $documentos[$i]['usos']   = $usos;
    $documentos[$i]['en_uso'] = !empty($usos);
    $peso_total += (int) $doc['peso'];
    if (empty($usos)) $n_sin_usar++;
}

echo layout_start('Documentos', 'documentos');
?>

<div class="dash-header dash-header--con-accion">
    <div>
        <h1>Documentos</h1>
        <p>Todos los documentos del sitio viven aquí. Sube una vez y reutilízalo en las secciones que lo necesiten.</p>
    </div>
    <button type="button" class="btn btn-primary" id="documentos-abrir-subida">
        <i class="bi bi-upload" aria-hidden="true"></i> Subir documentos
    </button>
</div>

<!-- Ventana de subida -->
<div class="admin-confirm-overlay documentos-modal is-hidden" id="documentos-modal"
     role="dialog" aria-modal="true" aria-labelledby="documentos-modal-titulo">
    <div class="admin-confirm-box documentos-modal__caja">
        <div class="documentos-modal__head">
            <div class="documentos-modal__titulo">
                <span class="documentos-modal__icono" aria-hidden="true"><i class="bi bi-cloud-arrow-up-fill"></i></span>
                <div>
                    <h3 id="documentos-modal-titulo">Subir documentos</h3>
                    <p>Se guardan en la galería y quedan disponibles en todo el sitio.</p>
                </div>
            </div>
            <button type="button" class="documentos-modal__cerrar" id="documentos-modal-cerrar" aria-label="Cerrar">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>

        <form method="post" enctype="multipart/form-data" class="documentos-subida" id="documentos-subida">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="subir">

            <div class="documentos-dropzone" id="documentos-dropzone">
                <span class="documentos-dropzone__icono" aria-hidden="true">
                    <i class="bi bi-folder-fill"></i>
                </span>
                <p class="documentos-dropzone__titulo">Arrastra tus documentos aquí</p>
                <p class="documentos-dropzone__texto">
                    o <label for="documentos-input" class="documentos-dropzone__enlace">búscalos en tu equipo</label>
                </p>
                <ul class="documentos-dropzone__notas">
                    <li><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> PDF, XML, DOCX, XLSX</li>
                    <li><i class="bi bi-stack" aria-hidden="true"></i> Varios a la vez</li>
                </ul>
                <input type="file" id="documentos-input" name="documentoes[]" accept=".pdf,.xml,.doc,.docx,.xls,.xlsx" multiple class="is-hidden">
            </div>

            <div class="documentos-previews-caja is-hidden" id="documentos-previews-caja">
                <div class="documentos-previews-caja__head">
                    <h4 id="documentos-seleccion-texto" class="documentos-seleccion__texto"></h4>
                    <button type="button" class="documentos-previews-caja__vaciar" id="documentos-vaciar">
                        Quitar todos
                    </button>
                </div>
                <div class="documentos-previews" id="documentos-previews" aria-live="polite"></div>
            </div>

            <div class="documentos-modal__pie">
                <div class="documentos-modal__acciones">
                    <button type="button" class="btn btn-outline" id="documentos-modal-cancelar">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="documentos-enviar" disabled>
                        <i class="bi bi-upload" aria-hidden="true"></i> Subir a la galería
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Barra de herramientas -->
<div class="documentos-toolbar">
    <form method="get" class="documentos-buscador">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" name="q" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Buscar por nombre de archivo..." class="form-input"
               aria-label="Buscar documentos por nombre de archivo">
        <?php if ($busqueda !== ''): ?>
            <a href="<?= $AB ?>/documentos.php" class="btn btn-outline btn-sm">Ver todos</a>
        <?php endif; ?>
    </form>

    <div class="documentos-toolbar__controles">
        <div class="documentos-filtros" role="group" aria-label="Filtrar por uso">
            <button type="button" class="documentos-chip is-activo" data-filtro-uso="todas" aria-pressed="true">
                Todos <span class="documentos-chip__n"><?= $total ?></span>
            </button>
            <button type="button" class="documentos-chip" data-filtro-uso="en-uso" aria-pressed="false">
                En uso <span class="documentos-chip__n"><?= $total - $n_sin_usar ?></span>
            </button>
            <button type="button" class="documentos-chip" data-filtro-uso="sin-usar" aria-pressed="false">
                Sin usar <span class="documentos-chip__n"><?= $n_sin_usar ?></span>
            </button>
        </div>

        <label class="documentos-orden">
            <span class="is-sr-only">Ordenar por</span>
            <select id="documentos-orden" class="form-input">
                <option value="recientes">Más recientes</option>
                <option value="antiguas">Más antiguos</option>
                <option value="nombre">Nombre (A-Z)</option>
                <option value="pesadas">Más pesados</option>
            </select>
        </label>

        <div class="documentos-vistas" role="group" aria-label="Forma de ver los documentos">
            <button type="button" class="documentos-vista is-activo" data-vista="rejilla"
                    aria-pressed="true" aria-label="Ver en rejilla" title="Rejilla">
                <i class="bi bi-grid-3x3-gap-fill" aria-hidden="true"></i>
            </button>
            <button type="button" class="documentos-vista" data-vista="lista"
                    aria-pressed="false" aria-label="Ver en lista" title="Lista">
                <i class="bi bi-list-ul" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>

<div class="documentos-resumen">
    <span id="documentos-conteo" class="documentos-conteo" role="status" aria-live="polite">
        <?= $total ?> <?= $total === 1 ? 'documento' : 'documentos' ?><?= $busqueda !== '' ? ' encontrados' : '' ?>
    </span>
    <span class="documentos-conteo documentos-conteo--suave">
        <?= documentos_peso_legible($peso_total) ?> en total
    </span>
</div>

<!-- Barra de selección en lote -->
<div class="documentos-lote is-hidden" id="documentos-lote" role="status" aria-live="polite">
    <span id="documentos-lote-texto"></span>
    <div class="documentos-lote__acciones">
        <button type="button" class="btn btn-outline btn-sm" id="documentos-lote-cancelar">Quitar selección</button>
        <button type="button" class="btn btn-danger btn-sm" id="documentos-lote-eliminar">
            <i class="bi bi-trash-fill" aria-hidden="true"></i> Eliminar seleccionados
        </button>
    </div>
</div>

<?php if ($total === 0): ?>
    <div class="documentos-vacia">
        <i class="bi bi-folder2-open"></i>
        <p><?= $busqueda !== ''
            ? 'Ningún documento coincide con esa búsqueda.'
            : 'Todavía no hay documentos. Sube el primero con el botón de arriba.' ?></p>
    </div>
<?php else: ?>
    <?php
    $to_lower = fn(string $s): string => function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
    ?>
    <div class="documentos-grid" id="documentos-grid" data-vista="rejilla">
        <?php foreach ($documentos as $doc): ?>
            <?php
            $usos       = $doc['usos'];
            $en_uso     = $doc['en_uso'];
            $titulo_usos = $en_uso
                ? 'En uso en: ' . implode(' · ', $usos)
                : 'No se está usando en ninguna sección';

            // Icono según extensión
            $ext = strtolower($doc['ext'] ?? '');
            if ($ext === 'pdf') {
                $doc_icon  = 'bi-file-earmark-pdf-fill';
                $doc_color = '#dc3545';
            } elseif (in_array($ext, ['doc', 'docx'])) {
                $doc_icon  = 'bi-file-earmark-word-fill';
                $doc_color = '#0d6efd';
            } elseif (in_array($ext, ['xls', 'xlsx'])) {
                $doc_icon  = 'bi-file-earmark-excel-fill';
                $doc_color = '#198754';
            } elseif ($ext === 'xml') {
                $doc_icon  = 'bi-file-earmark-code-fill';
                $doc_color = '#fd7e14';
            } else {
                $doc_icon  = 'bi-file-earmark-fill';
                $doc_color = '#6c757d';
            }
            ?>
            <figure class="documentos-card<?= $en_uso ? ' is-en-uso' : '' ?>"
                    data-nombre="<?= htmlspecialchars($to_lower($doc['nombre']), ENT_QUOTES, 'UTF-8') ?>"
                    data-fecha="<?= (int) $doc['fecha'] ?>"
                    data-peso="<?= (int) $doc['peso'] ?>"
                    data-uso="<?= $en_uso ? 'en-uso' : 'sin-usar' ?>"
                    data-ruta="<?= htmlspecialchars($doc['ruta'], ENT_QUOTES, 'UTF-8') ?>">

                <?php if (!$en_uso): ?>
                    <label class="documentos-card__marca">
                        <input type="checkbox" class="js-marcar"
                               aria-label="<?= htmlspecialchars('Seleccionar ' . $doc['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                        <span aria-hidden="true"></span>
                    </label>
                <?php endif; ?>

                <button type="button" class="documentos-card__img js-ver-documento"
                        data-src="../<?= htmlspecialchars($doc['ruta'], ENT_QUOTES, 'UTF-8') ?>"
                        data-nombre="<?= htmlspecialchars($doc['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                        data-ruta="<?= htmlspecialchars($doc['ruta'], ENT_QUOTES, 'UTF-8') ?>"
                        data-peso="<?= htmlspecialchars(documentos_peso_legible($doc['peso']), ENT_QUOTES, 'UTF-8') ?>"
                        data-fecha="<?= date('d/m/Y', $doc['fecha']) ?>"
                        data-tipo="<?= htmlspecialchars(strtoupper($doc['ext']), ENT_QUOTES, 'UTF-8') ?>"
                        data-usos="<?= htmlspecialchars(implode('|', $usos), ENT_QUOTES, 'UTF-8') ?>"
                        aria-label="<?= htmlspecialchars('Abrir ' . $doc['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                    <i class="bi <?= $doc_icon ?>" style="font-size:3.5rem; color:<?= $doc_color ?>;"></i>
                    <span class="documentos-card__ext"><?= strtoupper($ext) ?></span>
                </button>

                <figcaption class="documentos-card__info">
                    <span class="documentos-card__nombre" title="<?= htmlspecialchars($doc['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($doc['nombre'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="documentos-card__meta">
                        <?= documentos_peso_legible($doc['peso']) ?> · <?= date('d/m/Y', $doc['fecha']) ?>
                    </span>
                    <span class="documentos-card__uso" title="<?= htmlspecialchars($titulo_usos, ENT_QUOTES, 'UTF-8') ?>">
                        <?php if ($en_uso): ?>
                            <i class="bi bi-link-45deg"></i> En uso (<?= count($usos) ?>)
                        <?php else: ?>
                            <i class="bi bi-dash-circle"></i> Sin usar
                        <?php endif; ?>
                    </span>
                </figcaption>

                <div class="documentos-card__acciones">
                    <button type="button" class="btn btn-sm btn-outline js-copiar-ruta"
                            data-ruta="<?= htmlspecialchars($doc['ruta'], ENT_QUOTES, 'UTF-8') ?>"
                            aria-label="<?= htmlspecialchars('Copiar la ruta de ' . $doc['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                            title="Copiar ruta">
                        <i class="bi bi-clipboard" aria-hidden="true"></i> Copiar ruta
                    </button>

                    <?php if ($en_uso): ?>
                        <button type="button" class="btn btn-sm btn-outline" disabled
                                title="<?= htmlspecialchars($titulo_usos, ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-lock-fill" aria-hidden="true"></i> En uso
                        </button>
                    <?php else: ?>
                        <form method="post" class="js-delete-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="ruta" value="<?= htmlspecialchars($doc['ruta'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="button" class="btn btn-sm btn-danger js-delete-btn"
                                    aria-label="<?= htmlspecialchars('Eliminar ' . $doc['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                                <i class="bi bi-trash-fill" aria-hidden="true"></i> Eliminar
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </figure>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= layout_end() ?>
