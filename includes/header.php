<?php /* includes/header.php */ ?>
<!-- ============================================= -->
<!-- SITE HEADER (Tarjeta flotante sticky)         -->
<!-- ============================================= -->
<div class="site-header" id="site-header">

    <!-- ============================================= -->
    <!-- BARRA SUPERIOR (Top Bar)                      -->
    <!-- ============================================= -->
    <div class="top-bar">
        <div class="top-bar__container">
            <nav class="top-bar__nav">
                <a href="#" class="top-bar__link"><?= content_get('menu', 'top_link_1', 'Portal Estudiantil SGA') ?></a>
                <a href="#" class="top-bar__link"><?= content_get('menu', 'top_link_2', 'Educación Continua') ?></a>
                <a href="#" class="top-bar__link"><?= content_get('menu', 'top_link_3', 'Vinculación') ?></a>
                <a href="#" class="top-bar__link"><?= content_get('menu', 'top_link_4', 'Investigación') ?></a>
                <a href="#" class="top-bar__link"><?= content_get('menu', 'top_link_5', 'UNIEBEC') ?></a>
            </nav>
            <div class="top-bar__actions">
                <div class="top-bar__search top-bar__search--light">
                    <i class="fas fa-search top-bar__search-icon-left"></i>
                    <input type="text" placeholder="" class="top-bar__search-input" id="search-input">
                    <button class="top-bar__search-btn" id="search-btn" aria-label="Dictar">
                        <i class="fas fa-microphone"></i>
                    </button>
                </div>
                <button class="top-bar__icon-btn top-bar__icon-btn--orange" id="accessibility-btn"
                    aria-label="Accesibilidad">
                    <i class="fas fa-wheelchair"></i>
                </button>
                <button class="top-bar__lang-btn" id="lang-toggle">
                    <i class="fas fa-globe"></i> EN
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================= -->
    <!-- NAVBAR PRINCIPAL                              -->
    <!-- ============================================= -->
    <header class="navbar" id="navbar">
        <div class="navbar__container">
            <!-- Logo -->
            <a href="index.php" class="navbar__logo">
                <img src="<?= content_raw('ajustes', 'logo_principal', 'img/logo.png') ?>" alt="ITB - Instituto Superior Tecnológico Bolivariano" class="navbar__logo-img">
            </a>

            <!-- Botón hamburguesa (Mobile) -->
            <button class="navbar__toggle" id="navbar-toggle" aria-label="Abrir menú">
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
            </button>

            <!-- Menú de navegación -->
            <nav class="navbar__menu" id="navbar-menu">
                <!-- Encabezado exclusivo del cajón móvil -->
                <div class="navbar__mobile-header">
                    <span class="navbar__mobile-title">Menú Institucional</span>
                    <button class="navbar__mobile-close" id="navbar-close" aria-label="Cerrar menú">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <ul class="navbar__list">
                    <?php
                    $items_menu = content_raw('menu', 'items_menu', []);
                    if (empty($items_menu) || !is_array($items_menu)) {
                        $items_menu = [
                            ['texto' => 'Instituto', 'url' => '#', 'nivel' => 'padre'],
                            ['texto' => 'Sobre Nosotros', 'url' => 'sobre-nosotros.php', 'nivel' => 'hijo'],
                            ['texto' => 'Himno e Identidad', 'url' => '#', 'nivel' => 'hijo'],
                            ['texto' => 'Transparencia / Leyes', 'url' => 'transparencia-leyes.php', 'nivel' => 'hijo'],
                            ['texto' => 'Oferta Académica', 'url' => 'oferta-academica.php', 'nivel' => 'padre'],
                            ['texto' => 'Vida Estudiantil', 'url' => '#', 'nivel' => 'padre'],
                            ['texto' => 'Admisiones', 'url' => '#', 'nivel' => 'padre'],
                        ];
                    }
                    
                    // Arma la URL de un item del menú (con el caso especial de
                    // "Sobre Nosotros" sin URL propia, heredado de antes).
                    if (!function_exists('navbar_menu_url')) {
                        function navbar_menu_url(array $item): string {
                            $raw_url = trim($item['url'] ?? '');
                            $texto_normalizado = function_exists('mb_strtolower')
                                ? mb_strtolower(trim($item['texto'] ?? ''))
                                : strtolower(trim($item['texto'] ?? ''));
                            if ($raw_url === '' && $texto_normalizado === 'sobre nosotros') {
                                $raw_url = 'sobre-nosotros.php';
                            }
                            return htmlspecialchars($raw_url !== '' ? $raw_url : '#', ENT_QUOTES, 'UTF-8');
                        }
                    }

                    // 'nivel' venia como texto fijo ('padre'/'hijo'/'nieto'). Ahora
                    // es un numero de profundidad (0, 1, 2, ...) sin techo, pero
                    // esto sigue leyendo el formato viejo para no romper menus
                    // guardados antes de este cambio.
                    if (!function_exists('navbar_menu_profundidad')) {
                        function navbar_menu_profundidad($nivel): int {
                            if (is_numeric($nivel)) return max(0, (int) $nivel);
                            $legado = ['padre' => 0, 'hijo' => 1, 'nieto' => 2];
                            return $legado[$nivel] ?? 0;
                        }
                    }

                    // Convierte la lista plana (cada item con su profundidad) en un
                    // arbol real, sin importar cuantos niveles tenga: cada nodo trae
                    // sus hijos directos adentro de 'children'. Si un item viene con
                    // una profundidad mayor a la que le corresponde (datos raros,
                    // editados a mano, etc.) se recorta a la mas profunda valida en
                    // vez de perderlo — antes esos items simplemente desaparecian.
                    if (!function_exists('navbar_menu_armar_arbol')) {
                        function navbar_menu_armar_arbol(array $items): array {
                            $raiz = [];
                            $pila = [];
                            $pila[0] = &$raiz;
                            foreach ($items as $item) {
                                $texto = trim($item['texto'] ?? '');
                                if ($texto === '') continue;
                                $prof = navbar_menu_profundidad($item['nivel'] ?? 0);
                                if ($prof > count($pila) - 1) $prof = count($pila) - 1;

                                $nodo = $item;
                                $nodo['children'] = [];
                                $pila[$prof][] = $nodo;

                                for ($k = count($pila) - 1; $k > $prof; $k--) {
                                    unset($pila[$k]);
                                }
                                $ultimo = &$pila[$prof][count($pila[$prof]) - 1];
                                $pila[$prof + 1] = &$ultimo['children'];
                            }
                            return $raiz;
                        }
                    }

                    // Pinta la lista de <li>: en el nivel 0 son los items del menu
                    // principal (con flecha hacia abajo si tienen hijos); de ahi
                    // para adentro cada nivel se abre como un flyout hacia la
                    // derecha del anterior (misma clase para cualquier profundidad,
                    // por eso no hace falta un caso especial por nivel).
                    if (!function_exists('navbar_menu_pintar')) {
                        function navbar_menu_pintar(array $nodos, int $profundidad = 0): string {
                            $html = '';
                            foreach ($nodos as $nodo) {
                                $texto = htmlspecialchars($nodo['texto'] ?? '', ENT_QUOTES, 'UTF-8');
                                if ($texto === '') continue;
                                $url = navbar_menu_url($nodo);
                                $hijos = $nodo['children'] ?? [];

                                if ($profundidad === 0) {
                                    if (count($hijos) > 0) {
                                        $html .= '<li class="navbar__item navbar__item--dropdown">';
                                        $html .= '<a href="' . $url . '" class="navbar__link">' . $texto . ' <i class="fas fa-chevron-down navbar__dropdown-icon"></i></a>';
                                        $html .= '<ul class="navbar__dropdown">' . navbar_menu_pintar($hijos, $profundidad + 1) . '</ul>';
                                        $html .= '</li>';
                                    } else {
                                        $html .= '<li class="navbar__item"><a href="' . $url . '" class="navbar__link">' . $texto . '</a></li>';
                                    }
                                } else {
                                    if (count($hijos) > 0) {
                                        $html .= '<li class="navbar__dropdown-item--sub">';
                                        $html .= '<a href="' . $url . '" class="navbar__dropdown-link">' . $texto . ' <i class="fas fa-chevron-right navbar__dropdown-subicon"></i></a>';
                                        $html .= '<ul class="navbar__dropdown navbar__dropdown--sub">' . navbar_menu_pintar($hijos, $profundidad + 1) . '</ul>';
                                        $html .= '</li>';
                                    } else {
                                        $html .= '<li><a href="' . $url . '" class="navbar__dropdown-link">' . $texto . '</a></li>';
                                    }
                                }
                            }
                            return $html;
                        }
                    }

                    echo navbar_menu_pintar(navbar_menu_armar_arbol($items_menu));
                    ?>
                </ul>

                <!-- Accesos rápidos para móviles (Enlaces de la barra superior) -->
                <div class="navbar__mobile-quicklinks">
                    <span class="navbar__mobile-section-title">Portales y Enlaces</span>
                    <div class="navbar__mobile-quicklinks-grid">
                        <a href="#" class="navbar__mobile-quicklink"><i class="fas fa-user-graduate"></i> <?= content_get('menu', 'top_link_1', 'Portal Estudiantil SGA') ?></a>
                        <a href="#" class="navbar__mobile-quicklink"><i class="fas fa-graduation-cap"></i> <?= content_get('menu', 'top_link_2', 'Educación Continua') ?></a>
                        <a href="#" class="navbar__mobile-quicklink"><i class="fas fa-handshake"></i> <?= content_get('menu', 'top_link_3', 'Vinculación') ?></a>
                        <a href="#" class="navbar__mobile-quicklink"><i class="fas fa-flask"></i> <?= content_get('menu', 'top_link_4', 'Investigación') ?></a>
                        <a href="#" class="navbar__mobile-quicklink"><i class="fas fa-network-wired"></i> <?= content_get('menu', 'top_link_5', 'UNIEBEC') ?></a>
                    </div>
                </div>

                <!-- Botones CTA -->
                <div class="navbar__cta">
                    <a href="#" class="navbar__btn navbar__btn--outline" id="btn-solicitar"><?= content_get('menu', 'cta_btn_1', 'Solicitar Información') ?></a>
                    <a href="#" class="navbar__btn btn btn--solid" id="btn-matricula">
                        <?= content_get('menu', 'cta_btn_2', 'Matricúlame') ?>
                        <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </nav>
        </div>
    </header>
    <!-- Fondo oscuro difuminado al abrir menú móvil -->
    <div class="navbar__backdrop" id="navbar-backdrop"></div>
</div>
