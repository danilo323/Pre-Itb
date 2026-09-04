<!-- ============================================= -->
<!-- HISTORIAS DE ÉXITO / TESTIMONIOS              -->
<!-- ============================================= -->
<section class="testimonios" id="testimonios">
    <div class="testimonios__container">
        <!-- Lado izquierdo: Imagen -->
        <div class="testimonios__image">
            <img src="<?= content_raw('testimonios', 'imagen', 'img/testimonio-estudiante.jpg') ?>" alt="<?= content_get('testimonios', 'nombre', 'Estudiante ITB') ?>">
        </div>

        <!-- Lado derecho: Contenido del testimonio -->
        <div class="testimonios__content">
            <span class="section-tag section-tag--light"><?= content_get('testimonios', 'etiqueta_superior', 'HISTORIAS DE ÉXITO') ?></span>
            <h2 class="testimonios__title">
                <?= content_get('testimonios', 'titulo_seccion_1', 'Lo que dicen nuestros') ?><br>
                <span class="text-orange"><?= content_get('testimonios', 'titulo_seccion_2', 'Graduados') ?></span>
            </h2>
            
            <div class="testimonios__slider">
                <!-- Testimonio Activo -->
                <div class="testimonios__slide active">
                    <div class="testimonios__quote-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p class="testimonios__text">
                        <?= content_get('testimonios', 'cita', '"El ITB me brindó las herramientas y el conocimiento necesario para destacarme en el campo laboral. Los docentes y el enfoque práctico marcaron la diferencia en mi formación profesional. Hoy lidero un equipo de trabajo gracias a la preparación que recibí."') ?>
                    </p>
                    <div class="testimonios__author">
                        <h4 class="testimonios__author-name"><?= content_get('testimonios', 'nombre', 'María Fernanda López') ?></h4>
                        <span class="testimonios__author-role"><?= content_get('testimonios', 'carrera', 'Graduada en Enfermería - Promoción 2022') ?></span>
                    </div>
                </div>
            </div>

            <!-- Navegación de testimonios -->
            <div class="testimonios__nav">
                <button class="testimonios__nav-btn" id="testimonios-prev" aria-label="Anterior">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="testimonios__nav-dots">
                    <span class="testimonios__dot testimonios__dot--active"></span>
                    <span class="testimonios__dot"></span>
                    <span class="testimonios__dot"></span>
                </div>
                <button class="testimonios__nav-btn" id="testimonios-next" aria-label="Siguiente">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>
