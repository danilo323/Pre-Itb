<?php
if (!function_exists('is_visible')) require_once 'content_helper.php';
if (!is_visible('sobre_valores')) return;

if (!function_exists('sobre_imagen')) {
    function sobre_imagen(string $ruta, string $respaldo = 'img/placeholder_imagen.svg'): string {
        $ruta = trim($ruta);
        return content_image_exists($ruta) ? $ruta : $respaldo;
    }
}

$valores = (array) content_raw('sobre_valores', 'items', [
    ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Cogobierno',   'descripcion' => 'Participación y gestión democrática'],
    ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Igualdad',     'descripcion' => 'Oportunidades sin discriminación'],
    ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Calidad',      'descripcion' => 'Excelencia en educación superior'],
    ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Pertinencia',  'descripcion' => 'Programas alineados a la sociedad'],
    ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Integralidad', 'descripcion' => 'Formación académica y humana'],
]);
?>
<!-- ============================================= -->
<!-- 4. PRINCIPIOS Y VALORES                       -->
<!-- ============================================= -->
<section class="sobre-valores" id="principios-y-valores">
    <div class="sobre-valores__container">

        <div class="sobre-valores__header">
            <span class="section-tag"><?= content_get('sobre_valores', 'etiqueta_superior', 'NUESTROS PRINCIPIOS Y VALORES') ?></span>
            <h2 class="sobre-valores__title"><?= nl2br(content_get('sobre_valores', 'titulo', 'Los pilares que guían la excelencia académica y humana en el ITB.')) ?></h2>
        </div>

        <div class="sobre-valores__grid">
            <?php foreach ($valores as $valor): ?>
                <?php
                $valor_titulo = trim((string)($valor['titulo'] ?? ''));
                if ($valor_titulo === '') continue;
                $valor_icono = sobre_imagen((string)($valor['icono'] ?? ''));
                ?>
                <div class="sobre-valores__item">
                    <div class="sobre-valores__icon">
                        <img src="<?= htmlspecialchars($valor_icono, ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($valor_titulo, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <h3 class="sobre-valores__item-title"><?= htmlspecialchars($valor_titulo, ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="sobre-valores__item-text"><?= htmlspecialchars(trim((string)($valor['descripcion'] ?? '')), ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
