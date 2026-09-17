<?php
// includes/transparencia.php
// El panel pinta un interruptor "Visible" para esta seccion; sin esta linea
// el interruptor no hacia nada.
if (!function_exists('is_visible')) require_once __DIR__ . '/content_helper.php';
if (!is_visible('transparencia_leyes')) return;

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

if (!function_exists('transparencia_filesize')) {
    function transparencia_filesize(string $relPath, string $provisional = ''): string {
        if ($relPath !== '') {
            $abs = __DIR__ . '/../' . ltrim($relPath, '/');
            if (file_exists($abs)) {
                $bytes = filesize($abs);
                if ($bytes >= 1048576) return round($bytes / 1048576, 1) . 'MB';
                if ($bytes >= 1024) return round($bytes / 1024) . 'KB';
                return $bytes . 'B';
            }
        }
        return $provisional !== '' ? $provisional : '—';
    }
}
?>
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
                    <button type="button" class="acc-item__header" aria-expanded="false">
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
                                        $tiene_archivo = !empty($archivo) && file_exists(__DIR__ . '/../' . ltrim($archivo, '/'));
                                        $href = $tiene_archivo ? htmlspecialchars($archivo, ENT_QUOTES, 'UTF-8') : '#';
                                        // Nombre legible para el archivo descargado, en vez del hash aleatorio
                                        // con el que se guarda internamente en docs/.
                                        $nombre_descarga = trim(preg_replace('/[\\/:*?"<>|]+/', '', $nombre));
                                        $descarga_attr = $tiene_archivo ? 'download="' . htmlspecialchars($nombre_descarga !== '' ? $nombre_descarga . '.pdf' : basename($archivo), ENT_QUOTES, 'UTF-8') . '"' : '';
                                    ?>
                                        <tr class="acc-table__row" data-nombre="<?= htmlspecialchars(function_exists('mb_strtolower') ? mb_strtolower($nombre, 'UTF-8') : strtolower($nombre), ENT_QUOTES, 'UTF-8') ?>" data-anio="<?= htmlspecialchars($anio, ENT_QUOTES, 'UTF-8') ?>">
                                            <td><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($anio, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars(transparencia_filesize($archivo, $doc['tamano'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <a href="<?= $href ?>" class="btn btn--solid" <?= $descarga_attr ?>>
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
