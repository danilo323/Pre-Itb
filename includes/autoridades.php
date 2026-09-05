<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('autoridades')) return; ?>
<!-- ============================================= -->
<!-- NUESTRAS AUTORIDADES                          -->
<!-- ============================================= -->
<section class="autoridades" id="autoridades">
    <div class="autoridades__container">
        <div class="autoridades__header">
            <div>
                <span class="section-tag"><?= content_get('autoridades', 'etiqueta_superior', 'Nuestro Equipo') ?></span>
                <h2 class="autoridades__title">
                    <?= content_title('autoridades', 'titulo', 'Nuestras *Autoridades*') ?>
                </h2>
            </div>
            <a href="#" class="btn btn--outline-dark" id="btn-directorio">
                <?= content_get('autoridades', 'btn_directorio', 'Ver Directorio') ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>

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
                $cargo = htmlspecialchars($miembro['cargo'] ?? '', ENT_QUOTES, 'UTF-8');
                $linkedin = htmlspecialchars($miembro['linkedin'] ?? '#', ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars($miembro['email'] ?? '#', ENT_QUOTES, 'UTF-8');
                
                $count++;
            ?>
                <!-- Card <?= $count ?> -->
                <div class="autoridades__card">
                    <div class="autoridades__card-img">
                        <img src="<?= $foto ?>" alt="<?= $nombre ?>">
                        <div class="autoridades__card-social">
                            <a href="<?= $linkedin ?>" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="<?= $email ?>" aria-label="Email"><i class="fas fa-envelope"></i></a>
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
