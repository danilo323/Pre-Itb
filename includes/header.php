<?php /* includes/header.php */ ?>



<div class="site-header" id="site-header">

    
    
    
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

    
    
    
    <header class="navbar" id="navbar">
        <div class="navbar__container">
            
            <a href="index.php" class="navbar__logo">
                <img src="<?= content_raw('ajustes', 'logo_principal', 'img/logo.png') ?>" alt="ITB - Instituto Superior Tecnológico Bolivariano" class="navbar__logo-img">
            </a>

            
            <button class="navbar__toggle" id="navbar-toggle" aria-label="Abrir menú">
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
            </button>

            
            <nav class="navbar__menu" id="navbar-menu">
                <ul class="navbar__list">
                    <?php
                    $items_menu = content_raw('menu', 'items_menu', []);
                    if (empty($items_menu) || !is_array($items_menu)) {
                        $items_menu = [
                            ['texto' => 'Inicio', 'url' => '#', 'nivel' => 'padre'],
                            ['texto' => 'Nuestra Institución', 'url' => '#', 'nivel' => 'padre'],
                            ['texto' => 'Quienes Somos', 'url' => '#', 'nivel' => 'hijo'],
                            ['texto' => 'Misión y Visión', 'url' => '#', 'nivel' => 'hijo'],
                            ['texto' => 'Valores Institucionales', 'url' => '#', 'nivel' => 'hijo'],
                            ['texto' => 'Autoridades', 'url' => '#', 'nivel' => 'hijo'],
                            ['texto' => 'Preguntas Frecuentes', 'url' => '#', 'nivel' => 'hijo'],
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
                        $url = htmlspecialchars($item['url'] ?? '#', ENT_QUOTES, 'UTF-8');
                        
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
                                $c_url = htmlspecialchars($child['url'] ?? '#', ENT_QUOTES, 'UTF-8');
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
</div>
