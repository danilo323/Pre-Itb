<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('experiencia')) return; ?>
<?php /* includes/experiencia.php */ ?>
<!-- ============================================= -->
<!-- TU EXPERIENCIA ITB                            -->
<!-- ============================================= -->
<section class="experiencia" id="experiencia">
    <div class="experiencia__container">

        <!-- Lado izquierdo: Texto -->
        <div class="experiencia__content">
            <span class="experiencia__tag"><?= content_get('experiencia', 'etiqueta_superior', '¿Por qué elegir el ITB?') ?></span>
            <h2 class="experiencia__title"><?= content_title('experiencia', 'titulo', 'Tu Experiencia *ITB*') ?></h2>
            <p class="experiencia__description">
                <?= content_get('experiencia', 'descripcion', 'En el ITB no solo te formamos académicamente; nos preocupamos por tu bienestar integral. Te ofrecemos beneficios exclusivos diseñados para apoyarte durante toda tu carrera universitaria.') ?>
            </p>

            <ul class="experiencia__list">
                <li class="experiencia__list-item">
                    <span class="experiencia__check"><i class="fas fa-check"></i></span>
                    <p><?= content_raw('experiencia', 'caract1_desc', '<strong>Servicios Médicos Gratuitos</strong> (Podología, psicología, medicina general)') ?></p>
                </li>
                <li class="experiencia__list-item">
                    <span class="experiencia__check"><i class="fas fa-check"></i></span>
                    <p><?= content_raw('experiencia', 'caract2_desc', '<strong>Becas y Apoyo Económico</strong> (Académicas, deportivas y culturales)') ?></p>
                </li>
                <li class="experiencia__list-item">
                    <span class="experiencia__check"><i class="fas fa-check"></i></span>
                    <p><?= content_raw('experiencia', 'caract3_desc', '<strong>Gimnasio y SPA Gratis</strong> (Acceso exclusivo a ITB GYM)') ?></p>
                </li>
                <li class="experiencia__list-item">
                    <span class="experiencia__check"><i class="fas fa-check"></i></span>
                    <p><?= content_raw('experiencia', 'caract4_desc', '<strong>Modalidades a Tu Medida</strong> (Presencial, híbrida u online)') ?></p>
                </li>
            </ul>

            <a href="#" class="btn--solid" id="btn-beneficios">
                <?= content_get('experiencia', 'btn_texto', 'Más beneficios') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Lado derecho: Imagen -->
        <div class="experiencia__image">
            <img src="<?= content_raw('experiencia', 'imagen', 'img/experiencia.png') ?>" alt="Graduada del ITB">
        </div>

    </div>
</section>
