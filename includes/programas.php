<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('programas')) return; ?>
<!-- ============================================= -->
<!-- PROGRAMAS DESTACADOS                          -->
<!-- ============================================= -->
<section class="programas" id="programas">
    <div class="programas__container">
        <div class="programas__header">
            <span class="programas__tag"><?= content_get('programas', 'etiqueta_superior', 'Formación Práctica e Innovadora') ?></span>
            <h2 class="programas__title"><?= content_title('programas', 'titulo', 'Programas *Destacados*') ?></h2>
            <p class="programas__subtitle"><?= content_get('programas', 'subtitulo', 'Descubre nuestros programas tecnológicos de mayor demanda laboral, diseñados para insertarte rápidamente en el mercado de trabajo.') ?></p>
        </div>

        <div class="programas__grid">
            <!-- Card 1 -->
            <div class="programas__card">
                <div class="programas__card-img">
                    <img src="<?= content_raw('programas', 'prog1_imagen', 'img/enfermeria.jpg') ?>" alt="Enfermería">
                </div>
                <div class="programas__card-body">
                    <h3 class="programas__card-title"><?= content_raw('programas', 'prog1_titulo', 'Tecnología Superior en<br>Enfermería') ?></h3>
                    <div class="programas__card-details">
                        <p>Modalidad: <?= content_get('programas', 'prog1_modalidad', 'Presencial / Híbrida') ?></p>
                        <p>Duración: <?= content_get('programas', 'prog1_duracion', '2 Años (4 Semestres)') ?></p>
                    </div>
                    <a href="#" class="btn--outline-card">Ver programa <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span></a>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="programas__card">
                <div class="programas__card-img">
                    <img src="<?= content_raw('programas', 'prog2_imagen', 'img/Mecanica.jpg') ?>" alt="Mecánica Automotriz">
                </div>
                <div class="programas__card-body">
                    <h3 class="programas__card-title"><?= content_raw('programas', 'prog2_titulo', 'Tecnología Superior en<br>Mecánica Automotriz') ?></h3>
                    <div class="programas__card-details">
                        <p>Modalidad: <?= content_get('programas', 'prog2_modalidad', 'Presencial / Híbrida') ?></p>
                        <p>Duración: <?= content_get('programas', 'prog2_duracion', '2 Años (4 Semestres)') ?></p>
                    </div>
                    <a href="#" class="btn--outline-card">Ver programa <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span></a>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="programas__card">
                <div class="programas__card-img">
                    <img src="<?= content_raw('programas', 'prog3_imagen', 'img/desarrollo_software.jpg') ?>" alt="Desarrollo de Software">
                </div>
                <div class="programas__card-body">
                    <h3 class="programas__card-title"><?= content_raw('programas', 'prog3_titulo', 'Tecnología Superior en<br>Desarrollo de Software') ?></h3>
                    <div class="programas__card-details">
                        <p>Modalidad: <?= content_get('programas', 'prog3_modalidad', 'Online / Presencial') ?></p>
                        <p>Duración: <?= content_get('programas', 'prog3_duracion', '2 Años (4 Semestres)') ?></p>
                    </div>
                    <a href="#" class="btn--outline-card">Ver programa <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span></a>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="programas__card">
                <div class="programas__card-img">
                    <img src="<?= content_raw('programas', 'prog4_imagen', 'img/administracion.jpg') ?>" alt="Administración">
                </div>
                <div class="programas__card-body">
                    <h3 class="programas__card-title"><?= content_raw('programas', 'prog4_titulo', 'Tecnología Superior en<br>Administración') ?></h3>
                    <div class="programas__card-details">
                        <p>Modalidad: <?= content_get('programas', 'prog4_modalidad', 'Online / Presencial') ?></p>
                        <p>Duración: <?= content_get('programas', 'prog4_duracion', '2 Años (4 Semestres)') ?></p>
                    </div>
                    <a href="#" class="btn--outline-card">Ver programa <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span></a>
                </div>
            </div>
        </div>

        <div class="programas__footer">
            <a href="#" class="btn--solid"><?= content_get('programas', 'btn_ver_todos', 'Ver todos los programas') ?> <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span></a>
        </div>
    </div>
</section>


