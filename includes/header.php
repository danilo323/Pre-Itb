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
                            ['texto' => 'Oferta Académica', 'url' => '#', 'nivel' => 'padre'],
                            ['texto' => 'Vida Estudiantil', 'url' => '#', 'nivel' => 'padre'],
                            ['texto' => 'Admisiones', 'url' => '#', 'nivel' => 'padre'],
                        ];
                    }
                    
                    $total = count($items_menu);
                    for ($i = 0; $i < $total; $i++) {
                        $item = $items_menu[$i];
                        $nivel = $item['nivel'] ?? 'padre';
                        
                        // Si el nivel es hijo aquí, lo ignoramos, ya que se procesa dentro de su padre
                        if ($nivel === 'hijo') continue;
                        
                        $texto = htmlspecialchars($item['texto'] ?? '', ENT_QUOTES, 'UTF-8');
                        $raw_url = trim($item['url'] ?? '');
                        if ($raw_url === '' && (function_exists('mb_strtolower') ? mb_strtolower(trim($item['texto'] ?? '')) : strtolower(trim($item['texto'] ?? ''))) === 'sobre nosotros') {
                            $raw_url = 'sobre-nosotros.php';
                        }
                        $url = htmlspecialchars($raw_url !== '' ? $raw_url : '#', ENT_QUOTES, 'UTF-8');
                        
                        // Buscar si los siguientes elementos son hijos de este padre
                        $children = [];
                        for ($j = $i + 1; $j < $total; $j++) {
                            if (($items_menu[$j]['nivel'] ?? 'padre') === 'hijo') {
                                $children[] = $items_menu[$j];
                            } else {
                                break;
                            }
                        }
                        
                        if (count($children) > 0) {
                            // Renderizar Padre con submenú (Dropdown)
                            echo '<li class="navbar__item navbar__item--dropdown">';
                            echo '<a href="' . $url . '" class="navbar__link">' . $texto . ' <i class="fas fa-chevron-down navbar__dropdown-icon"></i></a>';
                            echo '<ul class="navbar__dropdown">';
                            foreach ($children as $child) {
                                $c_texto = htmlspecialchars($child['texto'] ?? '', ENT_QUOTES, 'UTF-8');
                                $c_raw_url = trim($child['url'] ?? '');
                                if ($c_raw_url === '' && (function_exists('mb_strtolower') ? mb_strtolower(trim($child['texto'] ?? '')) : strtolower(trim($child['texto'] ?? ''))) === 'sobre nosotros') {
                                    $c_raw_url = 'sobre-nosotros.php';
                                }
                                $c_url = htmlspecialchars($c_raw_url !== '' ? $c_raw_url : '#', ENT_QUOTES, 'UTF-8');
                                if ($c_texto) {
                                    echo '<li><a href="' . $c_url . '" class="navbar__dropdown-link">' . $c_texto . '</a></li>';
                                }
                            }
                            echo '</ul>';
                            echo '</li>';
                        } else {
                            // Renderizar enlace normal (Sin hijos)
                            if ($texto) {
                                echo '<li class="navbar__item">';
                                echo '<a href="' . $url . '" class="navbar__link">' . $texto . '</a>';
                                echo '</li>';
                            }
                        }
                    }
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
