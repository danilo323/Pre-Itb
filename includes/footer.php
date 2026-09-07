<?php /* includes/footer.php */ ?>
<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; ?>
<!-- ============================================= -->
<!-- BANNER CTA PRE-FOOTER                         -->
<!-- ============================================= -->
<div class="footer-cta" id="footer-cta">
    <div class="footer-cta__container">
        <div class="footer-cta__left">
            <h3 class="footer-cta__title"><?= content_get('footer', 'cta_titulo', '¿Aún no decides qué carrera estudiar?') ?></h3>
            <p class="footer-cta__desc"><?= content_get('footer', 'cta_desc', 'Descubre tu vocación con nuestro test guiado, visita el ITB y conoce de cerca nuestra propuesta académica o recibe asesoría personalizada para elegir el programa ideal para ti.') ?></p>
        </div>
        <div class="footer-cta__right">
            <a href="#" class="btn--outline-card">
                <?= content_get('footer', 'cta_btn_1', 'Test Vocacional') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="#" class="btn--outline-card">
                <?= content_get('footer', 'cta_btn_2', 'Vive la Experiencia ITB') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="#" class="btn--outline-card">
                <?= content_get('footer', 'cta_btn_3', 'Habla con un Asesor') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </div>
</div>

<!-- =============================================
     SKYLINE (transición hacia el footer)
     Reemplaza al SVG dibujado a mano: ahora son 3 archivos reales que trajo
     el usuario (svg/footer1.svg, footer2.svg, footer3.svg). Los tres comparten
     la misma paleta de azules del footer y estaban pensados para superponerse:
     footer2 = cordillera de fondo (capa trasera, a todo el ancho), footer1 =
     los edificios/monumentos sueltos (capa delantera, mismo lienzo de
     1920x759 que footer2, por eso encajan sin necesitar reposicionarlos), y
     footer3 = un monumento (arco) que no venía incluido en el dibujo grande,
     así que se coloca a mano en el hueco vacío que dejan los demás, a la
     misma escala (ver .footer-skyline__landmark en css/footer.css). -->
<div class="footer-skyline">
    <div class="footer-skyline__scene">
        <img src="svg/footer2.svg" alt="" class="footer-skyline__layer footer-skyline__layer--back" aria-hidden="true">
        <img src="svg/footer1.svg" alt="" class="footer-skyline__layer footer-skyline__layer--front" aria-hidden="true">
        <img src="svg/footer3.svg" alt="" class="footer-skyline__landmark" aria-hidden="true">
    </div>
</div>

