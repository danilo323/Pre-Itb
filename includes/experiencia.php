<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('experiencia')) return; ?>
<?php /* includes/experiencia.php */ ?>
<!-- ============================================= -->
<!-- TU EXPERIENCIA ITB                            -->
<!-- ============================================= -->
<section class="experiencia" id="experiencia">
    <div class="experiencia__container">

        <!-- Lado izquierdo: Texto -->
        <div class="experiencia__content">
            <span class="experiencia__tag"><?= content_get('experiencia', 'etiqueta_superior', '¿Por qué elegir el ITB?') ?></span>
            <h2 class="experiencia__title">
                <?= htmlspecialchars(content_get('experiencia', 'titulo', 'Tu Experiencia ITB'), ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <p class="experiencia__description">
                <?= content_get('experiencia', 'descripcion', 'En el ITB no solo te formamos académicamente; nos preocupamos por tu bienestar integral. Te ofrecemos beneficios exclusivos diseñados para apoyarte durante toda tu carrera universitaria.') ?>
            </p>

            <ul class="experiencia__list">
                <?php
                $caracteristicas = content_raw('experiencia', 'lista_caracteristicas', [
                    [
                        'titulo' => 'Servicios Médicos Gratuitos',
                        'descripcion' => '(Podología, psicología, medicina general)'
                    ],
                    [
                        'titulo' => 'Becas y Apoyo Económico',
                        'descripcion' => '(Académicas, deportivas y culturales)'
                    ],
                    [
                        'titulo' => 'Gimnasio y SPA Gratis',
                        'descripcion' => '(Acceso exclusivo a ITB GYM)'
                    ],
                    [
                        'titulo' => 'Modalidades a Tu Medida',
                        'descripcion' => '(Presencial, híbrida u online)'
                    ]
                ]);
                
                foreach ((array)$caracteristicas as $c):
                ?>
                <li class="experiencia__list-item">
                    <span class="experiencia__check"><i class="fas fa-check"></i></span>
                    <p><strong><?= htmlspecialchars($c['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong> <?= htmlspecialchars($c['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                </li>
                <?php 
                endforeach; 
                ?>
            </ul>

            <a href="#" class="btn--solid" id="btn-beneficios">
                <?= content_get('experiencia', 'btn_texto', 'Más beneficios') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Lado derecho: Imagen -->
        <div class="experiencia__image">
            <img src="<?= htmlspecialchars(content_raw('experiencia', 'imagen', 'img/experiencia.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Experiencia estudiantil ITB">
        </div>

    </div>
</section>

<!-- ============================================= -->
<!-- TESTIMONIOS - LO QUE DICEN NUESTROS           -->
<!-- ESTUDIANTES                                   -->
<!-- ============================================= -->
<section class="testimonios" id="testimonios">
    <div class="testimonios__container">

        <!-- Foto a la izquierda -->
        <div class="testimonios__photo">
            <img src="img/MariaFernanda.png" alt="María Fernanda Gómez">
        </div>

        <!-- Contenido a la derecha -->
        <div class="testimonios__content">
            <span class="testimonios__tag">Historias de Éxito</span>
            <h2 class="testimonios__title">Lo que dicen nuestros<br>estudiantes</h2>

            <div class="testimonios__quote-block">
                <span class="testimonios__quote-icon">&ldquo;</span>
                <p class="testimonios__quote-text">
                    Gracias a la modalidad híbrida del ITB y la formación
                    práctica en laboratorios, pude incorporarme rápidamente
                    al sector laboral mientras terminaba mi carrera.
                </p>
            </div>

            <div class="testimonios__author">
                <p class="testimonios__author-role">Graduada</p>
                <p class="testimonios__author-name">María Fernanda Gómez</p>
                <p class="testimonios__author-program">Tecnología Superior en Enfermería</p>
            </div>
        </div>

    </div>
</section>


