<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('autoridades')) return; ?>
<!-- ============================================= -->
<!-- NUESTRAS AUTORIDADES                          -->
<!-- ============================================= -->
<section class="autoridades" id="autoridades">
    <div class="autoridades__container">

        <!-- Header -->
        <div class="autoridades__header">
            <div class="autoridades__header-left">
                <span class="autoridades__tag"><?= content_get('autoridades', 'etiqueta_superior', 'Liderazgo Institucional') ?></span>
                <h2 class="autoridades__title"><?= content_get('autoridades', 'titulo', 'Nuestras Autoridades') ?></h2>
                <p class="autoridades__subtitle"><?= content_get('autoridades', 'descripcion', 'Profesionales comprometidos con la excelencia académica, la innovación educativa y la gestión transparente de nuestra comunidad universitaria.') ?></p>
            </div>
            <a href="#" class="btn--outline-directorio" id="btn-directorio">
                <?= content_get('autoridades', 'boton_directorio', 'Ver Directorio') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Grid de cards -->
        <div class="autoridades__grid">
            <?php
            $lista_autoridades = content_raw('autoridades', 'lista_autoridades', [
                [
                    'nombre' => 'PhD. Roberto Tolozano Benites',
                    'cargo' => 'Canciller',
                    'imagen' => 'img/PHD.Roberto_tolozano.jpg'
                ],
                [
                    'nombre' => 'PhD. Elena Tolozano Benites',
                    'cargo' => 'Rectora',
                    'imagen' => 'img/PHD.Elena_Tolozano.jpg'
                ],
                [
                    'nombre' => 'PhD. Luis Alzate Peralta',
                    'cargo' => 'Vicerrector Académico<br>y de Investigación',
                    'imagen' => 'img/PHD.Luis_alzate.jpg'
                ],
                [
                    'nombre' => 'PhD. Michelle Tolozano Lapierre',
                    'cargo' => 'Vicerrectora de Extensión<br>y Gestión Administrativa',
                    'imagen' => 'img/PHD.Michelle_tolozano.webp'
                ]
            ]);

            foreach ((array)$lista_autoridades as $auth): 
                $foto_path = trim($auth['imagen'] ?? '');
                if (empty($foto_path)) $foto_path = 'img/placeholder_autoridad.jpg';
                $nombre = htmlspecialchars($auth['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
                $cargo = nl2br(htmlspecialchars($auth['cargo'] ?? '', ENT_QUOTES, 'UTF-8'));
            ?>
            <div class="autoridades__card">
                <div class="autoridades__card-img">
                    <img src="<?= htmlspecialchars($foto_path, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $nombre ?>">
                    <div class="autoridades__card-actions">
                        <div class="autoridades__card-socials">
                            <a href="#" aria-label="Correo"><img src="img/correo-electronico.png" alt="Correo"></a>
                            <a href="#" aria-label="Teléfono"><img src="img/telefono-fijo.png" alt="Teléfono"></a>
                        </div>
                        <button class="autoridades__card-plus" aria-label="Ver perfil">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="autoridades__card-body">
                    <h3 class="autoridades__card-name"><?= $nombre ?></h3>
                    <span class="autoridades__card-role"><?= $cargo ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
