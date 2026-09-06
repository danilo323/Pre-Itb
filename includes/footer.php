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

<!-- ============================================= -->
<!-- SKYLINE TRANSITION                            -->
<!-- ============================================= -->
<div class="footer-skyline">
    <svg class="skyline-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 220" preserveAspectRatio="none">
        <!-- Capa trasera: colinas lejanas -->
        <path class="skyline-svg__back" d="M0,220 L0,150 Q120,110 260,140 T520,120 T760,150 T1020,110 T1280,150 L1440,130 L1440,220 Z"/>

        <!-- Capa frontal: edificios, rueda de la fortuna y torre -->
        <g class="skyline-svg__front">
            <!-- Torre con aguja (izquierda) -->
            <rect x="70" y="80" width="16" height="120" />
            <polygon points="62,80 94,80 78,45" />
            <rect x="75" y="30" width="6" height="18" />

            <!-- Edificios pequeños -->
            <rect x="20" y="150" width="34" height="50" />
            <rect x="110" y="130" width="30" height="70" />
            <rect x="150" y="160" width="26" height="40" />

            <!-- Rueda de la fortuna -->
            <circle cx="360" cy="140" r="58" class="skyline-svg__wheel-rim" />
            <circle cx="360" cy="140" r="7" />
            <line x1="360" y1="82" x2="360" y2="198" />
            <line x1="302" y1="140" x2="418" y2="140" />
            <line x1="319" y1="99" x2="401" y2="181" />
            <line x1="319" y1="181" x2="401" y2="99" />
            <rect x="352" y="196" width="16" height="4" />

            <!-- Edificios centrales -->
            <rect x="460" y="120" width="34" height="80" />
            <rect x="500" y="150" width="28" height="50" />
            <rect x="534" y="100" width="30" height="100" />

            <!-- Cúpula / templo -->
            <rect x="640" y="150" width="70" height="50" />
            <path d="M636,150 a39,39 0 0 1 78,0 Z" />
            <rect x="670" y="95" width="10" height="30" />
            <circle cx="675" cy="92" r="6" />

            <!-- Edificios derecha -->
            <rect x="760" y="140" width="30" height="60" />
            <rect x="796" y="110" width="34" height="90" />
            <rect x="836" y="155" width="26" height="45" />

            <!-- Árboles -->
            <g class="skyline-svg__trees">
                <line x1="920" y1="170" x2="920" y2="200" />
                <circle cx="920" cy="160" r="14" />
                <line x1="960" y1="175" x2="960" y2="200" />
                <circle cx="960" cy="166" r="11" />
            </g>

            <!-- Bloque de edificios final -->
            <rect x="1010" y="130" width="30" height="70" />
            <rect x="1046" y="160" width="26" height="40" />
            <rect x="1086" y="105" width="34" height="95" />
            <rect x="1130" y="145" width="28" height="55" />

            <!-- Obelisco -->
            <polygon points="1210,200 1226,200 1220,120 1216,120" />

            <rect x="1270" y="150" width="30" height="50" />
            <rect x="1306" y="170" width="26" height="30" />
            <rect x="1350" y="130" width="34" height="70" />
            <rect x="1394" y="165" width="26" height="35" />
        </g>
    </svg>
</div>

<!-- ============================================= -->
<!-- MAIN FOOTER                                   -->
<!-- ============================================= -->
<footer class="footer-main" id="footer">
    <div class="footer-main__container">
        <div class="footer-main__grid">
            
            <!-- Columna 1: Logo y Teléfonos -->
            <div class="footer-main__col footer-main__col--logo">
                <a href="index.php" class="footer-main__logo-link">
                    <img src="<?= content_raw('ajustes', 'logo_blanco', 'img/logo-itb-white.png') ?>" alt="ITB Logo" class="footer-main__logo">
                </a>
                <div class="footer-main__contact">
                    <div class="footer-main__contact-item">
                        <i class="fas fa-desktop"></i>
                        <span><?= content_get('footer', 'contacto_1', 'PBX: (04) 500 0175 - 230 7028<br>500 2164 - 372 7040') ?></span>
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
                    $enlaces2 = content_raw('footer', 'enlaces_columna_2', "Noticias y Novedades ITB\nDirectorio General\nASOMI\nCONDUCE ECUADOR\nTrabaja en el ITB");
                    foreach (array_filter(array_map('trim', explode("\n", $enlaces2))) as $enlace):
                        $class = (strtoupper($enlace) == 'CONDUCE ECUADOR') ? 'class="footer-main__link-orange"' : '';
                    ?>
                        <li><a href="#" <?= $class ?>><?= htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8') ?></a></li>
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
            <?= content_get('footer', 'copyright', '&copy; 2026 TIC - ITB | TODOS LOS DERECHOS RESERVADOS') ?>
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
<div id="top-to-bottom">
    <i class="fas fa-angles-up"></i>
</div>

<!-- Botón flotante WhatsApp -->
<div class="whatsapp-float" id="whatsapp-float">
    <a href="https://wa.me/593XXXXXXXXX?text=Hola%2C%20tengo%20una%20pregunta%20sobre%20el%20ITB" 
       target="_blank" 
       class="whatsapp-float__link" 
       aria-label="Chatea con ITBChat por WhatsApp">
        <div class="whatsapp-float__label">
            <span>¿Tienes preguntas? Pregunta a ITBChat</span>
        </div>
        <div class="whatsapp-float__icon">
            <i class="fab fa-whatsapp"></i>
        </div>
    </a>
</div>
