<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('noticias')) return; ?>
<!-- ============================================= -->
<!-- NOTICIAS, EVENTOS Y ALIANZAS                  -->
<!-- ============================================= -->
<section class="noticias" id="noticias">
    <div class="noticias__container">
        <div class="noticias__header">
            <div>
                <span class="section-tag"><?= content_get('noticias', 'etiqueta_superior', 'Actualidad ITB') ?></span>
                <h2 class="noticias__title">
                    <?= content_title('noticias', 'titulo', 'Noticias y *Eventos*') ?>
                </h2>
            </div>
            <a href="#" class="btn btn--outline-dark" id="btn-todas-noticias">
                <?= content_get('noticias', 'boton_todas', 'Todas las noticias') ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="noticias__grid">
            <!-- Noticia principal -->
            <div class="noticias__main">
                <div class="noticias__main-img">
                    <img src="<?= content_raw('noticias', 'imagen', 'img/noticia-principal.jpg') ?>" alt="Evento principal ITB">
                    <span class="noticias__badge"><?= content_get('noticias', 'categoria', 'Evento') ?> Destacado</span>
                </div>
                <div class="noticias__main-body">
                    <div class="noticias__meta">
                        <span><i class="fas fa-calendar"></i> <?= content_get('noticias', 'fecha', '15 Sep 2025') ?></span>
                        <span><i class="fas fa-tag"></i> <?= content_get('noticias', 'categoria', 'Evento') ?></span>
                    </div>
                    <h3 class="noticias__main-title"><?= content_get('noticias', 'titulo', 'Casa Abierta ITB 2025: Descubre tu vocación profesional') ?></h3>
                    <p class="noticias__main-desc">
                        <?= content_get('noticias', 'descripcion', 'Visita nuestro campus y conoce de primera mano nuestras instalaciones, docentes y oferta académica en la Casa Abierta más grande del año.') ?>
                    </p>
                    <a href="#" class="noticias__link">
                        Leer más <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Lista de noticias -->
            <div class="noticias__list">
                <div class="noticias__item">
                    <div class="noticias__item-img">
                        <img src="<?= content_raw('noticias', 'sec1_imagen', 'img/noticia-2.jpg') ?>" alt="Noticia 2">
                    </div>
                    <div class="noticias__item-body">
                        <div class="noticias__meta">
                            <span><i class="fas fa-calendar"></i> <?= content_get('noticias', 'sec1_fecha', '10 Sep 2025') ?></span>
                        </div>
                        <h4 class="noticias__item-title"><?= content_get('noticias', 'sec1_titulo', 'Convenio internacional con universidad de España') ?></h4>
                        <a href="#" class="noticias__link">Leer más <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="noticias__item">
                    <div class="noticias__item-img">
                        <img src="<?= content_raw('noticias', 'sec2_imagen', 'img/noticia-3.jpg') ?>" alt="Noticia 3">
                    </div>
                    <div class="noticias__item-body">
                        <div class="noticias__meta">
                            <span><i class="fas fa-calendar"></i> <?= content_get('noticias', 'sec2_fecha', '05 Sep 2025') ?></span>
                        </div>
                        <h4 class="noticias__item-title"><?= content_get('noticias', 'sec2_titulo', 'Graduación de la promoción 2025: más de 500 nuevos profesionales') ?></h4>
                        <a href="#" class="noticias__link">Leer más <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <div class="noticias__item">
                    <div class="noticias__item-img">
                        <img src="<?= content_raw('noticias', 'sec3_imagen', 'img/noticia-4.jpg') ?>" alt="Noticia 4">
                    </div>
                    <div class="noticias__item-body">
                        <div class="noticias__meta">
                            <span><i class="fas fa-calendar"></i> <?= content_get('noticias', 'sec3_fecha', '01 Sep 2025') ?></span>
                        </div>
                        <h4 class="noticias__item-title"><?= content_get('noticias', 'sec3_titulo', 'ITB inaugura nuevo laboratorio de simulación clínica') ?></h4>
                        <a href="#" class="noticias__link">Leer más <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================= -->
<!-- ALIANZAS                                      -->
<!-- ============================================= -->
<section class="alianzas" id="alianzas">
    <div class="alianzas__container">
        <h3 class="alianzas__title">Nuestros Aliados Estratégicos</h3>
        <div class="alianzas__track">
            <div class="alianzas__logos">
                <img src="img/alianza-1.png" alt="ATE">
                <img src="img/alianza-2.png" alt="Artefacta">
                <img src="img/alianza-3.png" alt="Alianza 3">
                <img src="img/alianza-4.png" alt="Alianza 4">
                <img src="img/alianza-5.png" alt="Alianza 5">
                <img src="img/alianza-6.png" alt="Alianza 6">
                <!-- Duplicados para efecto infinito -->
                <img src="img/alianza-1.png" alt="ATE">
                <img src="img/alianza-2.png" alt="Artefacta">
                <img src="img/alianza-3.png" alt="Alianza 3">
                <img src="img/alianza-4.png" alt="Alianza 4">
                <img src="img/alianza-5.png" alt="Alianza 5">
                <img src="img/alianza-6.png" alt="Alianza 6">
            </div>
        </div>
    </div>
</section>
