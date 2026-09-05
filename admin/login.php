<?php
// admin/login.php
// Acceso seguro al panel de administración ITB

require_once __DIR__ . '/auth.php';
auth_session_start();

// Si ya está logueado, ir directo al panel
if (auth_check()) {
    header('Location: index.php');
    exit;
}

$error = '';
$notice = '';

if (!empty($_GET['logout'])) {
    $notice = 'Has cerrado sesión correctamente.';
} elseif (!empty($_GET['expired'])) {
    $error = 'Tu sesión ha expirado por inactividad. Inicia sesión nuevamente.';
}

$rl = auth_rate_limit_check();
$isLocked = !$rl['allowed'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isLocked) {
        $minutes = ceil($rl['retry_after'] / 60);
        $error = "Demasiados intentos fallidos. Por seguridad, el acceso está bloqueado por {$minutes} minuto(s).";
    } elseif (!csrf_verify()) {
        $error = 'Token de seguridad inválido o expirado. Por favor, recarga e inténtalo de nuevo.';
    } else {
        $identifier = trim($_POST['email'] ?? $_POST['username'] ?? '');
        $password   = (string)($_POST['password'] ?? '');

        if (auth_attempt($identifier, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $rlNow = auth_rate_limit_check();
            if (!$rlNow['allowed']) {
                $minutes = ceil($rlNow['retry_after'] / 60);
                $error = "Demasiados intentos fallidos. Tu acceso ha sido bloqueado por {$minutes} minuto(s).";
                $isLocked = true;
            } else {
                $rem = $rlNow['remaining'];
                $error = "Credenciales incorrectas. Intentos restantes: {$rem}.";
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
    <title>Acceso Seguro — ITB Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="login-body">

    <div class="login-card">

        <div class="login-header">
            <h2>ITB Admin</h2>
            <p>Panel de Administración — Acceso Seguro</p>
        </div>

        <?php if (!empty($notice)): ?>
            <div class="flash-message flash-success" style="margin-bottom: 16px;">
                <?= h($notice) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="flash-message flash-error" style="margin-bottom: 16px;">
                <?= h($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">

            <?= csrf_input() ?>

            <div class="field-group">
                <label for="email">Correo electrónico o Usuario</label>
                <input 
                    type="text" 
                    id="email" 
                    name="email" 
                    placeholder="admin@itb.edu.ec" 
                    value="<?= h($_POST['email'] ?? '') ?>"
                    required 
                    autofocus
                    <?= $isLocked ? 'disabled' : '' ?>
                >
            </div>

            <div class="field-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••" 
                    required
                    <?= $isLocked ? 'disabled' : '' ?>
                >
            </div>

            <button type="submit" class="btn-block" <?= $isLocked ? 'disabled' : '' ?>>
                <?= $isLocked ? 'Acceso Bloqueado' : 'Entrar al Panel →' ?>
            </button>

        </form>

    </div>

</body>
</html>
