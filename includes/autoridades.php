<!-- ============================================= -->
<!-- NUESTRAS AUTORIDADES                          -->
<!-- ============================================= -->
<section class="autoridades" id="autoridades">
    <div class="autoridades__container">
        <div class="autoridades__header">
            <div>
                <span class="section-tag"><?= content_get('autoridades', 'etiqueta_superior', 'Nuestro Equipo') ?></span>
                <h2 class="autoridades__title">
                    <?= content_get('autoridades', 'titulo_seccion_1', 'Nuestras') ?> <span class="text-orange"><?= content_get('autoridades', 'titulo_seccion_2', 'Autoridades') ?></span>
                </h2>
            </div>
            <a href="#" class="btn btn--outline-dark" id="btn-directorio">
                <?= content_get('autoridades', 'btn_directorio', 'Ver Directorio') ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="autoridades__grid">
            <!-- Card 1 -->
            <div class="autoridades__card">
                <div class="autoridades__card-img">
                    <img src="<?= content_raw('autoridades', 'aut1_imagen', 'img/autoridad-1.jpg') ?>" alt="<?= content_get('autoridades', 'aut1_nombre', 'PhD. Roberto Tolozano Benites') ?>">
                    <div class="autoridades__card-social">
                        <a href="<?= content_raw('autoridades', 'aut1_linkedin', '#') ?>" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="<?= content_raw('autoridades', 'aut1_email', '#') ?>" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
                <div class="autoridades__card-body">
                    <h3 class="autoridades__card-name"><?= content_get('autoridades', 'aut1_nombre', 'PhD. Roberto Tolozano Benites') ?></h3>
                    <span class="autoridades__card-role"><?= content_get('autoridades', 'aut1_cargo', 'Canciller') ?></span>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="autoridades__card">
                <div class="autoridades__card-img">
                    <img src="<?= content_raw('autoridades', 'aut2_imagen', 'img/autoridad-2.jpg') ?>" alt="<?= content_get('autoridades', 'aut2_nombre', 'Mgs. Nombre Apellido') ?>">
                    <div class="autoridades__card-social">
                        <a href="<?= content_raw('autoridades', 'aut2_linkedin', '#') ?>" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="<?= content_raw('autoridades', 'aut2_email', '#') ?>" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
                <div class="autoridades__card-body">
                    <h3 class="autoridades__card-name"><?= content_get('autoridades', 'aut2_nombre', 'Mgs. Nombre Apellido') ?></h3>
                    <span class="autoridades__card-role"><?= content_get('autoridades', 'aut2_cargo', 'Rector') ?></span>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="autoridades__card">
                <div class="autoridades__card-img">
                    <img src="<?= content_raw('autoridades', 'aut3_imagen', 'img/autoridad-3.jpg') ?>" alt="<?= content_get('autoridades', 'aut3_nombre', 'Mgs. Nombre Apellido') ?>">
                    <div class="autoridades__card-social">
                        <a href="<?= content_raw('autoridades', 'aut3_linkedin', '#') ?>" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="<?= content_raw('autoridades', 'aut3_email', '#') ?>" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
                <div class="autoridades__card-body">
                    <h3 class="autoridades__card-name"><?= content_get('autoridades', 'aut3_nombre', 'Mgs. Nombre Apellido') ?></h3>
                    <span class="autoridades__card-role"><?= content_get('autoridades', 'aut3_cargo', 'Vicerrector Académico') ?></span>
                </div>
            </div>
        </div>
    </div>
</section>
