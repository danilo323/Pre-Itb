<?php
if (!function_exists('is_visible')) require_once 'content_helper.php';
if (!is_visible('sobre_mision')) return;

if (!function_exists('sobre_imagen')) {
    function sobre_imagen(string $ruta, string $respaldo = 'img/placeholder_imagen.svg'): string {
        $ruta = trim($ruta);
        return content_image_exists($ruta) ? $ruta : $respaldo;
    }
}

$mision_imagen = sobre_imagen((string) content_raw('sobre_mision', 'imagen', 'img/experiencia.png'));
$mision_puntos = (array) content_raw('sobre_mision', 'puntos', [
    ['texto' => 'Autonomía de pensamiento y liderazgo.'],
    ['texto' => 'Aliado estratégico de empresas e instituciones.'],
    ['texto' => 'Formación práctica orientada al desarrollo económico y social.'],
]);
$mision_titulo = content_get('sobre_mision', 'titulo', 'Nuestra misión');
$mision_desc   = content_get('sobre_mision', 'descripcion', 'Somos una Institución de Educación Superior comprometida con la excelencia académica y la formación de profesionales tecnológicos innovadores.');
?>
<!-- ============================================= -->
<!-- 2. NUESTRA MISIÓN                             -->
<!-- ============================================= -->
<article class="sobre-mv__card" id="nuestra-mision">
    <div class="sobre-mv__image">
        <img src="<?= htmlspecialchars($mision_imagen, ENT_QUOTES, 'UTF-8') ?>"
            alt="<?= htmlspecialchars($mision_titulo, ENT_QUOTES, 'UTF-8') ?> - ITB">
    </div>

    <div class="sobre-mv__body">
        <h2 class="sobre-mv__title"><?= htmlspecialchars($mision_titulo, ENT_QUOTES, 'UTF-8') ?></h2>

        <!-- La bolita + línea continua + bolita que acompaña al texto -->
        <div class="sobre-mv__text">
            <span class="sobre-mv__linea" aria-hidden="true"></span>

            <p class="sobre-mv__desc"><?= nl2br(htmlspecialchars($mision_desc, ENT_QUOTES, 'UTF-8')) ?></p>

            <?php if (!empty($mision_puntos)): ?>
                <ul class="sobre-mv__list">
                    <?php foreach ($mision_puntos as $punto): ?>
                        <?php $punto_texto = trim((string)($punto['texto'] ?? '')); ?>
                        <?php if ($punto_texto === '') continue; ?>
                        <li>
                            <span class="sobre-mv__check">✓</span>
                            <span class="sobre-mv__item-text"><?= htmlspecialchars($punto_texto, ENT_QUOTES, 'UTF-8') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</article>
