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
            <!-- Card 1 -->
            <div class="programas__card">
                <div class="programas__card-img">
                    <img src="<?= content_raw('programas', 'prog1_imagen', 'img/programa-enfermeria.jpg') ?>" alt="<?= content_get('programas', 'prog1_titulo', 'Enfermería') ?>">
                    <span class="programas__card-badge"><?= content_get('programas', 'prog1_modalidad', 'Presencial') ?></span>
                </div>
                <div class="programas__card-body">
                    <span class="programas__card-area"><?= content_get('programas', 'prog1_area', 'Salud') ?></span>
                    <h3 class="programas__card-title"><?= content_get('programas', 'prog1_titulo', 'Tecnología Superior en Enfermería') ?></h3>
                    <div class="programas__card-meta">
                        <span><i class="fas fa-clock"></i> <?= content_get('programas', 'prog1_duracion', '5 Semestres') ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= content_get('programas', 'prog1_sede', 'Guayaquil') ?></span>
                    </div>
                    <a href="#" class="programas__card-btn">
                        Ver programa <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="programas__card">
                <div class="programas__card-img">
                    <img src="<?= content_raw('programas', 'prog2_imagen', 'img/programa-marketing.jpg') ?>" alt="<?= content_get('programas', 'prog2_titulo', 'Marketing Digital') ?>">
                    <span class="programas__card-badge"><?= content_get('programas', 'prog2_modalidad', 'Presencial') ?></span>
                </div>
                <div class="programas__card-body">
                    <span class="programas__card-area"><?= content_get('programas', 'prog2_area', 'Ciencias Empresariales') ?></span>
                    <h3 class="programas__card-title"><?= content_get('programas', 'prog2_titulo', 'Tecnología Superior en Marketing Digital') ?></h3>
                    <div class="programas__card-meta">
                        <span><i class="fas fa-clock"></i> <?= content_get('programas', 'prog2_duracion', '5 Semestres') ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= content_get('programas', 'prog2_sede', 'Guayaquil') ?></span>
                    </div>
                    <a href="#" class="programas__card-btn">
                        Ver programa <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="programas__card">
                <div class="programas__card-img">
                    <img src="<?= content_raw('programas', 'prog3_imagen', 'img/programa-logistica.jpg') ?>" alt="<?= content_get('programas', 'prog3_titulo', 'Logística y Transporte') ?>">
                    <span class="programas__card-badge"><?= content_get('programas', 'prog3_modalidad', 'Presencial') ?></span>
                </div>
                <div class="programas__card-body">
                    <span class="programas__card-area"><?= content_get('programas', 'prog3_area', 'Transporte') ?></span>
                    <h3 class="programas__card-title"><?= content_get('programas', 'prog3_titulo', 'Tecnología Superior en Logística y Transporte') ?></h3>
                    <div class="programas__card-meta">
                        <span><i class="fas fa-clock"></i> <?= content_get('programas', 'prog3_duracion', '5 Semestres') ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= content_get('programas', 'prog3_sede', 'Guayaquil') ?></span>
                    </div>
                    <a href="#" class="programas__card-btn">
                        Ver programa <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="programas__card">
                <div class="programas__card-img">
                    <img src="<?= content_raw('programas', 'prog4_imagen', 'img/programa-software.jpg') ?>" alt="<?= content_get('programas', 'prog4_titulo', 'Desarrollo de Software') ?>">
                    <span class="programas__card-badge"><?= content_get('programas', 'prog4_modalidad', 'Híbrido') ?></span>
                </div>
                <div class="programas__card-body">
                    <span class="programas__card-area"><?= content_get('programas', 'prog4_area', 'Tecnología') ?></span>
                    <h3 class="programas__card-title"><?= content_get('programas', 'prog4_titulo', 'Tecnología Superior en Desarrollo de Software') ?></h3>
                    <div class="programas__card-meta">
                        <span><i class="fas fa-clock"></i> <?= content_get('programas', 'prog4_duracion', '5 Semestres') ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= content_get('programas', 'prog4_sede', 'Guayaquil') ?></span>
                    </div>
                    <a href="#" class="programas__card-btn">
                        Ver programa <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
