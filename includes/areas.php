<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('areas')) return; ?>
<!-- ============================================= -->
<!-- ÁREAS DE FORMACIÓN                            -->
<!-- ============================================= -->
<section class="areas" id="areas">
    <div class="areas__container">
        <div class="areas__header" style="text-align: center;">
            <span class="text-orange" style="font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 8px;"><?= content_get('areas', 'etiqueta_superior', 'Oferta Académica de Vanguardia') ?></span>
            <h2 class="areas__title" style="font-family: var(--font-heading); font-size: 2.8rem;">
                <?= content_get('areas', 'titulo', 'Áreas de Formación') ?>
            </h2>
            <p class="areas__subtitle" style="margin: 0 auto 40px auto;">
                <?= content_get('areas', 'descripcion', 'Programas tecnológicos de nivel superior diseñados para responder a las exigencias del mercado laboral actual con un enfoque 100% práctico.') ?>
            </p>
        </div>

        <div class="areas__grid">
            <!-- Card 1: Salud -->
            <div class="areas__card">
                <div class="areas__card-icon">
                    <img src="<?= content_raw('areas', 'area1_icono', 'img/doctor.png') ?>" alt="Salud" class="areas__icon-img">
                </div>
                <h3 class="areas__card-title"><?= content_get('areas', 'area1_titulo', "Facultad de Salud\ny Servicios Sociales (FASSS)") ?></h3>
                <p class="areas__card-description">
                    <?= content_get('areas', 'area1_desc', 'Carreras técnicas y tecnológicas enfocadas en el cuidado de la salud, enfermería, rehabilitación y bienestar comunitario.') ?>
                </p>
                <a href="<?= content_get('areas', 'area1_link', '#') ?>" class="btn btn--solid">
                    <?= content_get('areas', 'area1_btn', 'Explorar programas') ?>
                    <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 2: Ciencias Empresariales -->
            <div class="areas__card areas__card--image" style="background-image: linear-gradient(rgba(26, 54, 104, 0.8), rgba(26, 54, 104, 0.8)), url('<?= content_raw('areas', 'area2_bg', 'img/estudiantes1.png') ?>');">
                <div class="areas__card-icon">
                    <img src="<?= content_raw('areas', 'area2_icono', 'img/laptop.png') ?>" alt="Empresariales" class="areas__icon-img">
                </div>
                <h3 class="areas__card-title"><?= content_get('areas', 'area2_titulo', "Facultad de Ciencias Empresariales\ny Sistemas / Económicas y\nEmpresariales (FACES)") ?></h3>
                <p class="areas__card-description">
                    <?= content_get('areas', 'area2_desc', 'Programas de gestión, contabilidad, marketing y comercio para liderar en el sector empresarial e industrial.') ?>
                </p>
                <a href="<?= content_get('areas', 'area2_link', '#') ?>" class="btn btn--solid">
                    <?= content_get('areas', 'area2_btn', 'Explorar programas') ?>
                    <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Card 3: Transporte -->
            <div class="areas__card">
                <div class="areas__card-icon">
                    <img src="<?= content_raw('areas', 'area3_icono', 'img/coche.png') ?>" alt="Transporte" class="areas__icon-img">
                </div>
                <h3 class="areas__card-title"><?= content_get('areas', 'area3_titulo', "Facultad de Transporte\ny Vialidad (FATV)") ?></h3>
                <p class="areas__card-description">
                    <?= content_get('areas', 'area3_desc', 'Formación especializada en mecánica, gestión de transporte, seguridad vial y escuela de conducción') ?>
                </p>
                <a href="<?= content_get('areas', 'area3_link', '#') ?>" class="btn btn--solid">
                    <?= content_get('areas', 'area3_btn', 'Explorar programas') ?>
                    <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </div>
</section>