<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('admision')) return; ?>
<?php /* includes/admision.php */
// Video del botón circular. Si el administrador no puso ningún link, o escribió
// algo que no es un link de YouTube reconocible, el botón mantiene su
// comportamiento anterior: bajar hasta el formulario. Así un error de tipeo
// nunca abre una ventana de video en negro.
$admision_video_raw   = trim(content_raw('admision', 'video_url', 'https://www.youtube.com/watch?v=_arpKGQERXM'));
$admision_video_embed = youtube_embed_url($admision_video_raw);
$admision_tiene_video = str_contains($admision_video_embed, 'youtube.com/embed/');
?>



<section class="admision" id="admision">
    <div class="admision__container">
        
        
        <div class="admision__image-col" data-jarallax data-speed="0.5" data-img-position="top">
            <img src="<?= htmlspecialchars(content_raw('admision', 'imagen_principal', 'img/admision1.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Estudiantes ITB" class="admision__img jarallax-img">
        </div>

        
        <div class="admision__content-col">
            
            
            <div class="admision__marquee-wrapper">
                <div class="admision__marquee-track">
                    <h2 class="admision__huge-title"><?= htmlspecialchars(content_get('admision', 'titulo', 'Inicia tu proceso de admisión'), ENT_QUOTES, 'UTF-8') ?></h2>
                    <h2 class="admision__huge-title"><?= htmlspecialchars(content_get('admision', 'titulo', 'Inicia tu proceso de admisión'), ENT_QUOTES, 'UTF-8') ?></h2>
                </div>
            </div>
            
            <div class="admision__split">
                
                <div class="admision__text-wrapper">
                    <p class="admision__desc">
                        <?= htmlspecialchars(content_get('admision', 'descripcion', 'Da el primer paso hacia tu futuro profesional. Déjanos tus datos y un asesor académico se contactará contigo para guiarte en la elección de tu carrera, becas y opciones de financiamiento.'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    
                    <a href="<?= $admision_tiene_video ? htmlspecialchars($admision_video_raw, ENT_QUOTES, 'UTF-8') : '#admision-form' ?>"
                       class="hero__video-wrapper admision__video-circle<?= $admision_tiene_video ? ' js-video-modal-trigger' : '' ?>"
                       <?php if ($admision_tiene_video): ?>data-video-url="<?= htmlspecialchars($admision_video_embed, ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                       >
                        
                        <div class="hero__circular-text">
                            <svg viewBox="0 0 160 160" class="hero__circular-svg">
                                <defs>
                                    <path id="circlePathAdmision" d="M 80,80 m -55,0 a 55,55 0 1,1 110,0 a 55,55 0 1,1 -110,0" />
                                </defs>
                                <text>
                                    <textPath href="#circlePathAdmision" class="hero__circular-text-path" textLength="345" lengthAdjust="spacing" style="font-size: 11px;">
                                        <?= content_circular('admision', 'circular_text', "¿CÓMO INSCRIBIRSE?\nHAZ CLIC AQUÍ") ?>
                                    </textPath>
                                </text>
                            </svg>
                        </div>
                        <div class="hero__video-card admision__video-card">
                            <div class="hero__play-btn admision__play-btn">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                    </a>
                </div>

                
                <div class="admision__form-wrapper">
                    <form class="admision__form" id="admision-form" action="#" method="POST">
                        <h3 class="admision__form-title"><?= content_get('admision', 'form_titulo', 'Formulario de Registro') ?></h3>
                        <p class="admision__form-subtitle">
                            <?php
                            // El asterisco del subtítulo se pinta en naranja sin que el
                            // administrador tenga que escribir HTML: solo pone un * en el texto.
                            $subtitulo = content_get('admision', 'form_subtitulo', 'Los campos marcados con un asterisco (*) son obligatorios.');
                            echo str_replace('*', '<span class="admision__asterisk">*</span>', $subtitulo);
                            ?>
                        </p>

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
                                    <?php
                                    // Opciones administrables desde el panel (Inicio → Admisión).
                                    // El orden es el que se define allí con las flechas ↑ ↓.
                                    $programas_interes = content_raw('admision', 'lista_programas_interes', [
                                        ['texto' => 'Enfermería'],
                                        ['texto' => 'Fisioterapia'],
                                        ['texto' => 'Marketing Digital'],
                                        ['texto' => 'Contabilidad'],
                                        ['texto' => 'Logística y Transporte'],
                                        ['texto' => 'Desarrollo de Software'],
                                        ['texto' => 'Otra'],
                                    ]);
                                    foreach ((array)$programas_interes as $opcion):
                                        $txt = trim($opcion['texto'] ?? '');
                                        if ($txt === '') continue;
                                        $txt = htmlspecialchars($txt, ENT_QUOTES, 'UTF-8');
                                    ?>
                                        <option value="<?= $txt ?>"><?= $txt ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fas fa-chevron-down admision__select-icon"></i>
                            </div>
                        </div>

                        <div class="admision__form-group">
                            <label for="modalidad">Modalidad Preferida<span class="admision__asterisk">*</span></label>
                            <div class="admision__select-wrapper">
                                <select id="modalidad" name="modalidad" required>
                                    <option value="" disabled selected>Selecciona la modalidad</option>
                                    <?php
                                    // Opciones administrables desde el panel (Inicio → Admisión).
                                    $modalidades = content_raw('admision', 'lista_modalidades', [
                                        ['texto' => 'Presencial'],
                                        ['texto' => 'Online'],
                                        ['texto' => 'Híbrida'],
                                    ]);
                                    foreach ((array)$modalidades as $opcion):
                                        $txt = trim($opcion['texto'] ?? '');
                                        if ($txt === '') continue;
                                        $txt = htmlspecialchars($txt, ENT_QUOTES, 'UTF-8');
                                    ?>
                                        <option value="<?= $txt ?>"><?= $txt ?></option>
                                    <?php endforeach; ?>
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

                </div>
            </div>
        </div>
    </div>
</section>
