<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('areas')) return; ?>
<!-- ============================================= -->
<!-- ÁREAS DE FORMACIÓN                            -->
<!-- ============================================= -->
<section class="areas" id="areas">
    <div class="areas__container">
        <div class="areas__header">
            <span class="section-tag section-tag--light"><?= content_get('areas', 'etiqueta_superior', 'Áreas de Conocimiento') ?></span>
            <h2 class="areas__title">
                <?= content_title('areas', 'titulo', 'Nuestras Áreas de *Formación*') ?>
            </h2>
            <p class="areas__subtitle">
                <?= content_get('areas', 'descripcion', 'Descubre las áreas de estudio que ofrecemos para tu desarrollo profesional') ?>
            </p>
        </div>

        <div class="areas__grid">
            <!-- Card 1: Salud -->
            <div class="areas__card">
                <div class="areas__card-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h3 class="areas__card-title"><?= content_get('areas', 'area1_titulo', 'Salud') ?></h3>
                <p class="areas__card-description">
                    <?= content_get('areas', 'area1_desc', 'Formación integral en ciencias de la salud con laboratorios especializados y prácticas clínicas reales.') ?>
                </p>
                <ul class="areas__card-list">
                    <?php
                    $lista1 = explode("\n", content_get('areas', 'area1_programas', "Enfermería\nFisioterapia\nLaboratorio Clínico"));
                    foreach ($lista1 as $item) {
                        $item = trim($item);
                        if ($item) echo "<li>{$item}</li>\n";
                    }
                    ?>
                </ul>
                <a href="#" class="areas__card-btn">
                    <?= content_get('areas', 'area1_btn', 'Explorar programas') ?> <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Card 2: Ciencias Empresariales -->
            <div class="areas__card">
                <div class="areas__card-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3 class="areas__card-title"><?= content_get('areas', 'area2_titulo', 'Ciencias Empresariales') ?></h3>
                <p class="areas__card-description">
                    <?= content_get('areas', 'area2_desc', 'Desarrolla habilidades de liderazgo, gestión y emprendimiento con enfoque práctico y global.') ?>
                </p>
                <ul class="areas__card-list">
                    <?php
                    $lista2 = explode("\n", content_get('areas', 'area2_programas', "Administración de Empresas\nContabilidad\nMarketing Digital"));
                    foreach ($lista2 as $item) {
                        $item = trim($item);
                        if ($item) echo "<li>{$item}</li>\n";
                    }
                    ?>
                </ul>
                <a href="#" class="areas__card-btn">
                    <?= content_get('areas', 'area2_btn', 'Explorar programas') ?> <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Card 3: Transporte -->
            <div class="areas__card">
                <div class="areas__card-icon">
                    <i class="fas fa-ship"></i>
                </div>
                <h3 class="areas__card-title"><?= content_get('areas', 'area3_titulo', 'Transporte') ?></h3>
                <p class="areas__card-description">
                    <?= content_get('areas', 'area3_desc', 'Especialízate en logística y transporte marítimo, terrestre y multimodal con certificaciones internacionales.') ?>
                </p>
                <ul class="areas__card-list">
                    <?php
                    $lista3 = explode("\n", content_get('areas', 'area3_programas', "Logística y Transporte\nComercio Exterior\nOperaciones Portuarias"));
                    foreach ($lista3 as $item) {
                        $item = trim($item);
                        if ($item) echo "<li>{$item}</li>\n";
                    }
                    ?>
                </ul>
                <a href="#" class="areas__card-btn">
                    <?= content_get('areas', 'area3_btn', 'Explorar programas') ?> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>
