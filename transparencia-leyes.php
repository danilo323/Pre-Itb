Exit code: 0
Wall time: 1 seconds
Output:
<?php
/* transparencia-leyes.php
   Página interna "Transparencia / Leyes" del menú Instituto.
   Reutiliza el header, el footer y el sistema de colores/botones del sitio
   principal: solo agrega la cabecera con foto + el acordeón de documentos. */
session_start();
require_once 'includes/content_helper.php';

$doc_categorias = [
    'info_financiera'      => content_get('transparencia_leyes', 'cat_info_financiera', 'Información Financiera y Presupuestaria'),
    'rendicion_cuentas'    => content_get('transparencia_leyes', 'cat_rendicion_cuentas', 'Rendición de Cuentas y Gestión'),
    'talento_humano'       => content_get('transparencia_leyes', 'cat_talento_humano', 'Talento Humano'),
    'remuneracion_mensual' => content_get('transparencia_leyes', 'cat_remuneracion_mensual', 'Remuneración Mensual'),
];

$documentos = collection_items('documentos_transparencia');

$por_categoria = array_fill_keys(array_keys($doc_categorias), []);
$anios = [];
foreach ($documentos as $doc) {
    $publicado = $doc['publicado'] ?? true;
    if ($publicado === false || $publicado === '0') continue;

    $cat = $doc['categoria'] ?? '';
    if (isset($por_categoria[$cat])) {
        $por_categoria[$cat][] = $doc;
    }
    if (!empty($doc['anio'])) {
        $anios[$doc['anio']] = true;
    }
}
krsort($anios);
$anios = array_keys($anios);

// Tamaño legible: si ya hay un PDF real subido, se calcula de ese archivo
// (así nunca se desincroniza de lo que el admin subió). Si todavía no hay
// archivo, se usa el campo "Tamaño (provisional)" como texto de relleno.
function transparencia_filesize(string $relPath, string $provisional = ''): string {
    if ($relPath !== '') {
        $abs = __DIR__ . '/' . ltrim($relPath, '/');
        if (file_exists($abs)) {
            $bytes = filesize($abs);
            if ($bytes >= 1048576) return round($bytes / 1048576, 1) . 'MB';
            if ($bytes >= 1024) return round($bytes / 1024) . 'KB';
            return $bytes . 'B';
        }
    }
    return $provisional !== '' ? $provisional : '—';
}

// Misma foto de fondo que el hero de la portada, para que la imagen también
// se herede del panel en vez de quedar fija en el código.
$hero_imgs = content_raw('hero', 'imagenes_fondo', [['archivo' => 'img/hero_1.jpeg']]);
$page_hero_img = htmlspecialchars($hero_imgs[0]['archivo'] ?? 'img/hero_1.jpeg', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transparencia / Leyes — <?= content_get('ajustes', 'site_name', 'ITB - Instituto Superior Tecnológico Bolivariano de Tecnología') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;0,800;0,900;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/pagina-interna.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main>
        <section class="page-hero" style="background-image: url('<?= $page_hero_img ?>')">
            <div class="page-hero__overlay"></div>
            <div class="page-hero__container">
                <h1 class="page-hero__title">Cumplimiento Legal y Acceso a la Información</h1>
                <nav class="page-hero__breadcrumb">
                    <a href="index.php">Inicio</a>
                    <span>&gt;</span>
                    <span>Instituto</span>
                    <span>&gt;</span>
                    <span class="is-active">Transparencia / Leyes</span>
                </nav>
            </div>
        </section>

        <section class="transparencia">
            <div class="transparencia__container">
                <h2 class="transparencia__title"><?= content_get('transparencia_leyes', 'titulo', 'Transparencia e Información Pública') ?></h2>
                <p class="transparencia__desc">
                    <?= content_get('transparencia_leyes', 'descripcion', 'En cumplimiento con las disposiciones legales y el principio de rendición de cuentas, el ITB pone a disposición de la ciudadanía la información institucional, financiera y administrativa.') ?>
                </p>

                <div class="transparencia__toolbar">
                    <div class="transparencia__search-group">
                        <input type="text" id="doc-search" class="transparencia__search-input" placeholder="<?= content_get('transparencia_leyes', 'buscador_placeholder', 'Buscar documento, resolución o presupuesto...') ?>">
                        <button type="button" class="transparencia__search-btn" aria-label="Buscar">
                            <i class="fas fa-search"></i>
                        </button>

                    </div>
                    <select id="doc-year-filter" class="transparencia__year-filter">
                        <option value="">Todos los años</option>
                        <?php foreach ($anios as $anio): ?>
                            <option value="<?= htmlspecialchars($anio, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($anio, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="acc-list" id="doc-accordion">
                    <?php foreach ($doc_categorias as $cat_key => $cat_label): $docs = $por_categoria[$cat_key]; ?>
                        <div class="acc-item">
                            <button type="button" class="acc-item__header">
                                <span><?= htmlspecialchars($cat_label, ENT_QUOTES, 'UTF-8') ?></span>
                                <i class="fas fa-chevron-down acc-item__icon"></i>
                            </button>
                            <div class="acc-item__body">
                                <?php if (empty($docs)): ?>
                                    <p class="acc-item__empty">Aún no hay documentos publicados en esta sección.</p>
                                <?php else: ?>
                                    <table class="acc-table">
                                        <thead>
                                            <tr>
                                                <th>Documento</th>
                                                <th>Año</th>
                                                <th>Tamaño</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($docs as $doc):
                                                $archivo = $doc['archivo'] ?? '';
                                                $nombre = $doc['nombre'] ?? '';
                                                $anio = $doc['anio'] ?? '';
                                                $tiene_archivo = !empty($archivo) && file_exists(__DIR__ . '/' . ltrim($archivo, '/'));
                                                $href = $tiene_archivo ? htmlspecialchars($archivo, ENT_QUOTES, 'UTF-8') : '#';
                                            ?>
                                                <tr class="acc-table__row" data-nombre="<?= htmlspecialchars(mb_strtolower($nombre), ENT_QUOTES, 'UTF-8') ?>" data-anio="<?= htmlspecialchars($anio, ENT_QUOTES, 'UTF-8') ?>">
                                                    <td><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars($anio, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars(transparencia_filesize($archivo, $doc['tamano'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td>
                                                        <a href="<?= $href ?>" class="btn btn--solid" <?= $tiene_archivo ? 'target="_blank" rel="noopener"' : '' ?>>
                                                            Descargar PDF
                                                            <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
    <script src="js/pagina-interna.js"></script>

</body>
</html>

