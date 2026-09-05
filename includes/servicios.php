<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('servicios')) return; ?>
<!-- ============================================= -->
<!-- BIENESTAR ESTUDIANTIL / SERVICIOS             -->
<!-- ============================================= -->
<section class="servicios" id="servicios">
    <div class="servicios__container">

        <!-- Columna de Texto Principal -->
        <div class="servicios__intro">
            <h2 class="servicios__title"><?= content_raw('servicios', 'titulo', 'Bienestar<br>Estudiantil') ?></h2>
            <p class="servicios__desc">
                <?= content_get('servicios', 'descripcion', 'Impulsamos tu desarrollo integral dentro y fuera del aula con beneficios exclusivos para tu carrera.') ?>
            </p>
            <a href="#" class="btn--solid" id="btn-servicios-main">
                <?= content_get('servicios', 'btn_texto', 'Más servicios') ?>
                <span class="btn__icon-right-white"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Tarjetas (Cards) -->
        <div class="servicios__cards">
            
            <!-- Card 1 -->
            <div class="servicios__card-simple">
                <h3 class="servicios__card-title"><?= content_raw('servicios', 'serv1_titulo', 'Campus<br>Virtual 360°') ?></h3>
                <p class="servicios__card-text"><?= content_get('servicios', 'serv1_desc', 'Conoce nuestras instalaciones, aulas y laboratorios de forma interactiva.') ?></p>
                <a href="#" class="btn--outline-servicios">
                    <?= content_get('servicios', 'serv1_btn', 'Ver Tour') ?>
                    <span class="btn__icon-servicios"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 2 -->
            <div class="servicios__card-simple">
                <h3 class="servicios__card-title"><?= content_raw('servicios', 'serv2_titulo', 'Horarios y<br>Clases') ?></h3>
                <p class="servicios__card-text"><?= content_get('servicios', 'serv2_desc', 'Consulta turnos presenciales, nocturnos y de fin de semana.') ?></p>
                <a href="#" class="btn--outline-servicios">
                    <?= content_get('servicios', 'serv2_btn', 'Ver Horarios') ?>
                    <span class="btn__icon-servicios"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 3 -->
            <div class="servicios__card-simple">
                <h3 class="servicios__card-title"><?= content_raw('servicios', 'serv3_titulo', 'Servicios<br>Digitales') ?></h3>
                <p class="servicios__card-text"><?= content_get('servicios', 'serv3_desc', 'Accede al Aula Virtual, App Móvil y herramientas académicas.') ?></p>
                <a href="#" class="btn--outline-servicios">
                    <?= content_get('servicios', 'serv3_btn', 'Acceder') ?>
                    <span class="btn__icon-servicios"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 4 -->
            <div class="servicios__card-simple">
                <h3 class="servicios__card-title"><?= content_raw('servicios', 'serv4_titulo', '#Podcast<br>ITB') ?></h3>
                <p class="servicios__card-text"><?= content_get('servicios', 'serv4_desc', 'Historias de éxito y consejos de docentes y graduados.') ?></p>
                <a href="#" class="btn--outline-servicios">
                    <?= content_get('servicios', 'serv4_btn', 'Escuchar') ?>
                    <span class="btn__icon-servicios"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 5 (Imagen de fondo) -->
            <div class="servicios__card-image">
                <img src="<?= content_raw('servicios', 'serv6_imagen', 'img/estudiantes1.png') ?>" alt="Arte y Deportes">
                <div class="servicios__card-overlay"></div>
                <div class="servicios__card-content">
                    <h3 class="servicios__card-title-white"><?= content_raw('servicios', 'serv6_titulo', 'Arte y<br>Deportes') ?></h3>
                    <p class="servicios__card-text-white"><?= content_get('servicios', 'serv6_desc', 'Participa en grupos culturales, eventos y torneos.') ?></p>
                    <a href="#" class="btn--solid">
                        <?= content_get('servicios', 'serv6_btn', 'Conocer Más') ?>
                        <span class="btn__icon-right-white"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
