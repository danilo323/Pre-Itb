<?php /* includes/admision.php */ ?>
<!-- ============================================= -->
<!-- FORMULARIO DE ADMISIÓN                        -->
<!-- ============================================= -->
<section class="admision" id="admision">
    <div class="admision__container">
        <!-- Lado izquierdo: Texto -->
        <div class="admision__content">
            <span class="section-tag section-tag--light">Admisiones Abiertas</span>
            <h2 class="admision__title">
                <?= content_get('admision', 'titulo', 'Inicia tu proceso de admisión') ?>
            </h2>
            <p class="admision__description">
                <?= content_get('admision', 'descripcion', 'Da el primer paso hacia tu futuro profesional. Completa el formulario y un asesor académico se pondrá en contacto contigo para guiarte en todo el proceso de inscripción.') ?>
            </p>
            <div class="admision__features">
                <div class="admision__feature">
                    <i class="fas fa-check-circle"></i>
                    <span><?= content_get('admision', 'feature_1', 'Proceso 100% en línea') ?></span>
                </div>
                <div class="admision__feature">
                    <i class="fas fa-check-circle"></i>
                    <span><?= content_get('admision', 'feature_2', 'Asesoría personalizada') ?></span>
                </div>
                <div class="admision__feature">
                    <i class="fas fa-check-circle"></i>
                    <span><?= content_get('admision', 'feature_3', 'Respuesta en 24 horas') ?></span>
                </div>
            </div>
        </div>

        <!-- Lado derecho: Formulario -->
        <div class="admision__form-wrapper">
            <form class="admision__form" id="admision-form" action="#" method="POST">
                <h3 class="admision__form-title">Solicita Información</h3>

                <div class="admision__form-row">
                    <div class="admision__form-group">
                        <label for="nombre">Nombres</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre completo" required>
                    </div>
                    <div class="admision__form-group">
                        <label for="apellido">Apellidos</label>
                        <input type="text" id="apellido" name="apellido" placeholder="Tu apellido completo" required>
                    </div>
                </div>

                <div class="admision__form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
                </div>

                <div class="admision__form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="09XX XXX XXXX" required>
                </div>

                <div class="admision__form-group">
                    <label for="carrera">Carrera de Interés</label>
                    <select id="carrera" name="carrera" required>
                        <option value="" disabled selected>Selecciona una carrera</option>
                        <option value="enfermeria">Enfermería</option>
                        <option value="fisioterapia">Fisioterapia</option>
                        <option value="marketing">Marketing Digital</option>
                        <option value="contabilidad">Contabilidad</option>
                        <option value="logistica">Logística y Transporte</option>
                        <option value="software">Desarrollo de Software</option>
                        <option value="otro">Otra</option>
                    </select>
                </div>

                <div class="admision__form-group">
                    <label for="mensaje">Mensaje (Opcional)</label>
                    <textarea id="mensaje" name="mensaje" rows="3" placeholder="¿Tienes alguna consulta?"></textarea>
                </div>

                <button type="submit" class="admision__form-btn" id="admision-submit">
                    <?= content_get('admision', 'btn_enviar', 'Enviar Solicitud') ?> <i class="fas fa-paper-plane"></i>
                </button>

                <p class="admision__form-terms">
                    Al enviar este formulario, aceptas nuestra <a href="#">Política de Privacidad</a>.
                </p>
            </form>
        </div>
    </div>
</section>
