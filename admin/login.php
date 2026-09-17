<?php
// admin/login.php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/base_url.php';

auth_session_start();
$AB = admin_base();
$SB = site_base();

// Si ya está logueado y la sesión es válida, ir directo al panel
if (!empty($_SESSION['admin_logged']) && auth_check_inactivity()) {
    header('Location: ' . $AB . '/index.php');
    exit;
}

$error = '';
$notice = '';

if (isset($_GET['expired'])) {
    $notice = 'Tu sesión ha expirado por inactividad. Por favor, ingresa nuevamente.';
}

// 1. Verificar bloqueo por Rate Limiting
$rateLimit = auth_rate_limit_check();
$isLocked = $rateLimit['locked'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    if ($isLocked) {
        $minutes = ceil($rateLimit['seconds_left'] / 60);
        $error = "Demasiados intentos fallidos. Acceso bloqueado temporalmente por {$minutes} minuto(s).";
    } else {
        $user = trim($_POST['username'] ?? '');
        $pass = trim($_POST['password'] ?? '');

        // Autenticación estricta con password_verify() (sin fallbacks en texto plano)
        if (auth_verify_credentials($user, $pass)) {
            // Protección contra Session Fixation
            session_regenerate_id(true);
            $_SESSION['admin_logged'] = true;
            $_SESSION['user'] = $user;
            $_SESSION['last_activity'] = time();

            // Resetear contador de fallos
            auth_rate_limit_reset();

            header('Location: ' . $AB . '/index.php');
            exit;
        } else {
            // Registrar intento fallido
            auth_rate_limit_fail();
            $newCheck = auth_rate_limit_check();
            if ($newCheck['locked']) {
                $minutes = ceil($newCheck['seconds_left'] / 60);
                $error = "Has excedido el número máximo de intentos. Acceso bloqueado por {$minutes} minutos.";
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        }
    }
}

// Si el bloqueo sigue activo al abrir la pagina (no solo al enviar el
// formulario), avisarlo de entrada en vez de dejar que lo descubra fallando.
if ($isLocked && $error === '') {
    $minutes = max(1, (int) ceil($rateLimit['seconds_left'] / 60));
    $error = "Acceso bloqueado temporalmente por demasiados intentos fallidos. Vuelve a intentarlo en {$minutes} minuto(s).";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Admin — ITB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Ruta calculada dinámicamente: funciona igual en la raíz del dominio o en una subcarpeta (XAMPP) -->
    <link rel="stylesheet" href="<?= $AB ?>/assets/admin.css">
    <!-- login.css va después de admin.css: reutiliza sus variables, .field-group
         y .flash-message, y solo añade lo propio de esta pantalla. -->
    <link rel="stylesheet" href="<?= $AB ?>/assets/login.css">
</head>
<body class="login-body">

    <!-- Mitad de marca. Es puramente decorativa: nada de lo que hay aquí
         hace falta para iniciar sesión, por eso en móvil se reduce a una
         franja de cabecera. -->
    <aside class="login-vitrina">

        <img class="login-vitrina__foto" src="<?= $AB ?>/assets/login-fondo.jpg" alt="" aria-hidden="true">
        <span class="login-vitrina__velo"></span>

        <div class="login-vitrina__marca">
            <img src="<?= $SB ?>img/logo-itb-white.png" alt="Instituto Superior Universitario Bolivariano de Tecnología">
        </div>

        <p class="login-vitrina__pie">
            <i class="bi bi-shield-lock"></i>
            Área privada del sitio institucional
        </p>

    </aside>

    <!-- Mitad de acceso -->
    <div class="login-acceso">
        <main class="login-panel">

            <h1 class="login-panel__titulo">Iniciar sesión</h1>
            <p class="login-panel__sub">Panel de administración del ITB</p>

            <?php if (!empty($notice)): ?>
                <div class="flash-message flash-info" role="status">
                    <i class="bi bi-info-circle-fill"></i>
                    <?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="flash-message flash-error" role="alert">
                    <i class="bi bi-x-octagon-fill"></i>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

                <div class="field-group">
                    <label for="username">Usuario</label>
                    <span class="login-campo">
                        <input type="text" id="username" name="username" placeholder="admin" required autofocus autocomplete="username" <?= $isLocked ? 'disabled' : '' ?>>
                    </span>
                </div>

                <div class="field-group">
                    <label for="password">Contraseña</label>
                    <span class="login-campo login-campo--clave">
                        <input type="password" id="password" name="password" placeholder="Tu contraseña" required autocomplete="current-password" <?= $isLocked ? 'disabled' : '' ?>>
                        <button type="button" class="login-ver" id="verClave" aria-controls="password" aria-pressed="false" aria-label="Mostrar la contraseña" title="Mostrar la contraseña">
                            <i class="bi bi-eye"></i>
                        </button>
                    </span>
                </div>

                <p class="login-mayusculas" id="avisoMayusculas" role="status" hidden>
                    <i class="bi bi-capslock-fill"></i>
                    Bloqueo de mayúsculas activado
                </p>

                <button type="submit" class="login-btn <?= $isLocked ? 'is-locked' : '' ?>" <?= $isLocked ? 'disabled' : '' ?>>
                    Entrar al Panel <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <p class="login-sep">¿No eres parte del equipo?</p>

            <a class="login-secundario" href="<?= $SB ?>">
                <i class="bi bi-house-door"></i> Volver al sitio
            </a>

            <p class="login-nota">La sesión se cierra sola tras un rato sin actividad.</p>

        </main>

    </div>

    <script>
    // Dos ayudas puntuales de esta pantalla. Van aquí y no en admin.js porque
    // admin.js no se carga en el login (y no tendría sentido cargarlo entero).
    (function () {
        'use strict';

        var clave = document.getElementById('password');
        var boton = document.getElementById('verClave');
        var aviso = document.getElementById('avisoMayusculas');

        // 1. Ver / ocultar la contraseña.
        if (clave && boton) {
            boton.addEventListener('click', function () {
                var visible = clave.type === 'text';
                clave.type = visible ? 'password' : 'text';
                boton.setAttribute('aria-pressed', String(!visible));

                var texto = visible ? 'Mostrar la contraseña' : 'Ocultar la contraseña';
                boton.setAttribute('aria-label', texto);
                boton.setAttribute('title', texto);
                boton.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';

                // Devolver el foco al campo sin perder la posición del cursor:
                // si no, al seguir escribiendo el texto se iría al principio.
                var fin = clave.value.length;
                clave.focus();
                try { clave.setSelectionRange(fin, fin); } catch (e) { /* algunos navegadores no lo permiten en type=password */ }
            });
        }

        // 2. Avisar del bloqueo de mayúsculas: es la causa más común de
        //    "la contraseña es correcta y no entra".
        if (clave && aviso) {
            var revisar = function (e) {
                if (typeof e.getModifierState !== 'function') return;
                aviso.hidden = !e.getModifierState('CapsLock');
            };
            clave.addEventListener('keydown', revisar);
            clave.addEventListener('keyup', revisar);
            clave.addEventListener('blur', function () { aviso.hidden = true; });
        }
    })();
    </script>

</body>
</html>
