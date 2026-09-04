<?php /* includes/experiencia.php */ ?>
<!-- ============================================= -->
<!-- TU EXPERIENCIA ITB                            -->
<!-- ============================================= -->
<section class="experiencia" id="experiencia">
    <div class="experiencia__container">
        <!-- Lado izquierdo -->
        <div class="experiencia__content">
            <span class="section-tag"><?= content_get('experiencia', 'etiqueta_superior', 'Vida Estudiantil') ?></span>
            <h2 class="experiencia__title">
                <?= content_get('experiencia', 'titulo_seccion_1', 'Tu Experiencia') ?> <span class="text-orange"><?= content_get('experiencia', 'titulo_seccion_2', 'ITB') ?></span>
            </h2>
            <p class="experiencia__description">
                <?= content_get('experiencia', 'descripcion', 'Más allá de lo académico, el ITB te ofrece una experiencia universitaria completa con servicios y beneficios diseñados para tu bienestar.') ?>
            </p>

            <ul class="experiencia__list">
                <li class="experiencia__list-item">
                    <span class="experiencia__list-icon"><i class="fas fa-stethoscope"></i></span>
                    <div>
                        <strong><?= content_get('experiencia', 'caract1_titulo', 'Servicios Médicos') ?></strong>
                        <p><?= content_get('experiencia', 'caract1_desc', 'Atención médica y odontológica gratuita para estudiantes.') ?></p>
                    </div>
                </li>
                <li class="experiencia__list-item">
                    <span class="experiencia__list-icon"><i class="fas fa-award"></i></span>
                    <div>
                        <strong><?= content_get('experiencia', 'caract2_titulo', 'Becas y Financiamiento') ?></strong>
                        <p><?= content_get('experiencia', 'caract2_desc', 'Programas de becas por excelencia académica y apoyo financiero.') ?></p>
                    </div>
                </li>
                <li class="experiencia__list-item">
                    <span class="experiencia__list-icon"><i class="fas fa-laptop-code"></i></span>
                    <div>
                        <strong><?= content_get('experiencia', 'caract3_titulo', 'Laboratorios Modernos') ?></strong>
                        <p><?= content_get('experiencia', 'caract3_desc', 'Tecnología de punta en todos nuestros laboratorios especializados.') ?></p>
                    </div>
                </li>
                <li class="experiencia__list-item">
                    <span class="experiencia__list-icon"><i class="fas fa-handshake"></i></span>
                    <div>
                        <strong><?= content_get('experiencia', 'caract4_titulo', 'Bolsa de Empleo') ?></strong>
                        <p><?= content_get('experiencia', 'caract4_desc', 'Conexión directa con empresas aliadas para tus prácticas y primer empleo.') ?></p>
                    </div>
                </li>
            </ul>

            <a href="#" class="btn btn--solid" id="btn-beneficios">
                <?= content_get('experiencia', 'btn_texto', 'Más beneficios') ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Lado derecho: Imagen -->
        <div class="experiencia__image">
            <img src="<?= content_raw('experiencia', 'imagen', 'img/experiencia-itb.jpg') ?>" alt="Experiencia estudiantil ITB">
            <div class="experiencia__image-badge">
                <span class="experiencia__image-badge-number"><?= content_get('experiencia', 'badge_numero', '98%') ?></span>
                <span class="experiencia__image-badge-text"><?= content_get('experiencia', 'badge_texto', 'Satisfacción Estudiantil') ?></span>
            </div>
        </div>
    </div>
</section>
