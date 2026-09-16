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

/* ---------- Subir imágenes ---------- */
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

// biblioteca.css no se declara aquí: views/layout.php ya lo carga en todas las
// pantallas, porque el selector de imágenes puede salir en cualquiera.
echo layout_start('Biblioteca', 'biblioteca');
?>

<div class="dash-header">
    <h1>Biblioteca</h1>
    <p>Todas las imágenes del sitio viven aquí. Sube una vez y reutilízala en las secciones, la portada o el pie de página.</p>
</div>

<form method="post" enctype="multipart/form-data" class="biblioteca-subida" id="biblioteca-subida">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="action" value="subir">

    <div class="biblioteca-dropzone" id="biblioteca-dropzone">
        <i class="bi bi-cloud-arrow-up-fill"></i>
        <p class="biblioteca-dropzone__titulo">Arrastra tus imágenes aquí</p>
        <p class="biblioteca-dropzone__texto">
            o <label for="biblioteca-input" class="biblioteca-dropzone__enlace">búscalas en tu equipo</label>.
            JPG, PNG, WEBP, GIF o SVG. Se comprimen solas al subir.
        </p>
        <input type="file" id="biblioteca-input" name="imagenes[]" accept="image/*" multiple class="is-hidden">
    </div>

    <div class="biblioteca-seleccion is-hidden" id="biblioteca-seleccion">
        <span id="biblioteca-seleccion-texto"></span>
        <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Subir a la biblioteca</button>
    </div>
</form>

<div class="collection-header">
    <form method="get" class="search-box biblioteca-buscador">
        <?php /* El placeholder desaparece al escribir y algunos lectores de
                 pantalla ni lo anuncian, así que el campo lleva su etiqueta. */ ?>
        <input type="search" name="q" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"
               placeholder="Buscar por nombre de archivo..." class="form-input"
               aria-label="Buscar imágenes por nombre de archivo">
        <?php if ($busqueda !== ''): ?>
            <a href="<?= $AB ?>/biblioteca.php" class="btn btn-outline">Ver todas</a>
        <?php endif; ?>
    </form>
    <span class="biblioteca-conteo">
        <?= $total ?> <?= $total === 1 ? 'imagen' : 'imágenes' ?><?= $busqueda !== '' ? ' encontradas' : '' ?>
    </span>
</div>

<?php if ($total === 0): ?>
    <div class="biblioteca-vacia">
        <i class="bi bi-images"></i>
        <p><?= $busqueda !== ''
            ? 'Ninguna imagen coincide con esa búsqueda.'
            : 'Todavía no hay imágenes. Sube la primera con el recuadro de arriba.' ?></p>
    </div>
<?php else: ?>
    <div class="biblioteca-grid">
        <?php foreach ($imagenes as $img): ?>
            <?php
            $usos = biblioteca_usos($img['ruta']);
            $en_uso = !empty($usos);
            $titulo_usos = $en_uso
                ? 'En uso en: ' . implode(' · ', $usos)
                : 'No se está usando en ninguna sección';
            ?>
            <figure class="biblioteca-card<?= $en_uso ? ' is-en-uso' : '' ?>">
                <div class="biblioteca-card__img">
                    <img src="../<?= htmlspecialchars($img['ruta'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="<?= htmlspecialchars($img['nombre'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                </div>
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

<?= layout_end() ?>
