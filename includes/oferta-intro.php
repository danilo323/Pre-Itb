<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('oferta_intro')) return; ?>
<?php
// includes/oferta-intro.php
//
// Título + descripción centrados, arriba del buscador de Oferta Académica.
// Es contenido simple (sin foto, sin tarjetas): el buscador en sí vive en
// includes/oferta-programas.php y no lee nada de esta sección.
?>
<!-- ============================================= -->
<!-- OFERTA ACADÉMICA — PRESENTACIÓN               -->
<!-- ============================================= -->
<section class="oferta-intro" id="oferta-intro">
    <div class="oferta-intro__container">
        <h2 class="oferta-intro__title">
            <?= nl2br(content_get('oferta_intro', 'titulo', "Cientos de programas.\nUn título diseñado a tu medida.")) ?>
        </h2>
        <p class="oferta-intro__desc">
            <?= content_get('oferta_intro', 'descripcion', 'Elegir tu especialidad es solo el punto de partida.') ?>
        </p>
    </div>
</section>
