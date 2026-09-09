<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('himno')) return; ?>
<!-- ============================================= -->
<!-- MÚSICA Y LETRA (HIMNO) — editable desde el panel (Sobre Nosotros) -->
<!-- ============================================= -->
<section class="himno" id="himno">
    <div class="himno__container">

        <div class="himno__header">
            <h2 class="himno__title"><?= content_get('himno', 'titulo', 'Música y letra') ?></h2>
            <p class="himno__description">
                <?= nl2br(htmlspecialchars(content_raw('himno', 'descripcion', 'Nuestro himno representa la historia, los valores y el espíritu de excelencia de la comunidad bolivariana. Acompaña cada uno de nuestros actos solemnes y nos identifica como líderes tecnológicos en el país.'), ENT_QUOTES, 'UTF-8')) ?>
            </p>
        </div>

        <div class="himno__card">
            <!-- Columna Izquierda: Letra y Controles -->
            <div class="himno__content">
                <div class="himno__author">
                    Autor: <?= nl2br(htmlspecialchars(content_raw('himno', 'autor', "Dr. Narcilo Natan\nVillavicencio Maldonado"), ENT_QUOTES, 'UTF-8')) ?>
                </div>

                <div class="himno__lyrics">
                    <div class="himno__stanza">
                        <h3 class="himno__stanza-title">CORO</h3>
                        <p class="himno__stanza-text">
                            <?= nl2br(htmlspecialchars(content_raw('himno', 'coro', ''), ENT_QUOTES, 'UTF-8')) ?>
                        </p>
                    </div>

                    <div class="himno__stanza">
                        <h3 class="himno__stanza-title">ESTROFA I</h3>
                        <p class="himno__stanza-text">
                            <?= nl2br(htmlspecialchars(content_raw('himno', 'estrofa1', ''), ENT_QUOTES, 'UTF-8')) ?>
                        </p>
                    </div>

                    <div class="himno__stanza">
                        <h3 class="himno__stanza-title">ESTROFA II</h3>
                        <p class="himno__stanza-text">
                            <?= nl2br(htmlspecialchars(content_raw('himno', 'estrofa2', ''), ENT_QUOTES, 'UTF-8')) ?>
                        </p>
                    </div>
                </div>

                <div class="himno__actions">
                    <div class="himno__audio-player">
                        <!-- Reproductor de audio nativo, sin JS -->
                        <?php $himno_audio = content_raw('himno', 'audio', 'audio/himnoitb.mp3'); ?>
                        <audio controls>
                            <source src="<?= htmlspecialchars($himno_audio, ENT_QUOTES, 'UTF-8') ?>" type="audio/mpeg">
                            Tu navegador no soporta el elemento de audio.
                        </audio>
                    </div>

                    <a href="#" class="btn btn--solid">
                        <?= content_get('himno', 'btn_descargar', 'Descargar música') ?>
                        <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            </div>

            <!-- Columna Derecha: Imagen del Coro -->
            <div class="himno__image">
                <?php
                $himno_img = content_raw('himno', 'imagen', 'img/Himno_Estudiante_itb.jpg');
                $himno_img = content_image_exists($himno_img) ? $himno_img : 'https://via.placeholder.com/800x1000/F4F6F9/1A3B70?text=Espacio+para+foto';
                ?>
                <img src="<?= htmlspecialchars($himno_img, ENT_QUOTES, 'UTF-8') ?>" alt="Coro del ITB">
            </div>

            <!-- Footer de la Tarjeta -->
            <div class="himno__footer">
                <?= nl2br(htmlspecialchars(content_raw('himno', 'nota_legal', "Estos archivos son para uso exclusivo del usuario final. Por favor, no los redistribuya\nsin el permiso del coro de ITB."), ENT_QUOTES, 'UTF-8')) ?>
            </div>
        </div> <!-- Fin de himno__card -->

    </div>
</section>
