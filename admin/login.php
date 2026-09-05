<?php
// admin/login.php
session_start();

// Si ya está logueado, ir directo al panel
if (!empty($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    // Credenciales temporales (Persona 3 reemplazará con hash real)
    if ($user === 'admin' && $pass === '1234') {
        $_SESSION['admin_logged'] = true;
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Ruta relativa al CSS (funciona siempre) -->
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="login-body">

    <div class="login-card">

        <div class="login-header">
            <h2>🎓 ITB Admin</h2>
            <p>Panel de Administración — Acceso Privado</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="flash-message flash-error">
                ❌ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="login-form">

            <div class="field-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" placeholder="admin" required autofocus>
            </div>

            <div class="field-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="login-hint">
                💡 Credenciales temporales: <strong>admin</strong> / <strong>1234</strong>
            </div>

            <button type="submit" class="btn-block">Entrar al Panel →</button>

        </form>

    </div>

</body>
</html>