<!-- MAIN FOOTER                                   -->
<!-- ============================================= -->
<footer class="footer-main" id="footer">
    <div class="footer-main__container">
        <div class="footer-main__grid">
            
            <!-- Columna 1: Logo y Teléfonos -->
            <div class="footer-main__col footer-main__col--logo">
                <a href="index.php" class="footer-main__logo-link">
                    <img src="<?= content_raw('footer', 'logo', 'img/logo-itb-white.png') ?>" alt="ITB Logo" class="footer-main__logo">
                </a>
                <div class="footer-main__contact">
                    <div class="footer-main__contact-item">
                        <i class="fas fa-desktop"></i>
                        <span><?= content_get('footer', 'contacto_1', "PBX: (04) 500 0175 - 230 7028\n500 2164 - 372 7040") ?></span>
                    </div>
                    <div class="footer-main__contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <span><?= content_get('footer', 'contacto_2', '1800 ITB-ITB: 482-482') ?></span>
                    </div>
                </div>
            </div>

            <!-- Columna 2: Enlaces 1 -->
            <div class="footer-main__col">
                <ul class="footer-main__list">
                    <?php
                    $enlaces1 = content_raw('footer', 'enlaces_columna_1', "Admisiones Pregrado\nCarreras y Programas\nCalendario Académico\nTalento Humano\nVinculación");
                    foreach (array_filter(array_map('trim', explode("\n", $enlaces1))) as $enlace):
                    ?>
                        <li><a href="#"><?= htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Columna 3: Enlaces 2 -->
            <div class="footer-main__col">
                <ul class="footer-main__list">
                    <?php
                    // "CONDUCE ECUADOR" ya NO se pinta de naranja a la fuerza: ese color era
                    // solo una guía en Figma para saber qué tono usar en el :hover de TODOS
                    // los enlaces del footer (ver .footer-main__list a:hover en footer.css).
                    // Antes había un if comparando el texto contra "CONDUCE ECUADOR" a mano,
                    // justo el tipo de lógica de negocio metida en un include que las reglas
                    // del proyecto piden evitar.
                    $enlaces2 = content_raw('footer', 'enlaces_columna_2', "Noticias y Novedades ITB\nDirectorio General\nASOMI\nCONDUCE ECUADOR\nTrabaja en el ITB");
                    foreach (array_filter(array_map('trim', explode("\n", $enlaces2))) as $enlace):
                    ?>
                        <li><a href="#"><?= htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Columna 4: Campus -->
            <div class="footer-main__col">
                <ul class="footer-main__list">
                    <?php
                    $campus_list = content_raw('footer', 'lista_campus', "Campus Matriz\nCampus Boyacá\nCampus Naval\nCampus Teresa Benites\nCampus Tomás Martínez");
                    foreach (array_filter(array_map('trim', explode("\n", $campus_list))) as $campus):
                    ?>
                        <li><a href="#"><?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Columna 5: Mapa -->
            <div class="footer-main__col footer-main__col--map">
                <div class="footer-main__map-wrapper">
                    <img src="<?= content_raw('footer', 'mapa_img', 'img/Mapa.png') ?>" alt="Mapa de ubicación" class="footer-main__map-img">
                </div>
                <div class="footer-main__address-pill">
                    <?= content_get('footer', 'direccion_mapa', 'Roca #101 y Pedro Carbo esq.') ?>
                </div>
            </div>

        </div>

        <!-- Redes Sociales -->
        <div class="footer-social">
            <span class="footer-social__text"><?= content_get('footer', 'social_texto', '#ITB Instituto Superior Universitario Bolivariano en') ?></span>
            <div class="footer-social__icons">
                <a href="<?= content_raw('footer', 'youtube_url', '#') ?>"><i class="fab fa-youtube"></i></a>
                <a href="<?= content_raw('footer', 'instagram_url', '#') ?>"><i class="fab fa-instagram"></i></a>
                <a href="<?= content_raw('footer', 'facebook_url', '#') ?>"><i class="fab fa-facebook-f"></i></a>
                <a href="<?= content_raw('footer', 'gplus_url', '#') ?>"><i class="fab fa-google-plus-g"></i></a>
                <a href="<?= content_raw('footer', 'twitter_url', '#') ?>"><i class="fab fa-twitter"></i></a>
                <a href="<?= content_raw('footer', 'vimeo_url', '#') ?>"><i class="fab fa-vimeo-v"></i></a>
                <a href="<?= content_raw('footer', 'linkedin_url', '#') ?>"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================= -->
<!-- BOTTOM BAR                                    -->
<!-- ============================================= -->
<div class="footer-bottom">
    <div class="footer-bottom__container">
        <div class="footer-bottom__left">
            <?= content_get('footer', 'copyright', '© 2026 TIC - ITB | TODOS LOS DERECHOS RESERVADOS') ?>
        </div>
        <div class="footer-bottom__right">
            <a href="#">POLÍTICA DE PRIVACIDAD</a> | 
            <a href="#">TRANSPARENCIA INSTITUCIONAL</a> | 
            <a href="#">ACCESIBILIDAD</a> | 
            <a href="#">GESTIÓN DE COOKIES</a>
        </div>
    </div>
</div>

<!-- Botón Ir Arriba -->
<div id="top-to-bottom" class="js-floating">
    <i class="fas fa-angles-up"></i>
</div>

<!-- Botón flotante WhatsApp -->
<div class="whatsapp-float js-floating" id="whatsapp-float">
    <a href="https://wa.me/593XXXXXXXXX?text=Hola%2C%20tengo%20una%20pregunta%20sobre%20el%20ITB" 
       target="_blank" 
       class="whatsapp-float__link" 
       aria-label="Chatea con ITBChat por WhatsApp">
        <div class="whatsapp-float__label">
            <span>¿Tienes preguntas? Pregunta a ITB Chat</span>
        </div>
        <div class="whatsapp-float__icon">
            <i class="fab fa-whatsapp"></i>
        </div>
    </a>
</div>
