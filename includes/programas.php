<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('programas')) return; ?>



<section class="programas" id="programas">
    <div class="programas__container">
        <div class="programas__header">
            <span class="programas__tag"><?= content_get('programas', 'etiqueta_superior', 'Formación Práctica e Innovadora') ?></span>
            <h2 class="programas__title">
                <?= htmlspecialchars(content_get('programas', 'titulo', 'Programas Destacados'), ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <p class="programas__subtitle">
                <?= content_get('programas', 'descripcion', 'Descubre nuestros programas tecnológicos de mayor demanda laboral, diseñados para insertarte rápidamente en el mercado de trabajo.') ?>
            </p>
        </div>

        <div class="programas__grid">
            <?php
            // Obtener programas del repeater
            $programas_list = content_raw('programas', 'lista_programas', [
                [
                    'imagen' => 'img/enfermeria.jpg',
                    'modalidad' => 'Presencial / Híbrida',
                    'titulo' => 'Tecnología Superior en Enfermería',
                    'duracion' => '2 Años (4 Semestres)'
                ],
                [
                    'imagen' => 'img/Mecanica.jpg',
                    'modalidad' => 'Presencial / Híbrida',
                    'titulo' => 'Tecnología Superior en Mecánica Automotriz',
                    'duracion' => '2 Años (4 Semestres)'
                ],
                [
                    'imagen' => 'img/desarrollo_software.jpg',
                    'modalidad' => 'Online / Presencial',
                    'titulo' => 'Tecnología Superior en Desarrollo de Software',
                    'duracion' => '2 Años (4 Semestres)'
                ],
                [
                    'imagen' => 'img/administracion.jpg',
                    'modalidad' => 'Online / Presencial',
                    'titulo' => 'Tecnología Superior en Administración',
                    'duracion' => '2 Años (4 Semestres)'
                ]
            ]);
            
            $index = 0;
            foreach ((array)$programas_list as $prog):
                $img_src = $prog['imagen'] ?? '';
                if (!empty($img_src) && !content_image_exists($img_src)) $img_src = 'img/placeholder_imagen.svg';
            ?>
            <div class="programas__card">
                <div class="programas__card-img">
                    <?php if (!empty($img_src)): ?>
                        <img src="<?= htmlspecialchars($img_src, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($prog['titulo'] ?? 'Programa', ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                </div>
                <div class="programas__card-body">
                    <h3 class="programas__card-title"><?= nl2br(htmlspecialchars($prog['titulo'] ?? 'Título del programa', ENT_QUOTES, 'UTF-8')) ?></h3>
                    <div class="programas__card-details">
                        <p>Modalidad: <?= htmlspecialchars($prog['modalidad'] ?? 'Presencial', ENT_QUOTES, 'UTF-8') ?></p>
                        <p>Duración: <?= htmlspecialchars($prog['duracion'] ?? '2 Años', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <a href="#" class="btn--outline-card">Ver programa <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span></a>
                </div>
            </div>
            <?php 
                $index++;
            endforeach; 
            ?>
        </div>

        <div class="programas__footer">
            <a href="#" class="btn--solid">
                <?= content_get('programas', 'btn_ver_todos', 'Ver todos los programas') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </div>
</section>

