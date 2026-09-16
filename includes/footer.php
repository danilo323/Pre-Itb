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

<!-- MAIN FOOTER                                   -->
<!-- ============================================= -->
<footer class="footer-main" id="footer">
    <div class="footer-main__container">
        <div class="footer-main__grid">
            
            <!-- Columna 1: Logo y Teléfonos -->
            <div class="footer-main__col footer-main__col--logo">
                <a href="index.php" class="footer-main__logo-link">
                    <img src="<?= content_url('footer', 'logo', 'img/logo-itb-white.png') ?>" alt="ITB Logo" class="footer-main__logo">
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

            <?php /* Las tres columnas de enlaces van dentro de su propio bloque.
                     Asi la linea divisoria de cada una mide lo mismo que la lista
                     mas larga de las tres -y no lo que mide la fila entera, que la
                     estiraba muy por debajo del texto por culpa del mapa. */ ?>
            <div class="footer-main__links">

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

            <?php
            // ---- Campus y mapa ----
            // Antes el mapa era una imagen fija (img/Mapa.png) y la lista de
            // campus era texto suelto que no llevaba a ningún sitio. Ahora cada
            // campus trae su dirección desde el panel y, al pulsarlo, el mapa de
            // al lado se mueve hasta él.
            $campus_default = [
                ['nombre' => 'Campus Matriz', 'direccion' => 'Roca #101 y Pedro Carbo esq., Guayaquil, Ecuador', 'mapa_url' => '', 'ubicacion' => []],
            ];
            $campus_list = content_raw('footer', 'lista_campus', $campus_default);

            // Compatibilidad hacia atrás: hasta ahora esto era un textarea con un
            // campus por línea. Si alguien abre el sitio con esos datos todavía
            // sin migrar, se convierten al vuelo para no dejar la columna vacía.
            if (is_string($campus_list)) {
                $campus_list = array_map(function ($linea) {
                    return ['nombre' => trim($linea), 'direccion' => '', 'mapa_url' => ''];
                }, array_filter(array_map('trim', explode("\n", $campus_list))));
            }
            if (empty($campus_list) || !is_array($campus_list)) $campus_list = $campus_default;

            // Un campus sin nombre es un item a medio llenar en el panel.
            $campus_list = array_values(array_filter((array)$campus_list, function ($c) {
                return is_array($c) && trim($c['nombre'] ?? '') !== '';
            }));

            // Texto que se le añade al nombre del campus cuando todavía no tiene
            // dirección propia, para que el mapa sepa por dónde buscarlo. Sin
            // esto, "Campus Naval" a secas puede llevar a cualquier parte del
            // mundo; con esto, la búsqueda queda acotada a la institución.
            $contexto_mapa = trim((string)content_raw(
                'footer', 'mapa_contexto', 'Instituto Superior Tecnológico Bolivariano, Guayaquil, Ecuador'
            ));

            // Lo que se le pide al mapa para cada campus. Por orden de precisión:
            //   1. la dirección escrita en el panel (lo más exacto),
            //   2. si no hay, el nombre del campus + el contexto de arriba.
            // Así TODOS los campus se pueden pulsar desde el primer momento,
            // aunque al administrador todavía le falte escribir direcciones.
            $busqueda_mapa = function (array $c) use ($contexto_mapa): string {
                $dir = trim($c['direccion'] ?? '');
                if ($dir !== '') return $dir;
                $nombre = trim($c['nombre'] ?? '');
                return $contexto_mapa !== '' ? $nombre . ', ' . $contexto_mapa : $nombre;
            };

            $url_mapa = function (array $c) use ($busqueda_mapa): string {
                $ubicacion = $c['ubicacion'] ?? [];
                if (is_array($ubicacion) && isset($ubicacion['lat'], $ubicacion['lng'])
                    && is_numeric($ubicacion['lat']) && is_numeric($ubicacion['lng'])) {
                    return 'https://www.google.com/maps?q=' . rawurlencode($ubicacion['lat'] . ',' . $ubicacion['lng']) . '&z=17&output=embed';
                }
                // Un enlace propio pegado desde Google Maps manda sobre todo lo demás.
                $propia = trim($c['mapa_url'] ?? '');
                if ($propia !== '') return $propia;
                $consulta = $busqueda_mapa($c);
                if ($consulta === '') return '';
                // Formato de inserción de Google Maps que no necesita clave de API.
                return 'https://www.google.com/maps?q=' . rawurlencode($consulta) . '&output=embed';
            };

            // Lo que se lee en la pastilla de debajo del mapa: la dirección si la
            // hay y, si no, el nombre del campus (no la consulta de búsqueda
            // completa, que incluye el contexto y quedaría redundante).
            $texto_pastilla = function (array $c): string {
                $dir = trim($c['direccion'] ?? '');
                return $dir !== '' ? $dir : trim($c['nombre'] ?? '');
            };

            // El mapa arranca en el primer campus de la lista.
            $campus_inicial = null;
            foreach ($campus_list as $c) {
                if ($url_mapa($c) !== '') { $campus_inicial = $c; break; }
            }
            ?>

            <!-- Columna 4: Campus -->
            <div class="footer-main__col">
                <ul class="footer-main__list footer-main__campus-list">
                    <?php foreach ($campus_list as $c):
                        $nombre = htmlspecialchars(trim($c['nombre']), ENT_QUOTES, 'UTF-8');
                        $dir    = htmlspecialchars($texto_pastilla($c), ENT_QUOTES, 'UTF-8');
                        $url    = htmlspecialchars($url_mapa($c), ENT_QUOTES, 'UTF-8');
                        $activo = ($campus_inicial !== null && $c === $campus_inicial);
                    ?>
                        <li>
                            <button type="button"
                                    class="footer-main__campus-btn js-campus<?= $activo ? ' is-active' : '' ?>"
                                    data-mapa="<?= $url ?>"
                                    data-direccion="<?= $dir ?>"
                                    aria-pressed="<?= $activo ? 'true' : 'false' ?>"><?= $nombre ?></button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            </div><!-- /.footer-main__links -->

            <!-- Columna 5: Mapa -->
            <div class="footer-main__col footer-main__col--map">
                <?php if ($campus_inicial !== null): ?>
                    <div class="footer-main__map-wrapper">
                        <iframe
                            id="footer-map"
                            class="footer-main__map-frame"
                            src="<?= htmlspecialchars($url_mapa($campus_inicial), ENT_QUOTES, 'UTF-8') ?>"
                            title="Mapa de ubicación de los campus del ITB"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen></iframe>
                    </div>
                    <div class="footer-main__address-pill" id="footer-map-direccion">
                        <?= htmlspecialchars($texto_pastilla($campus_inicial), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Redes Sociales -->
        <div class="footer-social">
            <span class="footer-social__text"><?= content_get('footer', 'social_texto', '#ITB Instituto Superior Universitario Bolivariano en') ?></span>
            <div class="footer-social__icons">
                <a href="<?= content_url('footer', 'youtube_url', '#') ?>"><i class="fab fa-youtube"></i></a>
                <a href="<?= content_url('footer', 'instagram_url', '#') ?>"><i class="fab fa-instagram"></i></a>
                <a href="<?= content_url('footer', 'facebook_url', '#') ?>"><i class="fab fa-facebook-f"></i></a>
                <a href="<?= content_url('footer', 'gplus_url', '#') ?>"><i class="fab fa-google-plus-g"></i></a>
                <a href="<?= content_url('footer', 'twitter_url', '#') ?>"><i class="fab fa-twitter"></i></a>
                <a href="<?= content_url('footer', 'vimeo_url', '#') ?>"><i class="fab fa-vimeo-v"></i></a>
                <a href="<?= content_url('footer', 'linkedin_url', '#') ?>"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- =============================================
     SKYLINE (base del pie)
     Los 3 SVG reales (svg/footer1.svg, footer2.svg, footer3.svg) que trajo
     el usuario: footer2 = cordillera de fondo, footer1 = los edificios y
     monumentos sueltos (mismo lienzo de 1920x759, por eso encajan sin
     reposicionar nada) y footer3 = un arco que no venia en el dibujo
     grande y se coloca a mano en el hueco que dejan los demas.
     Antes iba ARRIBA, entre la tarjeta CTA y las columnas. Ahora cierra el
     pie: los edificios se apoyan sobre la barra naranja de derechos, y el
     cielo de la franja arranca del mismo azul del pie (ver .footer-skyline
     en css/footer.css) para que no se vea ninguna costura. -->
<div class="footer-skyline">
    <div class="footer-skyline__scene">
        <img src="svg/footer2.svg" alt="" class="footer-skyline__layer footer-skyline__layer--back" aria-hidden="true">
        <img src="svg/footer1.svg" alt="" class="footer-skyline__layer footer-skyline__layer--front" aria-hidden="true">
        <img src="svg/footer3.svg" alt="" class="footer-skyline__landmark" aria-hidden="true">
    </div>
</div>

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

<?php
// Botón flotante de WhatsApp. El número, el texto de la burbuja y el mensaje
// con el que se abre el chat se editan en el panel (Pie de Página → Botón
// flotante de WhatsApp). Antes el número estaba escrito aquí como
// 593XXXXXXXXX, así que el enlace no llevaba a ninguna parte y sólo un
// programador podía cambiarlo.
//
// Sin número configurado no se pinta la burbuja: mejor eso que un botón que
// no funciona en todas las páginas del sitio.
$wa_numero = preg_replace('/\D+/', '', (string) content_raw('footer', 'whatsapp_numero', ''));
if ($wa_numero !== ''):
    $wa_texto   = content_get('footer', 'whatsapp_texto', '¿Tienes preguntas? Pregunta a ITB Chat');
    $wa_mensaje = (string) content_raw('footer', 'whatsapp_mensaje', 'Hola, tengo una pregunta sobre el ITB');
    $wa_url     = 'https://wa.me/' . $wa_numero . '?text=' . rawurlencode($wa_mensaje);
?>
<!-- Botón flotante WhatsApp -->
<div class="whatsapp-float js-floating" id="whatsapp-float">
    <a href="<?= htmlspecialchars($wa_url, ENT_QUOTES, 'UTF-8') ?>"
       target="_blank"
       rel="noopener"
       class="whatsapp-float__link"
       aria-label="Chatea con el ITB por WhatsApp">
        <div class="whatsapp-float__label">
            <span><?= $wa_texto ?></span>
        </div>
        <div class="whatsapp-float__icon">
            <i class="fab fa-whatsapp"></i>
        </div>
    </a>
</div>
<?php endif; ?>
