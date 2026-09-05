<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('experiencia')) return; ?>
<?php /* includes/experiencia.php */ ?>
<!-- ============================================= -->
<!-- TU EXPERIENCIA ITB                            -->
<!-- ============================================= -->
<section class="experiencia" id="experiencia">
    <div class="experiencia__container">
        <!-- Lado izquierdo -->
        <div class="experiencia__content">
            <span class="section-tag"><?= content_get('experiencia', 'etiqueta_superior', 'Vida Estudiantil') ?></span>
            <h2 class="experiencia__title">
                <?= content_title('experiencia', 'titulo', 'Tu Experiencia *ITB*') ?>
            </h2>
            <p class="experiencia__description">
                <?= content_get('experiencia', 'descripcion', 'Más allá de lo académico, el ITB te ofrece una experiencia universitaria completa con servicios y beneficios diseñados para tu bienestar.') ?>
            </p>

            <ul class="experiencia__list">
                <?php
                $caracteristicas = content_raw('experiencia', 'lista_caracteristicas', [
                    [
                        'icono' => 'fas fa-stethoscope',
                        'titulo' => 'Servicios Médicos',
                        'descripcion' => 'Atención médica y odontológica gratuita para estudiantes.'
                    ],
                    [
                        'icono' => 'fas fa-award',
                        'titulo' => 'Becas y Financiamiento',
                        'descripcion' => 'Programas de becas por excelencia académica y apoyo financiero.'
                    ],
                    [
                        'icono' => 'fas fa-laptop-code',
                        'titulo' => 'Laboratorios Modernos',
                        'descripcion' => 'Tecnología de punta en todos nuestros laboratorios especializados.'
                    ],
                    [
                        'icono' => 'fas fa-handshake',
                        'titulo' => 'Bolsa de Empleo',
                        'descripcion' => 'Conexión directa con empresas aliadas para tus prácticas y primer empleo.'
                    ]
                ]);
                
                $default_icons = ['fas fa-stethoscope', 'fas fa-award', 'fas fa-laptop-code', 'fas fa-handshake'];
                $index = 0;
                foreach ((array)$caracteristicas as $c):
                    $icono_class = $default_icons[$index % 4];
                ?>
                <li class="experiencia__list-item">
                    <span class="experiencia__list-icon"><i class="<?= $icono_class ?>"></i></span>
                    <div>
                        <strong><?= htmlspecialchars($c['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                        <p><?= nl2br(htmlspecialchars($c['descripcion'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                </li>
                <?php 
                    $index++;
                endforeach; 
                ?>
            </ul>

            <a href="#" class="btn btn--solid" id="btn-beneficios">
                <?= content_get('experiencia', 'btn_texto', 'Más beneficios') ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Lado derecho: Imagen -->
        <div class="experiencia__image">
            <img src="<?= content_raw('experiencia', 'imagen', 'img/experiencia-itb.jpg') ?>" alt="Experiencia estudiantil ITB">
            <div class="experiencia__image-badge">
                <span class="experiencia__image-badge-number"><?= content_get('experiencia', 'badge_numero', '98%') ?></span>
                <span class="experiencia__image-badge-text"><?= content_get('experiencia', 'badge_texto', 'Satisfacción Estudiantil') ?></span>
            </div>
        </div>
    </div>
</section>
