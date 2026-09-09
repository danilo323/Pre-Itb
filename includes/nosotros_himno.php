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
                    <?php
                    // Las tres partes se pintan igual, asi que van en un bucle.
                    // La que no tenga letra cargada se salta entera: un titulo
                    // "ESTROFA II" con el parrafo vacio debajo se ve como un
                    // error de la pagina, no como una seccion sin llenar.
                    $himno_partes = [
                        'CORO'       => content_raw('himno', 'coro', ''),
                        'ESTROFA I'  => content_raw('himno', 'estrofa1', ''),
                        'ESTROFA II' => content_raw('himno', 'estrofa2', ''),
                    ];
                    foreach ($himno_partes as $himno_parte => $himno_texto):
                        if (trim((string)$himno_texto) === '') continue;
                    ?>
                        <div class="himno__stanza">
                            <h3 class="himno__stanza-title"><?= $himno_parte ?></h3>
                            <p class="himno__stanza-text">
                                <?= nl2br(htmlspecialchars($himno_texto, ENT_QUOTES, 'UTF-8')) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php
                // El audio se usa dos veces: en el reproductor y en el boton de
                // descarga, asi que se lee una sola vez aqui arriba.
                $himno_audio = content_raw('himno', 'audio', 'audio/himnoitb.mp3');
                $himno_audio_ok = content_image_exists($himno_audio);
                ?>
                <div class="himno__actions">
                    <?php if ($himno_audio_ok): ?>
                        <div class="himno__audio-player">
                            <!-- Reproductor de audio nativo, sin JS -->
                            <audio controls>
                                <source src="<?= htmlspecialchars($himno_audio, ENT_QUOTES, 'UTF-8') ?>" type="audio/mpeg">
                                Tu navegador no soporta el elemento de audio.
                            </audio>
                        </div>

                        <!-- El boton baja el mismo archivo que suena arriba. Si
                             el panel se queda sin audio, no se pinta ninguno de
                             los dos: un reproductor vacio y un boton que no
                             descarga nada se ven como una pagina rota. -->
                        <a href="<?= htmlspecialchars($himno_audio, ENT_QUOTES, 'UTF-8') ?>"
                           download class="btn btn--solid">
                            <?= content_get('himno', 'btn_descargar', 'Descargar música') ?>
                            <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                        </a>
                    <?php endif; ?>
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
