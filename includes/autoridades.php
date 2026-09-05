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
                <h2 class="autoridades__title">
                    <?= htmlspecialchars(content_get('autoridades', 'titulo', 'Nuestras Autoridades'), ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <p class="autoridades__subtitle">
                    <?= content_get('autoridades', 'descripcion', 'Profesionales comprometidos con la excelencia académica, la innovación educativa y la gestión transparente de nuestra comunidad universitaria.') ?>
                </p>
            </div>
            <a href="#" class="btn--outline-directorio" id="btn-directorio">
                <?= content_get('autoridades', 'btn_directorio', 'Ver Directorio') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Grid de cards -->
        <div class="autoridades__grid">
            <?php
            $equipo = collection_items('equipo');
            $equipo_filtrado = [];
            foreach ($equipo as $miembro) {
                if (!empty($miembro['mostrar_en_home']) && !empty($miembro['publicado'])) {
                    $equipo_filtrado[] = $miembro;
                }
            }

            // Ordenar por el campo 'orden'
            usort($equipo_filtrado, function($a, $b) {
                $orden_a = isset($a['orden']) ? (int)$a['orden'] : 999;
                $orden_b = isset($b['orden']) ? (int)$b['orden'] : 999;
                return $orden_a <=> $orden_b;
            });

            $count = 0;
            foreach ($equipo_filtrado as $miembro) {
                
                $foto = !empty($miembro['foto']) ? htmlspecialchars($miembro['foto'], ENT_QUOTES, 'UTF-8') : 'img/placeholder.jpg';
                $nombre = htmlspecialchars($miembro['nombre_completo'] ?? '', ENT_QUOTES, 'UTF-8');
                $cargo = nl2br(htmlspecialchars($miembro['cargo'] ?? '', ENT_QUOTES, 'UTF-8'));
                $linkedin = htmlspecialchars($miembro['linkedin'] ?? '#', ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars($miembro['email'] ?? '#', ENT_QUOTES, 'UTF-8');
                
                $count++;
            ?>
                <!-- Card <?= $count ?> -->
                <div class="autoridades__card">
                    <div class="autoridades__card-img">
                        <img src="<?= $foto ?>" alt="<?= $nombre ?>">
                        <div class="autoridades__card-actions">
                            <div class="autoridades__card-socials">
                                <a href="<?= $email ?>" aria-label="Correo"><img src="img/correo-electronico.png" alt="Correo"></a>
                                <a href="<?= $linkedin ?>" aria-label="Teléfono"><img src="img/telefono-fijo.png" alt="Teléfono"></a>
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
            <?php } ?>
            
            <?php if ($count === 0): ?>
                <p>No hay autoridades destacadas en este momento.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

