<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('programas')) return; ?>
<!-- ============================================= -->
<!-- PROGRAMAS DESTACADOS                          -->
<!-- ============================================= -->
<section class="programas" id="programas">
    <div class="programas__container">
        <div class="programas__header">
            <div class="programas__header-left">
                <span class="section-tag"><?= content_get('programas', 'etiqueta_superior', 'Oferta Académica') ?></span>
                <h2 class="programas__title">
                    <?= content_title('programas', 'titulo', 'Programas *Destacados*') ?>
                </h2>
            </div>
            <a href="#" class="btn btn--outline-dark" id="btn-ver-todos">
                <?= content_get('programas', 'btn_ver_todos', 'Ver todos los programas') ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="programas__grid">
            <?php
            // Obtener programas del repeater
            $programas_list = content_raw('programas', 'lista_programas', [
                [
                    'imagen' => 'img/programa-enfermeria.jpg',
                    'modalidad' => 'Presencial',
                    'area' => 'Salud',
                    'titulo' => 'Tecnología Superior en Enfermería',
                    'duracion' => '5 Semestres',
                    'sede' => 'Guayaquil'
                ],
                [
                    'imagen' => 'img/programa-marketing.jpg',
                    'modalidad' => 'Presencial',
                    'area' => 'Ciencias Empresariales',
                    'titulo' => 'Tecnología Superior en Marketing Digital',
                    'duracion' => '5 Semestres',
                    'sede' => 'Guayaquil'
                ],
                [
                    'imagen' => 'img/programa-logistica.jpg',
                    'modalidad' => 'Presencial',
                    'area' => 'Transporte',
                    'titulo' => 'Tecnología Superior en Logística y Transporte',
                    'duracion' => '5 Semestres',
                    'sede' => 'Guayaquil'
                ],
                [
                    'imagen' => 'img/programa-software.jpg',
                    'modalidad' => 'Híbrido',
                    'area' => 'Tecnología',
                    'titulo' => 'Tecnología Superior en Desarrollo de Software',
                    'duracion' => '5 Semestres',
                    'sede' => 'Guayaquil'
                ]
            ]);
            
            $default_imgs = ['img/programa-enfermeria.jpg', 'img/programa-marketing.jpg', 'img/programa-logistica.jpg', 'img/programa-software.jpg'];
            $index = 0;
            foreach ((array)$programas_list as $prog):
                $fallback_img = $default_imgs[$index % 4];
                $img_src = !empty($prog['imagen']) ? $prog['imagen'] : $fallback_img;
            ?>
            <div class="programas__card">
                <div class="programas__card-img">
                    <img src="<?= htmlspecialchars($img_src, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($prog['titulo'] ?? 'Programa', ENT_QUOTES, 'UTF-8') ?>">
                    <span class="programas__card-badge"><?= htmlspecialchars($prog['modalidad'] ?? 'Presencial', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="programas__card-body">
                    <span class="programas__card-area"><?= htmlspecialchars($prog['area'] ?? 'Área', ENT_QUOTES, 'UTF-8') ?></span>
                    <h3 class="programas__card-title"><?= htmlspecialchars($prog['titulo'] ?? 'Título del programa', ENT_QUOTES, 'UTF-8') ?></h3>
                    <div class="programas__card-meta">
                        <span><i class="fas fa-clock"></i> <?= htmlspecialchars($prog['duracion'] ?? '5 Semestres', ENT_QUOTES, 'UTF-8') ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($prog['sede'] ?? 'Guayaquil', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <a href="#" class="programas__card-btn">
                        Ver programa <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php 
                $index++;
            endforeach; 
            ?>
        </div>
    </div>
</section>
