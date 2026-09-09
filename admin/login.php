<?php
// admin/login.php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/base_url.php';

auth_session_start();
$AB = admin_base();

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
</head>
<body class="login-body">

    <div class="login-card">

        <div class="login-header">
            <div class="login-brand-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h2>ITB Admin</h2>
            <p>Panel de Administración — Acceso Privado</p>
        </div>

        <?php if (!empty($notice)): ?>
            <div class="flash-message flash-info">
                <i class="bi bi-info-circle-fill"></i>
                <?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="flash-message flash-error">
                <i class="bi bi-x-octagon-fill"></i>
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="login-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

            <div class="field-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" placeholder="admin" required autofocus autocomplete="username">
            </div>

            <div class="field-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-block <?= $isLocked ? 'is-locked' : '' ?>" <?= $isLocked ? 'disabled' : '' ?>>
                Entrar al Panel <i class="bi bi-arrow-right"></i>
            </button>

        </form>

    </div>

</body>
</html>
