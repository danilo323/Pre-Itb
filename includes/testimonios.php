<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('testimonios')) return; ?>
<!-- ============================================= -->
<!-- TESTIMONIOS - LO QUE DICEN NUESTROS           -->
<!-- ESTUDIANTES                                   -->
<!-- ============================================= -->
<section class="testimonios" id="testimonios">
    <div class="testimonios__container">

        <!-- Foto a la izquierda -->
        <div class="testimonios__photo">
            <img src="<?= htmlspecialchars(content_raw('testimonios', 'imagen', 'img/MariaFernanda.png'), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars(content_raw('testimonios', 'nombre', 'María Fernanda Gómez'), ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <!-- Contenido a la derecha -->
        <div class="testimonios__content">
            <span class="testimonios__tag"><?= content_get('testimonios', 'etiqueta_superior', 'Historias de Éxito') ?></span>
            <h2 class="testimonios__title"><?= nl2br(htmlspecialchars(content_raw('testimonios', 'titulo', 'Lo que dicen nuestros estudiantes'), ENT_QUOTES, 'UTF-8')) ?></h2>

            <div class="testimonios__quote-block">
                <span class="testimonios__quote-icon">&ldquo;</span>
                <p class="testimonios__quote-text">
                    <?= content_get('testimonios', 'cita', 'Gracias a la modalidad híbrida del ITB y la formación práctica en laboratorios, pude incorporarme rápidamente al sector laboral mientras terminaba mi carrera.') ?>
                </p>
            </div>

            <div class="testimonios__author">
                <!-- Se puede omitir el role si no está en el panel o extraer la primera palabra de la carrera -->
                <p class="testimonios__author-name"><?= content_get('testimonios', 'nombre', 'María Fernanda Gómez') ?></p>
                <p class="testimonios__author-program"><?= content_get('testimonios', 'carrera', 'Tecnología Superior en Enfermería') ?></p>
            </div>
        </div>

    </div>
</section>
