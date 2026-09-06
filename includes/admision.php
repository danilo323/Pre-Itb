<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('admision')) return; ?>
<?php /* includes/admision.php */ ?>
<!-- ============================================= -->
<!-- FORMULARIO DE ADMISIÓN / REGISTRO             -->
<!-- ============================================= -->
<section class="admision" id="admision">
    <div class="admision__container">
        
        <!-- Lado Izquierdo: Imagen -->
        <div class="admision__image-col">
            <img src="<?= htmlspecialchars(content_get('admision', 'imagen_principal', 'img/admision1.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Estudiantes ITB" class="admision__img">
        </div>

        <!-- Lado Derecho: Contenido y Formulario -->
        <div class="admision__content-col">
            
            <!-- Título Animado (Marquesina Infinita) -->
            <div class="admision__marquee-wrapper">
                <div class="admision__marquee-track">
                    <h2 class="admision__huge-title"><?= htmlspecialchars(content_get('admision', 'titulo', 'Inicia tu proceso de admisión'), ENT_QUOTES, 'UTF-8') ?></h2>
                    <h2 class="admision__huge-title"><?= htmlspecialchars(content_get('admision', 'titulo', 'Inicia tu proceso de admisión'), ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
            </div>
            
            <div class="admision__split">
                <!-- Columna Centro: Texto descriptivo -->
                <div class="admision__text-wrapper">
                    <p class="admision__desc">
                        <?= htmlspecialchars(content_get('admision', 'descripcion', 'Da el primer paso hacia tu futuro profesional. Déjanos tus datos y un asesor académico se contactará contigo para guiarte en la elección de tu carrera, becas y opciones de financiamiento.'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    
                    <a href="#admision-form" class="hero__video-wrapper" style="width: 140px; height: 140px; margin-top: 150px; text-decoration: none;">
                        <!-- Texto circular giratorio -->
                        <div class="hero__circular-text">
                            <svg viewBox="0 0 160 160" class="hero__circular-svg">
                                <defs>
                                    <path id="circlePathAdmision" d="M 80,80 m -55,0 a 55,55 0 1,1 110,0 a 55,55 0 1,1 -110,0" />
                                </defs>
                                <text>
                                    <textPath href="#circlePathAdmision" class="hero__circular-text-path" textLength="345" lengthAdjust="spacing" style="font-size: 11px;">
                                        ¿CÓMO INSCRIBIRSE? • HAZ CLIC AQUÍ • 
                                    </textPath>
                                </text>
                            </svg>
                        </div>
                        <div class="hero__video-card" style="width: 70px; height: 70px;">
                            <div class="hero__play-btn" style="position: static; transform: none; width: 100%; height: 100%;">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Columna Derecha: Formulario Blanco -->
                <div class="admision__form-wrapper">
                    <form class="admision__form" id="admision-form" action="#" method="POST">
                        <h3 class="admision__form-title"><?= content_get('admision', 'form_titulo', 'Formulario de Registro') ?></h3>
                        <p class="admision__form-subtitle">Los campos marcados con un asterisco (<span class="admision__asterisk">*</span>) son obligatorios.</p>

                        <div class="admision__form-row">
                            <div class="admision__form-group">
                                <label for="nombre">Nombres<span class="admision__asterisk">*</span></label>
                                <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Carlos" required>
                            </div>
                            <div class="admision__form-group">
                                <label for="apellido">Apellidos<span class="admision__asterisk">*</span></label>
                                <input type="text" id="apellido" name="apellido" placeholder="Ej. Pérez Gómez" required>
                            </div>
                        </div>

                        <div class="admision__form-row">
                            <div class="admision__form-group">
                                <label for="email">Correo Electrónico<span class="admision__asterisk">*</span></label>
                                <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                            </div>
                            <div class="admision__form-group">
                                <label for="telefono">Celular / WhatsApp<span class="admision__asterisk">*</span></label>
                                <input type="tel" id="telefono" name="telefono" placeholder="Ej. 0991234567" required>
                            </div>
                        </div>

                        <div class="admision__form-row admision__form-row--mixed">
                            <div class="admision__form-group">
                                <label for="cedula">Número de Cédula<span class="admision__asterisk">*</span></label>
                                <input type="text" id="cedula" name="cedula" placeholder="Ej. 09xxxxxxxx" required>
                            </div>
                            
                            <div class="admision__form-group admision__form-group--radio">
                                <label>Nacionalidad<span class="admision__asterisk">*</span></label>
                                <div class="admision__radio-options">
                                    <label><input type="radio" name="nacionalidad" value="ecuatoriano" checked> Ecuatoriano</label>
                                    <label><input type="radio" name="nacionalidad" value="extranjero"> Extranjero</label>
                                </div>
                            </div>
                            
                            <div class="admision__form-group admision__form-group--radio">
                                <label>Soy Bachiller<span class="admision__asterisk">*</span></label>
                                <div class="admision__radio-options">
                                    <label><input type="radio" name="bachiller" value="si"> Sí, soy bachiller</label>
                                    <label><input type="radio" name="bachiller" value="no" checked> No, no soy bachiller</label>
                                </div>
                            </div>
                        </div>

                        <div class="admision__form-group">
                            <label for="carrera">Programa o Área de Interés<span class="admision__asterisk">*</span></label>
                            <div class="admision__select-wrapper">
                                <select id="carrera" name="carrera" required>
                                    <option value="" disabled selected>Selecciona una opción</option>
                                    <option value="enfermeria">Enfermería</option>
                                    <option value="fisioterapia">Fisioterapia</option>
                                    <option value="marketing">Marketing Digital</option>
                                    <option value="contabilidad">Contabilidad</option>
                                    <option value="logistica">Logística y Transporte</option>
                                    <option value="software">Desarrollo de Software</option>
                                    <option value="otro">Otra</option>
                                </select>
                                <i class="fas fa-chevron-down admision__select-icon"></i>
                            </div>
                        </div>

                        <div class="admision__form-group">
                            <label for="modalidad">Modalidad Preferida<span class="admision__asterisk">*</span></label>
                            <div class="admision__select-wrapper">
                                <select id="modalidad" name="modalidad" required>
                                    <option value="" disabled selected>Selecciona la modalidad</option>
                                    <option value="presencial">Presencial</option>
                                    <option value="online">Online</option>
                                    <option value="hibrida">Híbrida</option>
                                </select>
                                <i class="fas fa-chevron-down admision__select-icon"></i>
                            </div>
                        </div>

                        <div class="admision__form-group">
                            <label for="mensaje">Dudas o comentarios (opcional)</label>
                            <textarea id="mensaje" name="mensaje" rows="3" placeholder="Escribe aquí tu duda o comentario"></textarea>
                        </div>

                        <button type="submit" class="btn--solid admision__submit-btn">
                            <?= content_get('admision', 'btn_enviar', 'Completar registro') ?>
                            <span class="btn__icon-right-white"><i class="fas fa-arrow-right"></i></span>
                        </button>
                    </form>
                    
                    <!-- Boton flotante de noticias adjunto al formulario según diseño -->
                    <div style="display: flex; justify-content: flex-end; margin-top: 32px;">
                        <a href="#noticias" class="btn-noticias-todas">
                            Ver más Noticias y Eventos
                            <span class="btn__icon-right"><i class="fas fa-arrow-up-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
