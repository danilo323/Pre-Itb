<?php /* includes/footer.php */ ?>
<!-- ============================================= -->
<!-- BANNER PRE-FOOTER                             -->
<!-- ============================================= -->
<div class="prefooter" id="prefooter">
    <div class="prefooter__container">
        <div class="prefooter__content">
            <h3 class="prefooter__title"><?= content_get('footer', 'prefooter_titulo', '¿Aún no decides qué carrera estudiar?') ?></h3>
            <p class="prefooter__text"><?= content_get('footer', 'prefooter_texto', 'Te ayudamos a encontrar la carrera ideal para ti') ?></p>
        </div>
        <div class="prefooter__buttons">
            <a href="#" class="prefooter__btn">
                <i class="fas fa-comments"></i> Chatea con nosotros
            </a>
            <a href="#" class="prefooter__btn">
                <i class="fas fa-phone-alt"></i> Llámanos
            </a>
            <a href="#" class="prefooter__btn">
                <i class="fas fa-map-marker-alt"></i> Visítanos
            </a>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- FOOTER                                        -->
<!-- ============================================= -->
<footer class="footer" id="footer">
    <div class="footer__container">
        <div class="footer__grid">
            <!-- Columna 1: Logo y descripción -->
            <div class="footer__col">
                <a href="index.php" class="footer__logo">
                    <img src="<?= content_raw('footer', 'logo_blanco', 'img/logo-itb-white.png') ?>" alt="ITB Logo" class="footer__logo-img">
                </a>
                <p class="footer__description">
                    <?= content_get('footer', 'descripcion', 'Instituto Superior Tecnológico Bolivariano de Tecnología. Formando profesionales de excelencia desde 1995.') ?>
                </p>
                <div class="footer__social">
                    <a href="<?= content_raw('footer', 'facebook_url', '#') ?>" class="footer__social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= content_raw('footer', 'instagram_url', '#') ?>" class="footer__social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="<?= content_raw('footer', 'twitter_url', '#') ?>" class="footer__social-link" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                    <a href="<?= content_raw('footer', 'youtube_url', '#') ?>" class="footer__social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="<?= content_raw('footer', 'linkedin_url', '#') ?>" class="footer__social-link" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Columna 2: Enlaces rápidos -->
            <div class="footer__col">
                <h4 class="footer__heading">Enlaces Rápidos</h4>
                <ul class="footer__list">
                    <li><a href="#" class="footer__link">Oferta Académica</a></li>
                    <li><a href="#" class="footer__link">Admisiones</a></li>
                    <li><a href="#" class="footer__link">Vida Estudiantil</a></li>
                    <li><a href="#" class="footer__link">Investigación</a></li>
                    <li><a href="#" class="footer__link">Educación Continua</a></li>
                </ul>
            </div>

            <!-- Columna 3: Contacto -->
            <div class="footer__col">
                <h4 class="footer__heading">Contacto</h4>
                <ul class="footer__list footer__list--contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= content_get('footer', 'direccion', 'Víctor Manuel Rendón 236 y Pedro Carbo, Guayaquil') ?></span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span><?= content_get('footer', 'telefono', '(04) 2-566-800') ?></span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span><?= content_get('footer', 'email', 'info@bolivariano.edu.ec') ?></span>
                    </li>
                </ul>
            </div>

            <!-- Columna 4: Horarios -->
            <div class="footer__col">
                <h4 class="footer__heading">Horarios de Atención</h4>
                <ul class="footer__list">
                    <li><?= content_get('footer', 'horario_semana', 'Lunes a Viernes: 08:00 - 17:00') ?></li>
                    <li><?= content_get('footer', 'horario_sabado', 'Sábados: 08:00 - 13:00') ?></li>
                </ul>
            </div>
        </div>

        <!-- Línea divisora y copyright -->
        <div class="footer__bottom">
            <p class="footer__copyright">
                &copy; <?php echo date('Y'); ?> Instituto Superior Tecnológico Bolivariano de Tecnología. Todos los derechos reservados.
            </p>
            <div class="footer__bottom-links">
                <a href="#" class="footer__bottom-link">Política de Privacidad</a>
                <span class="footer__bottom-divider">|</span>
                <a href="#" class="footer__bottom-link">Términos de Uso</a>
            </div>
        </div>
    </div>
</footer>
