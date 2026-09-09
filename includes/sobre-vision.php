<?php
if (!function_exists('is_visible')) require_once 'content_helper.php';
if (!is_visible('sobre_vision')) return;

if (!function_exists('sobre_imagen')) {
    function sobre_imagen(string $ruta, string $respaldo = 'img/placeholder_imagen.svg'): string {
        $ruta = trim($ruta);
        return content_image_exists($ruta) ? $ruta : $respaldo;
    }
}

$vision_imagen = sobre_imagen((string) content_raw('sobre_vision', 'imagen', 'img/bienestar_estudiantil_1.png'));
$vision_puntos = (array) content_raw('sobre_vision', 'puntos', [
    ['texto' => 'Liderazgo en educación tecnológica universitaria.'],
    ['texto' => 'Alianzas e integración con instituciones nacionales e internacionales.'],
    ['texto' => 'Transferencia tecnológica e innovación para el aprendizaje continuo.'],
]);
$vision_titulo = content_get('sobre_vision', 'titulo', 'Nuestra visión');
$vision_desc   = content_get('sobre_vision', 'descripcion', 'Ser una institución de futuro, acreditada internacionalmente, que lidere la formación tecnológica en el Ecuador.');
?>
<!-- ============================================= -->
<!-- 3. NUESTRA VISIÓN                             -->
<!-- ============================================= -->
<article class="sobre-mv__card sobre-mv__card--invertida" id="nuestra-vision">
    <div class="sobre-mv__image">
        <img src="<?= htmlspecialchars($vision_imagen, ENT_QUOTES, 'UTF-8') ?>"
            alt="<?= htmlspecialchars($vision_titulo, ENT_QUOTES, 'UTF-8') ?> - ITB">
    </div>

    <div class="sobre-mv__body">
        <h2 class="sobre-mv__title"><?= htmlspecialchars($vision_titulo, ENT_QUOTES, 'UTF-8') ?></h2>

        <!-- La bolita + línea continua + bolita que acompaña al texto -->
        <div class="sobre-mv__text">
            <span class="sobre-mv__linea" aria-hidden="true"></span>

            <p class="sobre-mv__desc"><?= nl2br(htmlspecialchars($vision_desc, ENT_QUOTES, 'UTF-8')) ?></p>

            <?php if (!empty($vision_puntos)): ?>
                <ul class="sobre-mv__list">
                    <?php foreach ($vision_puntos as $punto): ?>
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
