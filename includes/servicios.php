<!-- ============================================= -->
<!-- GRILLA DE SERVICIOS / BIENESTAR (BENTO)       -->
<!-- ============================================= -->
<section class="servicios" id="servicios">
    <div class="servicios__container">
        <div class="servicios__header">
            <span class="section-tag"><?= content_get('servicios', 'etiqueta_superior', 'Servicios Institucionales') ?></span>
            <h2 class="servicios__title">
                <?= content_get('servicios', 'titulo_seccion_1', 'Todo lo que necesitas en ') ?><span class="text-orange"><?= content_get('servicios', 'titulo_seccion_2', 'un solo lugar') ?></span>
            </h2>
        </div>

        <div class="servicios__bento">
            <!-- Bloque grande: Bienestar Estudiantil -->
            <a href="#" class="servicios__card servicios__card--large" id="card-bienestar">
                <img src="<?= content_raw('servicios', 'serv1_imagen', 'img/servicio-bienestar.jpg') ?>" alt="<?= content_get('servicios', 'serv1_titulo', 'Bienestar Estudiantil') ?>" class="servicios__card-bg">
                <div class="servicios__card-overlay"></div>
                <div class="servicios__card-content">
                    <span class="servicios__card-icon"><i class="fas fa-heart"></i></span>
                    <h3 class="servicios__card-title"><?= content_get('servicios', 'serv1_titulo', 'Bienestar Estudiantil') ?></h3>
                    <p class="servicios__card-desc"><?= content_get('servicios', 'serv1_desc', 'Servicios médicos, psicológicos y odontológicos gratuitos') ?></p>
                </div>
            </a>

            <!-- Bloque: Campus Virtual -->
            <a href="#" class="servicios__card" id="card-campus">
                <img src="<?= content_raw('servicios', 'serv2_imagen', 'img/servicio-campus.jpg') ?>" alt="<?= content_get('servicios', 'serv2_titulo', 'Campus Virtual') ?>" class="servicios__card-bg">
                <div class="servicios__card-overlay"></div>
                <div class="servicios__card-content">
                    <span class="servicios__card-icon"><i class="fas fa-laptop"></i></span>
                    <h3 class="servicios__card-title"><?= content_get('servicios', 'serv2_titulo', 'Campus Virtual') ?></h3>
                    <p class="servicios__card-desc"><?= content_get('servicios', 'serv2_desc', 'Plataforma educativa 24/7') ?></p>
                </div>
            </a>

            <!-- Bloque: Horarios -->
            <a href="#" class="servicios__card" id="card-horarios">
                <img src="<?= content_raw('servicios', 'serv3_imagen', 'img/servicio-horarios.jpg') ?>" alt="<?= content_get('servicios', 'serv3_titulo', 'Horarios') ?>" class="servicios__card-bg">
                <div class="servicios__card-overlay"></div>
                <div class="servicios__card-content">
                    <span class="servicios__card-icon"><i class="fas fa-calendar-check"></i></span>
                    <h3 class="servicios__card-title"><?= content_get('servicios', 'serv3_titulo', 'Horarios') ?></h3>
                    <p class="servicios__card-desc"><?= content_get('servicios', 'serv3_desc', 'Consulta tus horarios de clase') ?></p>
                </div>
            </a>

            <!-- Bloque: Servicios Digitales -->
            <a href="#" class="servicios__card" id="card-digitales">
                <img src="<?= content_raw('servicios', 'serv4_imagen', 'img/servicio-digital.jpg') ?>" alt="<?= content_get('servicios', 'serv4_titulo', 'Servicios Digitales') ?>" class="servicios__card-bg">
                <div class="servicios__card-overlay"></div>
                <div class="servicios__card-content">
                    <span class="servicios__card-icon"><i class="fas fa-cogs"></i></span>
                    <h3 class="servicios__card-title"><?= content_get('servicios', 'serv4_titulo', 'Servicios Digitales') ?></h3>
                    <p class="servicios__card-desc"><?= content_get('servicios', 'serv4_desc', 'Trámites en línea y gestión académica') ?></p>
                </div>
            </a>

            <!-- Bloque: Podcast ITB -->
            <a href="#" class="servicios__card" id="card-podcast">
                <img src="<?= content_raw('servicios', 'serv5_imagen', 'img/servicio-podcast.jpg') ?>" alt="<?= content_get('servicios', 'serv5_titulo', 'Podcast ITB') ?>" class="servicios__card-bg">
                <div class="servicios__card-overlay"></div>
                <div class="servicios__card-content">
                    <span class="servicios__card-icon"><i class="fas fa-microphone"></i></span>
                    <h3 class="servicios__card-title"><?= content_get('servicios', 'serv5_titulo', 'Podcast ITB') ?></h3>
                    <p class="servicios__card-desc"><?= content_get('servicios', 'serv5_desc', 'Escucha nuestro contenido educativo') ?></p>
                </div>
            </a>

            <!-- Bloque grande: Arte y Deportes -->
            <a href="#" class="servicios__card servicios__card--large" id="card-deportes">
                <img src="<?= content_raw('servicios', 'serv6_imagen', 'img/servicio-deportes.jpg') ?>" alt="<?= content_get('servicios', 'serv6_titulo', 'Arte y Deportes') ?>" class="servicios__card-bg">
                <div class="servicios__card-overlay"></div>
                <div class="servicios__card-content">
                    <span class="servicios__card-icon"><i class="fas fa-running"></i></span>
                    <h3 class="servicios__card-title"><?= content_get('servicios', 'serv6_titulo', 'Arte y Deportes') ?></h3>
                    <p class="servicios__card-desc"><?= content_get('servicios', 'serv6_desc', 'Clubes deportivos, grupos artísticos y actividades recreativas') ?></p>
                </div>
            </a>
        </div>
    </div>
</section>
