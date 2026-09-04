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
                <a href="#" class="top-bar__link top-bar__link--active"><?= content_get('header', 'top_link_1', 'Portal Estudiantil SGA') ?></a>
                <a href="#" class="top-bar__link"><?= content_get('header', 'top_link_2', 'Educación Continua') ?></a>
                <a href="#" class="top-bar__link"><?= content_get('header', 'top_link_3', 'Vinculación') ?></a>
                <a href="#" class="top-bar__link"><?= content_get('header', 'top_link_4', 'Investigación') ?></a>
                <a href="#" class="top-bar__link"><?= content_get('header', 'top_link_5', 'UNIEBEC') ?></a>
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
                <img src="<?= content_raw('header', 'logo', 'img/logo.png') ?>" alt="ITB - Instituto Superior Tecnológico Bolivariano" class="navbar__logo-img">
            </a>

            <!-- Botón hamburguesa (Mobile) -->
            <button class="navbar__toggle" id="navbar-toggle" aria-label="Abrir menú">
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
            </button>

            <!-- Menú de navegación -->
            <nav class="navbar__menu" id="navbar-menu">
                <ul class="navbar__list">
                    <li class="navbar__item navbar__item--dropdown">
                        <a href="#" class="navbar__link" id="dropdown-instituto">
                            <?= content_get('header', 'nav_link_1', 'Instituto') ?> <i class="fas fa-chevron-down navbar__dropdown-icon"></i>
                        </a>

                    </li>
                    <li class="navbar__item">
                        <a href="#" class="navbar__link"><?= content_get('header', 'nav_link_2', 'Oferta Académica') ?></a>
                    </li>
                    <li class="navbar__item navbar__item--dropdown">
                        <a href="#" class="navbar__link" id="dropdown-vida">
                            <?= content_get('header', 'nav_link_3', 'Vida Estudiantil') ?> <i class="fas fa-chevron-down navbar__dropdown-icon"></i>
                        </a>

                    </li>
                    <li class="navbar__item">
                        <a href="#" class="navbar__link"><?= content_get('header', 'nav_link_4', 'Admisiones') ?></a>
                    </li>
                </ul>

                <!-- Botones CTA -->
                <div class="navbar__cta">
                    <a href="#" class="navbar__btn navbar__btn--outline" id="btn-solicitar"><?= content_get('header', 'cta_btn_1', 'Solicitar Información') ?></a>
                    <a href="#" class="navbar__btn navbar__btn--solid" id="btn-matricula">
                        <?= content_get('header', 'cta_btn_2', 'Matricúlame') ?>
                        <span class="navbar__btn-icon"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </nav>
        </div>
    </header>
</div>
