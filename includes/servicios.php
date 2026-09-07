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
                <?= htmlspecialchars(content_get('servicios', 'serv1_btn', 'Más servicios'), ENT_QUOTES, 'UTF-8') ?>
                <span class="btn__icon-right-white"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Tarjetas (Cards) -->
        <div class="servicios__cards">
            <?php
            $lista_servicios = content_raw('servicios', 'lista_servicios', [
                [
                    'titulo' => "Campus Virtual",
                    'desc' => "Plataforma educativa 24/7",
                    'btn_texto' => "Ver Tour",
                    'imagen' => ""
                ],
                [
                    'titulo' => "Horarios",
                    'desc' => "Consulta tus horarios de clase",
                    'btn_texto' => "Ver Horarios",
                    'imagen' => ""
                ],
                [
                    'titulo' => "Servicios Digitales",
                    'desc' => "Trámites en línea y gestión académica",
                    'btn_texto' => "Acceder",
                    'imagen' => ""
                ],
                [
                    'titulo' => "Podcast ITB",
                    'desc' => "Escucha nuestro contenido educativo",
                    'btn_texto' => "Escuchar",
                    'imagen' => ""
                ],
                [
                    'titulo' => "Arte y Deportes",
                    'desc' => "Clubes deportivos, grupos artísticos y actividades recreativas",
                    'btn_texto' => "Conocer Más",
                    'imagen' => "img/bienestar_estudiantil_1.png"
                ]
            ]);

            foreach ((array)$lista_servicios as $servicio): 
                $foto_path = trim($servicio['imagen'] ?? '');
                if (!empty($foto_path) && !content_image_exists($foto_path)) $foto_path = 'img/placeholder_imagen.svg';
                $titulo = nl2br(htmlspecialchars($servicio['titulo'] ?? '', ENT_QUOTES, 'UTF-8'));
                $desc = htmlspecialchars($servicio['desc'] ?? '', ENT_QUOTES, 'UTF-8');
                $btn_texto = htmlspecialchars($servicio['btn_texto'] ?? 'Ver más', ENT_QUOTES, 'UTF-8');

                // Regla de imagen: Si NO hay imagen, usamos diseño simple. Si la hay, diseño con fondo.
                if (empty($foto_path)): 
            ?>
                <!-- Tarjeta Simple (Sin Imagen) -->
                <div class="servicios__card-simple">
                    <h3 class="servicios__card-title"><?= $titulo ?></h3>
                    <p class="servicios__card-text"><?= $desc ?></p>
                    <a href="#" class="btn--outline-servicios">
                        <?= $btn_texto ?>
                        <span class="btn__icon-servicios"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            <?php else: ?>
                <!-- Tarjeta con Imagen de Fondo -->
                <div class="servicios__card-image">
                    <img src="<?= htmlspecialchars($foto_path, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars(strip_tags($titulo), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="servicios__card-overlay"></div>
                    <div class="servicios__card-content">
                        <h3 class="servicios__card-title-white"><?= $titulo ?></h3>
                        <p class="servicios__card-text-white"><?= $desc ?></p>
                        <a href="#" class="btn--solid">
                            <?= $btn_texto ?>
                            <span class="btn__icon-right-white"><i class="fas fa-arrow-right"></i></span>
                        </a>
                    </div>
                </div>
            <?php 
                endif; 
            endforeach; 
            ?>

        </div>

    </div>

        </div>
    </div>
</section>
