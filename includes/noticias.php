<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('noticias')) return; ?>
<!-- ============================================= -->
<!-- NOTICIAS, EVENTOS Y ALIANZAS                  -->
<!-- ============================================= -->
<section class="noticias" id="noticias">
    <div class="noticias__container">
        <div class="noticias__header">
            <div>
                <span class="section-tag"><?= content_get('noticias', 'etiqueta_superior', 'Vida Universitaria y Actualidad') ?></span>
                <h2 class="noticias__title">
                    <?= content_title('noticias', 'titulo', 'Noticias y Eventos del ITB') ?>
                </h2>
            </div>
            <a href="#" class="btn-noticias-todas" id="btn-todas-noticias">
                <?= content_get('noticias', 'boton_todas', 'Ver más Noticias y Eventos') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <div class="noticias__grid">
            <!-- Noticia principal -->
            <div class="noticias__main">
                <div class="noticias__main-img">
                    <img src="<?= content_raw('noticias', 'imagen', 'img/noticia_1.png') ?>" alt="Evento principal ITB">
                </div>
                <div class="noticias__main-body">
                    <div class="noticias__meta">
                        <span class="meta-cat"><?= content_get('noticias', 'categoria', 'EVENTO') ?></span>
                        <span class="meta-div">—</span>
                        <span class="meta-date"><?= content_get('noticias', 'fecha', 'Agosto 20, 2026') ?></span>
                    </div>
                    <h3 class="noticias__main-title"><?= content_get('noticias', 'titulo', '¡METAMORFOSIS CREATIVA está por comenzar!') ?></h3>
                    <p class="noticias__main-desc">
                        <?= content_get('noticias', 'descripcion', 'Lo mejor del Diseño de Modas y Maquillaje...') ?>
                    </p>
                    <a href="#" class="noticias__link">
                        <span class="noticias__link-text">Leer Más <i class="fas fa-arrow-right"></i></span>
                        <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </div>

            <!-- Lista de noticias -->
            <div class="noticias__list">
                <?php
                // Obtener las noticias secundarias configuradas en el panel
                $secundarias_default = [
                    [
                        'titulo' => 'Estudiantes de Diseño de Modas',
                        'fecha' => 'Agosto 20, 2026',
                        'imagen' => 'img/noticia_2.png',
                    ],
                    [
                        'titulo' => 'ITB promovió una movilidad',
                        'fecha' => 'Agosto 20, 2026',
                        'imagen' => 'img/noticia_3.png',
                    ]
                ];
                $secundarias = content_raw('noticias', 'secundarias', $secundarias_default);
                if (empty($secundarias) || !is_array($secundarias)) $secundarias = $secundarias_default;
                
                $total_sec = count($secundarias);
                $i = 0;
                foreach ($secundarias as $sec): 
                    $i++;
                    $is_last = ($i === $total_sec); 
                    $sec_titulo = htmlspecialchars($sec['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
                    $sec_fecha = htmlspecialchars($sec['fecha'] ?? '', ENT_QUOTES, 'UTF-8');
                    $sec_imagen = htmlspecialchars($sec['imagen'] ?? '', ENT_QUOTES, 'UTF-8');
                ?>
                <div class="noticias__item">
                    <div class="noticias__item-img">
                        <img src="<?= $sec_imagen ?>" alt="<?= $sec_titulo ?>">
                    </div>
                    <div class="noticias__item-body">
                        <div class="noticias__item-text">
                            <div class="noticias__meta">
                                <span class="meta-cat">NOTICIA</span>
                                <span class="meta-div">—</span>
                                <span class="meta-date"><?= $sec_fecha ?></span>
                            </div>
                            <h4 class="noticias__item-title"><?= $sec_titulo ?></h4>
                        </div>
                        <?php if ($is_last): ?>
                        <a href="#" class="btn-arrow-square" aria-label="Leer más">
                            <i class="fas fa-arrow-up"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
