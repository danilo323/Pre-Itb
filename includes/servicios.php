<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('servicios')) return; ?>
<!-- ============================================= -->
<!-- BIENESTAR ESTUDIANTIL / SERVICIOS             -->
<!-- ============================================= -->
<section class="servicios" id="servicios">
    <div class="servicios__container">
        <!-- Columna de Texto Principal -->
        <div class="servicios__intro">
            <h2 class="servicios__title">
                <?= nl2br(htmlspecialchars(content_get('servicios', 'serv1_titulo', "Bienestar\nEstudiantil"), ENT_QUOTES, 'UTF-8')) ?>
            </h2>
            <p class="servicios__desc">
                <?= htmlspecialchars(content_get('servicios', 'serv1_desc', 'Impulsamos tu desarrollo integral dentro y fuera del aula con beneficios exclusivos para tu carrera.'), ENT_QUOTES, 'UTF-8') ?>
            </p>
            <a href="#" class="btn--solid" id="btn-servicios-main">
                Más servicios
                <span class="btn__icon-right-white"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Tarjetas (Cards) -->
        <div class="servicios__cards">
            
            <!-- Card 1 -->
            <div class="servicios__card-simple">
                <h3 class="servicios__card-title"><?= nl2br(htmlspecialchars(content_get('servicios', 'serv2_titulo', "Campus\nVirtual 360°"), ENT_QUOTES, 'UTF-8')) ?></h3>
                <p class="servicios__card-text"><?= htmlspecialchars(content_get('servicios', 'serv2_desc', 'Conoce nuestras instalaciones, aulas y laboratorios de forma interactiva.'), ENT_QUOTES, 'UTF-8') ?></p>
                <a href="#" class="btn--outline-servicios">
                    Ver Tour
                    <span class="btn__icon-servicios"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 2 -->
            <div class="servicios__card-simple">
                <h3 class="servicios__card-title"><?= nl2br(htmlspecialchars(content_get('servicios', 'serv3_titulo', "Horarios y\nClases"), ENT_QUOTES, 'UTF-8')) ?></h3>
                <p class="servicios__card-text"><?= htmlspecialchars(content_get('servicios', 'serv3_desc', 'Consulta turnos presenciales, nocturnos y de fin de semana.'), ENT_QUOTES, 'UTF-8') ?></p>
                <a href="#" class="btn--outline-servicios">
                    Ver Horarios
                    <span class="btn__icon-servicios"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 3 -->
            <div class="servicios__card-simple">
                <h3 class="servicios__card-title"><?= nl2br(htmlspecialchars(content_get('servicios', 'serv4_titulo', "Servicios\nDigitales"), ENT_QUOTES, 'UTF-8')) ?></h3>
                <p class="servicios__card-text"><?= htmlspecialchars(content_get('servicios', 'serv4_desc', 'Accede al Aula Virtual, App Móvil y herramientas académicas.'), ENT_QUOTES, 'UTF-8') ?></p>
                <a href="#" class="btn--outline-servicios">
                    Acceder
                    <span class="btn__icon-servicios"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 4 -->
            <div class="servicios__card-simple">
                <h3 class="servicios__card-title"><?= nl2br(htmlspecialchars(content_get('servicios', 'serv5_titulo', "#Podcast\nITB"), ENT_QUOTES, 'UTF-8')) ?></h3>
                <p class="servicios__card-text"><?= htmlspecialchars(content_get('servicios', 'serv5_desc', 'Historias de éxito y consejos de docentes y graduados.'), ENT_QUOTES, 'UTF-8') ?></p>
                <a href="#" class="btn--outline-servicios">
                    Escuchar
                    <span class="btn__icon-servicios"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 5 (Imagen de fondo) -->
            <div class="servicios__card-image">
                <img src="<?= htmlspecialchars(content_raw('servicios', 'serv6_imagen', 'img/estudiantes1.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Arte y Deportes">
                <div class="servicios__card-overlay"></div>
                <div class="servicios__card-content">
                    <h3 class="servicios__card-title-white"><?= nl2br(htmlspecialchars(content_get('servicios', 'serv6_titulo', "Arte y\nDeportes"), ENT_QUOTES, 'UTF-8')) ?></h3>
                    <p class="servicios__card-text-white"><?= htmlspecialchars(content_get('servicios', 'serv6_desc', 'Participa en grupos culturales, eventos y torneos.'), ENT_QUOTES, 'UTF-8') ?></p>
                    <a href="#" class="btn--solid">
                        Conocer Más
                        <span class="btn__icon-right-white"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </div>

        </div>

    </div>

        </div>
    </div>
</section>
