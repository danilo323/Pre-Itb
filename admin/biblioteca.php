<?php
// admin/biblioteca.php
//
// Pantalla de la Biblioteca de imágenes: el único sitio del panel donde se
// suben imágenes. Desde aquí se ven todas, se buscan y se eliminan; los campos
// de imagen de las secciones solo ELIGEN de esta lista
// (ver admin/fields/image.php).
//
// Atiende tres cosas:
//   - ?ajax=lista .... JSON con las imágenes, para el selector que sale en las
//                      secciones. Responde y termina ANTES de imprimir HTML.
//   - POST subir ..... una o varias imágenes a la vez.
//   - POST eliminar .. una imagen, siempre que no esté puesta en ninguna parte.

require_once __DIR__ . '/auth.php';
auth_require();

require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/base_url.php';
require_once __DIR__ . '/biblioteca_lib.php';

$AB = admin_base();

/* ---------- Lista en JSON para el selector de las secciones ---------- */
// Va lo primero: una respuesta JSON necesita mandar su propia cabecera y con
// HTML ya impreso no se puede.
if (($_GET['ajax'] ?? '') === 'lista') {
    $items = array_map(function (array $i) {
        return [
            'ruta'   => $i['ruta'],
            'nombre' => $i['nombre'],
            // El panel cuelga de /admin, así que el <img> del selector necesita
            // la ruta un nivel más arriba.
            'src'    => '../' . $i['ruta'],
            'peso'   => biblioteca_peso_legible($i['peso']),
        ];
    }, biblioteca_listar((string) ($_GET['q'] ?? '')));

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['ok' => true, 'items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- Subir UNA imagen y responder en JSON ----------
   La pantalla sube de una en una desde el navegador, para poder enseñar el
   progreso real de cada archivo y decir cuál falló y por qué. El POST normal de
   más abajo se conserva para quien tenga JavaScript desactivado. */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'subir_una') {
    csrf_check();

    $res = biblioteca_guardar_subida($_FILES['imagen'] ?? []);

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');

    if (!$res['ok']) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => $res['error']], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Se devuelven los datos de la tarjeta para poder pintarla sin recargar.
    $nombre  = biblioteca_nombre_de_ruta($res['ruta']);
    $fisica  = biblioteca_dir() . '/' . $nombre;
    echo json_encode([
        'ok'     => true,
        'imagen' => [
            'ruta'   => $res['ruta'],
            'nombre' => $nombre,
            'src'    => '../' . $res['ruta'],
            'peso'   => biblioteca_peso_legible((int) @filesize($fisica)),
            'fecha'  => date('d/m/Y'),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------- Eliminar varias a la vez, en JSON ----------
   Cada una pasa por la misma comprobación de uso que el borrado de una sola:
   las que estén publicadas se rechazan y se dice cuáles. */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'eliminar_varias') {
    csrf_check();

    $rutas = $_POST['rutas'] ?? [];
    $borradas = [];
    $fallos   = [];

    foreach ((array) $rutas as $ruta) {
        $res = biblioteca_eliminar((string) $ruta);
        if ($res['ok']) $borradas[] = (string) $ruta;
        else $fallos[] = biblioteca_nombre_de_ruta((string) $ruta) . ': ' . $res['error'];
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

/* ---------- Subir imágenes (sin JavaScript) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'subir') {
    csrf_check();

    // El input es múltiple, así que $_FILES llega "en columnas" (un array por
    // propiedad). Se recompone archivo por archivo.
    $subidas = $_FILES['imagenes'] ?? null;
    $total_ok = 0;
    $errores  = [];

    if ($subidas && is_array($subidas['name'])) {
        foreach (array_keys($subidas['name']) as $i) {
            if (($subidas['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;

            $res = biblioteca_guardar_subida([
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
            ? 'Imagen subida a la biblioteca.'
            : "{$total_ok} imágenes subidas a la biblioteca.";
        $_SESSION['flash_type'] = 'success';
    } elseif ($total_ok > 0) {
        $_SESSION['flash_message'] = "{$total_ok} subida(s). " . implode(' ', $errores);
        $_SESSION['flash_type'] = 'warning';
    } else {
        $_SESSION['flash_message'] = $errores ? implode(' ', $errores) : 'No se seleccionó ninguna imagen.';
        $_SESSION['flash_type'] = 'error';
    }

    header("Location: {$AB}/biblioteca.php");
    exit;
}

/* ---------- Eliminar una imagen ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'eliminar') {
    csrf_check();

    $res = biblioteca_eliminar((string) ($_POST['ruta'] ?? ''));
    $_SESSION['flash_message'] = $res['ok'] ? 'Imagen eliminada de la biblioteca.' : $res['error'];
    $_SESSION['flash_type']    = $res['ok'] ? 'success' : 'error';

    header("Location: {$AB}/biblioteca.php");
    exit;
}

require_once __DIR__ . '/views/layout.php';

$busqueda = trim((string) ($_GET['q'] ?? ''));
$imagenes = biblioteca_listar($busqueda);
$total    = count($imagenes);

// Se calcula aquí, de una vez, lo que antes se consultaba dentro del bucle:
// en qué secciones se usa cada imagen, cuántas están libres y cuánto ocupa
// todo. Sirve para los filtros, el contador y las acciones en lote.
$peso_total   = 0;
$n_sin_usar   = 0;
foreach ($imagenes as $i => $img) {
    $usos = biblioteca_usos($img['ruta']);
    $imagenes[$i]['usos']   = $usos;
    $imagenes[$i]['en_uso'] = !empty($usos);
    $peso_total += (int) $img['peso'];
    if (empty($usos)) $n_sin_usar++;
}

// biblioteca.css no se declara aquí: views/layout.php ya lo carga en todas las
// pantallas, porque el selector de imágenes puede salir en cualquiera.
echo layout_start('Biblioteca', 'biblioteca');
?>

<div class="dash-header dash-header--con-accion">
    <div>
        <h1>Biblioteca</h1>
        <p>Todas las imágenes del sitio viven aquí. Sube una vez y reutilízala en las secciones, la portada o el pie de página.</p>
    </div>
    <?php /* La subida vive en una ventana aparte: antes la zona de arrastre
             ocupaba la mitad de la pantalla siempre, aunque solo se use de vez
             en cuando, y empujaba las imágenes hacia abajo. */ ?>
    <button type="button" class="btn btn-primary" id="biblioteca-abrir-subida">
        <i class="bi bi-upload" aria-hidden="true"></i> Subir imágenes
    </button>
</div>

<!-- Ventana de subida. Misma mecánica que el modal de confirmación del panel
     (admin-confirm-overlay): oculta de entrada, se muestra con .is-visible. -->
<div class="admin-confirm-overlay biblioteca-modal is-hidden" id="biblioteca-modal"
     role="dialog" aria-modal="true" aria-labelledby="biblioteca-modal-titulo">
    <div class="admin-confirm-box biblioteca-modal__caja">
        <div class="biblioteca-modal__head">
            <div class="biblioteca-modal__titulo">
                <span class="biblioteca-modal__icono" aria-hidden="true"><i class="bi bi-cloud-arrow-up-fill"></i></span>
                <div>
                    <h3 id="biblioteca-modal-titulo">Subir imágenes</h3>
                    <p>Se guardan en la biblioteca y quedan disponibles en todo el sitio.</p>
                </div>
            </div>
            <button type="button" class="biblioteca-modal__cerrar" id="biblioteca-modal-cerrar" aria-label="Cerrar">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>

        <form method="post" enctype="multipart/form-data" class="biblioteca-subida" id="biblioteca-subida">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="subir">

            <div class="biblioteca-dropzone" id="biblioteca-dropzone">
                <span class="biblioteca-dropzone__icono" aria-hidden="true">
                    <i class="bi bi-images"></i>
                </span>
                <p class="biblioteca-dropzone__titulo">Arrastra tus imágenes aquí</p>
                <p class="biblioteca-dropzone__texto">
                    o <label for="biblioteca-input" class="biblioteca-dropzone__enlace">búscalas en tu equipo</label>
                </p>
                <ul class="biblioteca-dropzone__notas">
                    <li><i class="bi bi-file-earmark-image" aria-hidden="true"></i> JPG, PNG, WEBP, GIF o SVG</li>
                    <li><i class="bi bi-magic" aria-hidden="true"></i> Se comprimen solas</li>
                    <li><i class="bi bi-stack" aria-hidden="true"></i> Varias a la vez</li>
                </ul>
                <input type="file" id="biblioteca-input" name="imagenes[]" accept="image/*" multiple class="is-hidden">
            </div>

            <?php /* Miniaturas de lo elegido ANTES de subir: así se ve que son
                     las fotos correctas y se puede quitar alguna. */ ?>
            <div class="biblioteca-previews-caja is-hidden" id="biblioteca-previews-caja">
                <div class="biblioteca-previews-caja__head">
                    <h4 id="biblioteca-seleccion-texto" class="biblioteca-seleccion__texto"></h4>
                    <button type="button" class="biblioteca-previews-caja__vaciar" id="biblioteca-vaciar">
                        Quitar todas
                    </button>
                </div>
                <div class="biblioteca-previews" id="biblioteca-previews" aria-live="polite"></div>
            </div>

            <div class="biblioteca-modal__pie">
                <div class="biblioteca-modal__acciones">
                    <button type="button" class="btn btn-outline" id="biblioteca-modal-cancelar">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="biblioteca-enviar" disabled>
                        <i class="bi bi-upload" aria-hidden="true"></i> Subir a la biblioteca
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php /* Barra de herramientas: buscar, filtrar por uso, ordenar y elegir vista.
         Todo se resuelve en el navegador sobre las tarjetas ya pintadas, salvo
         la búsqueda, que sigue yendo al servidor para poder enlazarla. */ ?>
<div class="biblioteca-toolbar">
    <form method="get" class="biblioteca-buscador">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" name="q" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Buscar por nombre de archivo..." class="form-input"
               aria-label="Buscar imágenes por nombre de archivo">
        <?php if ($busqueda !== ''): ?>
            <a href="<?= $AB ?>/biblioteca.php" class="btn btn-outline btn-sm">Ver todas</a>
        <?php endif; ?>
    </form>

    <div class="biblioteca-toolbar__controles">
        <div class="biblioteca-filtros" role="group" aria-label="Filtrar por uso">
            <button type="button" class="biblioteca-chip is-activo" data-filtro-uso="todas" aria-pressed="true">
                Todas <span class="biblioteca-chip__n"><?= $total ?></span>
            </button>
            <button type="button" class="biblioteca-chip" data-filtro-uso="en-uso" aria-pressed="false">
                En uso <span class="biblioteca-chip__n"><?= $total - $n_sin_usar ?></span>
            </button>
            <button type="button" class="biblioteca-chip" data-filtro-uso="sin-usar" aria-pressed="false">
                Sin usar <span class="biblioteca-chip__n"><?= $n_sin_usar ?></span>
            </button>
        </div>

        <label class="biblioteca-orden">
            <span class="is-sr-only">Ordenar por</span>
            <select id="biblioteca-orden" class="form-input">
                <option value="recientes">Más recientes</option>
                <option value="antiguas">Más antiguas</option>
                <option value="nombre">Nombre (A-Z)</option>
                <option value="pesadas">Más pesadas</option>
            </select>
        </label>

        <div class="biblioteca-vistas" role="group" aria-label="Forma de ver las imágenes">
            <button type="button" class="biblioteca-vista is-activo" data-vista="rejilla"
                    aria-pressed="true" aria-label="Ver en rejilla" title="Rejilla">
                <i class="bi bi-grid-3x3-gap-fill" aria-hidden="true"></i>
            </button>
            <button type="button" class="biblioteca-vista" data-vista="lista"
                    aria-pressed="false" aria-label="Ver en lista" title="Lista">
                <i class="bi bi-list-ul" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>

<div class="biblioteca-resumen">
    <span id="biblioteca-conteo" class="biblioteca-conteo" role="status" aria-live="polite">
        <?= $total ?> <?= $total === 1 ? 'imagen' : 'imágenes' ?><?= $busqueda !== '' ? ' encontradas' : '' ?>
    </span>
    <span class="biblioteca-conteo biblioteca-conteo--suave">
        <?= biblioteca_peso_legible($peso_total) ?> en total
    </span>
</div>

<?php /* Barra de selección: aparece al marcar imágenes. Permite borrar varias
         de una vez, que con 105 archivos era ir de una en una. */ ?>
<div class="biblioteca-lote is-hidden" id="biblioteca-lote" role="status" aria-live="polite">
    <span id="biblioteca-lote-texto"></span>
    <div class="biblioteca-lote__acciones">
        <button type="button" class="btn btn-outline btn-sm" id="biblioteca-lote-cancelar">Quitar selección</button>
        <button type="button" class="btn btn-danger btn-sm" id="biblioteca-lote-eliminar">
            <i class="bi bi-trash-fill" aria-hidden="true"></i> Eliminar seleccionadas
        </button>
    </div>
</div>

<?php if ($total === 0): ?>
    <div class="biblioteca-vacia">
        <i class="bi bi-images"></i>
        <p><?= $busqueda !== ''
            ? 'Ninguna imagen coincide con esa búsqueda.'
            : 'Todavía no hay imágenes. Sube la primera con el recuadro de arriba.' ?></p>
    </div>
<?php else: ?>
    <?php
    // Sin la extension mbstring, mb_strtolower() no existe y la pagina moria
    // aqui dentro: la rejilla salia vacia aunque las imagenes estuvieran. Se
    // cae a strtolower() como ya hace biblioteca_listar() en biblioteca_lib.php.
    $to_lower = fn(string $s): string => function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
    ?>
    <div class="biblioteca-grid" id="biblioteca-grid" data-vista="rejilla">
        <?php foreach ($imagenes as $img): ?>
            <?php
            $usos = $img['usos'];
            $en_uso = $img['en_uso'];
            $titulo_usos = $en_uso
                ? 'En uso en: ' . implode(' · ', $usos)
                : 'No se está usando en ninguna sección';
            ?>
            <?php /* Los data-* llevan los datos con los que el navegador filtra
                     y ordena sin volver al servidor. */ ?>
            <figure class="biblioteca-card<?= $en_uso ? ' is-en-uso' : '' ?>"
                    data-nombre="<?= htmlspecialchars($to_lower($img['nombre']), ENT_QUOTES, 'UTF-8') ?>"
                    data-fecha="<?= (int) $img['fecha'] ?>"
                    data-peso="<?= (int) $img['peso'] ?>"
                    data-uso="<?= $en_uso ? 'en-uso' : 'sin-usar' ?>"
                    data-ruta="<?= htmlspecialchars($img['ruta'], ENT_QUOTES, 'UTF-8') ?>">

                <?php if (!$en_uso): ?>
                    <?php /* Solo se pueden marcar las que no están publicadas:
                             las que están en uso no se pueden borrar igualmente. */ ?>
                    <label class="biblioteca-card__marca">
                        <input type="checkbox" class="js-marcar"
                               aria-label="<?= htmlspecialchars('Seleccionar ' . $img['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                        <span aria-hidden="true"></span>
                    </label>
                <?php endif; ?>

                <?php /* La miniatura recorta para cuadrar la rejilla, así que
                         al pulsarla se abre la foto entera. Es un <button> y no
                         un <div> con clic para que funcione con el teclado. */ ?>
                <button type="button" class="biblioteca-card__img js-ver-imagen"
                        data-src="../<?= htmlspecialchars($img['ruta'], ENT_QUOTES, 'UTF-8') ?>"
                        data-nombre="<?= htmlspecialchars($img['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                        data-meta="<?= htmlspecialchars(biblioteca_peso_legible($img['peso']) . ' · ' . date('d/m/Y', $img['fecha']) . ' · ' . $titulo_usos, ENT_QUOTES, 'UTF-8') ?>"
                        data-ruta="<?= htmlspecialchars($img['ruta'], ENT_QUOTES, 'UTF-8') ?>"
                        data-peso="<?= htmlspecialchars(biblioteca_peso_legible($img['peso']), ENT_QUOTES, 'UTF-8') ?>"
                        data-fecha="<?= date('d/m/Y', $img['fecha']) ?>"
                        data-tipo="<?= htmlspecialchars(strtoupper($img['ext']), ENT_QUOTES, 'UTF-8') ?>"
                        data-usos="<?= htmlspecialchars(implode('|', $usos), ENT_QUOTES, 'UTF-8') ?>"
                        aria-label="<?= htmlspecialchars('Ver ' . $img['nombre'] . ' a tamaño completo', ENT_QUOTES, 'UTF-8') ?>">
                    <img src="../<?= htmlspecialchars($img['ruta'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="<?= htmlspecialchars($img['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                         loading="lazy" draggable="false">
                    <span class="biblioteca-card__lupa" aria-hidden="true"><i class="bi bi-arrows-fullscreen"></i></span>
                </button>
                <figcaption class="biblioteca-card__info">
                    <span class="biblioteca-card__nombre" title="<?= htmlspecialchars($img['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($img['nombre'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="biblioteca-card__meta">
                        <?= biblioteca_peso_legible($img['peso']) ?> · <?= date('d/m/Y', $img['fecha']) ?>
                    </span>
                    <span class="biblioteca-card__uso" title="<?= htmlspecialchars($titulo_usos, ENT_QUOTES, 'UTF-8') ?>">
                        <?php if ($en_uso): ?>
                            <i class="bi bi-link-45deg"></i> En uso (<?= count($usos) ?>)
                        <?php else: ?>
                            <i class="bi bi-dash-circle"></i> Sin usar
                        <?php endif; ?>
                    </span>
                </figcaption>
                <div class="biblioteca-card__acciones">
                    <?php /* Copiar la ruta: es lo que hay que pegar en cualquier
                             campo que pida una imagen, y hasta ahora había que
                             teclear un nombre de 32 caracteres a mano. */ ?>
                    <button type="button" class="btn btn-sm btn-outline js-copiar-ruta"
                            data-ruta="<?= htmlspecialchars($img['ruta'], ENT_QUOTES, 'UTF-8') ?>"
                            aria-label="<?= htmlspecialchars('Copiar la ruta de ' . $img['nombre'], ENT_QUOTES, 'UTF-8') ?>"
                            title="Copiar ruta">
                        <i class="bi bi-clipboard" aria-hidden="true"></i> Copiar ruta
                    </button>

                    <?php if ($en_uso): ?>
                        <?php /* Con muchas imágenes, "En uso" repetido no dice
                                 cuál es cuál: la etiqueta larga nombra el
                                 archivo y dónde está puesto. */ ?>
                        <button type="button" class="btn btn-sm btn-outline" disabled
                                title="<?= htmlspecialchars($titulo_usos, ENT_QUOTES, 'UTF-8') ?>"
                                aria-label="<?= htmlspecialchars($img['nombre'] . ' está en uso: ' . $titulo_usos, ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-lock-fill" aria-hidden="true"></i> En uso
                        </button>
                    <?php else: ?>
                        <form method="post" class="js-delete-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="action" value="eliminar">
                            <input type="hidden" name="ruta" value="<?= htmlspecialchars($img['ruta'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="button" class="btn btn-sm btn-danger js-delete-btn"
                                    aria-label="<?= htmlspecialchars('Eliminar ' . $img['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                                <i class="bi bi-trash-fill" aria-hidden="true"></i> Eliminar
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </figure>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Visor: la foto entera, sin el recorte de la miniatura. -->
<div class="admin-confirm-overlay biblioteca-visor is-hidden" id="biblioteca-visor"
     role="dialog" aria-modal="true" aria-labelledby="biblioteca-visor-nombre">
    <div class="biblioteca-visor__caja">
        <div class="biblioteca-visor__head">
            <h3 id="biblioteca-visor-nombre"></h3>
            <button type="button" class="biblioteca-modal__cerrar" id="biblioteca-visor-cerrar" aria-label="Cerrar">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>

        <div class="biblioteca-visor__cuerpo">
            <div class="biblioteca-visor__lienzo">
                <?php /* Flechas para recorrer la biblioteca sin cerrar y volver
                         a abrir. También responden a las teclas de dirección. */ ?>
                <button type="button" class="biblioteca-visor__nav biblioteca-visor__nav--prev"
                        id="biblioteca-visor-prev" aria-label="Imagen anterior">
                    <i class="bi bi-chevron-left" aria-hidden="true"></i>
                </button>
                <img id="biblioteca-visor-img" src="" alt="" draggable="false">
                <button type="button" class="biblioteca-visor__nav biblioteca-visor__nav--next"
                        id="biblioteca-visor-next" aria-label="Imagen siguiente">
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </button>
            </div>

            <?php /* Ficha del archivo. Antes el visor solo enseñaba la foto y
                     una línea de texto: para saber el tamaño real, la ruta o en
                     qué secciones estaba puesta había que salir a buscarlo. */ ?>
            <aside class="biblioteca-visor__ficha">
                <dl class="biblioteca-ficha">
                    <div class="biblioteca-ficha__fila">
                        <dt>Dimensiones</dt>
                        <dd id="biblioteca-visor-dim">—</dd>
                    </div>
                    <div class="biblioteca-ficha__fila">
                        <dt>Peso</dt>
                        <dd id="biblioteca-visor-peso">—</dd>
                    </div>
                    <div class="biblioteca-ficha__fila">
                        <dt>Formato</dt>
                        <dd id="biblioteca-visor-tipo">—</dd>
                    </div>
                    <div class="biblioteca-ficha__fila">
                        <dt>Subida</dt>
                        <dd id="biblioteca-visor-fecha">—</dd>
                    </div>
                </dl>

                <div class="biblioteca-ficha__bloque">
                    <h4>Ruta del archivo</h4>
                    <div class="biblioteca-ficha__ruta">
                        <code id="biblioteca-visor-ruta"></code>
                        <button type="button" class="btn btn-sm btn-outline js-copiar-ruta"
                                id="biblioteca-visor-copiar" data-ruta=""
                                aria-label="Copiar la ruta del archivo">
                            <i class="bi bi-clipboard" aria-hidden="true"></i> Copiar
                        </button>
                    </div>
                </div>

                <div class="biblioteca-ficha__bloque">
                    <h4>Dónde se usa</h4>
                    <ul class="biblioteca-ficha__usos" id="biblioteca-visor-usos"></ul>
                </div>

                <?php /* Para las imágenes con transparencia: sobre blanco no se
                         distingue qué es fondo y qué es imagen. En vez de dejar
                         el tablero puesto siempre, que ensucia, se puede cambiar
                         el fondo solo cuando hace falta. */ ?>
                <div class="biblioteca-ficha__bloque">
                    <h4>Fondo de la vista</h4>
                    <div class="biblioteca-fondos" role="group" aria-label="Fondo de la vista previa">
                        <button type="button" class="biblioteca-fondo is-activo" data-fondo="claro"
                                aria-pressed="true" title="Claro">Claro</button>
                        <button type="button" class="biblioteca-fondo" data-fondo="oscuro"
                                aria-pressed="false" title="Oscuro">Oscuro</button>
                        <button type="button" class="biblioteca-fondo" data-fondo="cuadros"
                                aria-pressed="false" title="Cuadros, para ver la transparencia">Cuadros</button>
                    </div>
                </div>

                <div class="biblioteca-ficha__pie">
                    <a href="#" target="_blank" rel="noopener" class="btn btn-sm btn-outline"
                       id="biblioteca-visor-abrir">
                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Abrir original
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>

<?= layout_end() ?>
