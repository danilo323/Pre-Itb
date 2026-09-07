<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('areas')) return; ?>
<!-- ============================================= -->
<!-- ÁREAS DE FORMACIÓN                            -->
<!-- ============================================= -->
<section class="areas" id="areas">
    <div class="areas__container">
        <div class="areas__header" style="text-align: center;">
            <span class="text-orange" style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 8px;"><?= content_get('areas', 'etiqueta_superior', 'Oferta Académica de Vanguardia') ?></span>
            <h2 class="areas__title" style="font-family: var(--font-heading); font-size: 2.8rem;">
                <?= htmlspecialchars(content_get('areas', 'titulo', 'Nuestras Áreas de Formación'), ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <p class="areas__subtitle" style="margin: 0 auto 40px auto;">
                <?= content_get('areas', 'descripcion', 'Programas tecnológicos de nivel superior diseñados para responder a las exigencias del mercado laboral actual con un enfoque 100% práctico.') ?>
            </p>
        </div>

        <div class="areas__grid">
            <?php
            // Obtener las áreas desde el repeater
            $areas_list = content_raw('areas', 'lista_areas', [
                [
                    'titulo' => "Facultad de Salud\ny Servicios Sociales (FASSS)",
                    'descripcion' => "Carreras técnicas y tecnológicas enfocadas en el\ncuidado de la salud, enfermería, rehabilitación y\nbienestar comunitario.",
                    'icono' => 'img/doctor.png',
                    'imagen_fondo' => '',
                    'btn_texto' => 'Explorar programas'
                ],
                [
                    'titulo' => "Facultad de Ciencias Empresariales\ny Sistemas / Económicas y\nEmpresariales (FACES)",
                    'descripcion' => "Programas de gestión, contabilidad, marketing y\ncomercio para liderar en el sector empresarial e\nindustrial.",
                    'icono' => 'img/laptop.png',
                    'imagen_fondo' => 'img/Areas_formacion_1.png',
                    'btn_texto' => 'Explorar programas'
                ],
                [
                    'titulo' => "Facultad de Transporte\ny Vialidad (FATV)",
                    'descripcion' => "Formación especializada en mecánica, gestión de\ntransporte, seguridad vial y escuela de conducción",
                    'icono' => 'img/coche.png',
                    'imagen_fondo' => '',
                    'btn_texto' => 'Explorar programas'
                ]
            ]);
            
            foreach ((array)$areas_list as $area):
                $icono = $area['icono'] ?? '';
                $imagen_fondo = $area['imagen_fondo'] ?? '';
                
                $has_bg = !empty($imagen_fondo);
                $card_class = $has_bg ? 'areas__card areas__card--image' : 'areas__card';
                $bg_style = $has_bg ? 'background-image: linear-gradient(rgba(26, 54, 104, 0.8), rgba(26, 54, 104, 0.8)), url(\'' . htmlspecialchars($imagen_fondo, ENT_QUOTES, 'UTF-8') . '\');' : '';
            ?>
            <div class="<?= $card_class ?>" style="<?= $bg_style ?>">
                <div class="areas__card-icon">
                    <?php if (!empty($icono)): ?>
                        <img src="<?= htmlspecialchars($icono, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($area['titulo'] ?? 'Área', ENT_QUOTES, 'UTF-8') ?>" class="areas__icon-img">
                    <?php endif; ?>
                </div>
                <h3 class="areas__card-title"><?= nl2br(htmlspecialchars($area['titulo'] ?? '', ENT_QUOTES, 'UTF-8')) ?></h3>
                <p class="areas__card-description">
                    <?= nl2br(htmlspecialchars($area['descripcion'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                </p>
                <a href="#" class="btn btn--solid">
                    <?= htmlspecialchars($area['btn_texto'] ?? 'Explorar programas', ENT_QUOTES, 'UTF-8') ?>
                    <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>