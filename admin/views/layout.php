<?php
// admin/views/layout.php
// Esqueleto principal del Panel de Administración (Responsabilidad: Persona 2)

// Iniciar sesión si no está iniciada
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Protección de acceso — redirige al login si no hay sesión
if (empty($_SESSION['admin_logged'])) {
    header('Location: ' . dirname($_SERVER['PHP_SELF'], 1) . '/login.php');
    exit;
}

/**
 * Genera el sidebar dinámicamente leyendo el schema.
 */
function layout_sidebar(): string {
    $schema = require __DIR__ . '/../schema_mock.php';

    $html = "<li><a href=\"index.php\">🏠 Dashboard</a></li>\n";
    $html .= "<li class=\"sidebar-section-label\">CONTENIDO</li>\n";

    foreach ($schema as $key => $section) {
        $label = htmlspecialchars($section['label'], ENT_QUOTES, 'UTF-8');
        $type  = $section['type'];
        $icon  = ($type === 'singleton') ? '✏️' : '📋';

        if ($type === 'singleton') {
            $html .= "<li><a href=\"singleton.php?c={$key}\">{$icon} {$label}</a></li>\n";
        } elseif ($type === 'collection') {
            $html .= "<li><a href=\"coleccion.php?c={$key}\">{$icon} {$label}</a></li>\n";
        }
    }

    return $html;
}

/**
 * Muestra flash messages una sola vez.
 */
function layout_flash(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) return '';

    $html = '';
    if (!empty($_SESSION['flash_message'])) {
        $msg  = htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES, 'UTF-8');
        $type = $_SESSION['flash_type'] ?? 'success';
        $html = "<div class=\"flash-message flash-{$type}\">{$msg}</div>\n";
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
    return $html;
}

/**
 * Helper para guardar un flash message desde cualquier pantalla.
 */
function flash_set(string $message, string $type = 'success'): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type']    = $type;
}

function layout_start(string $title = "Panel de Administración"): string {
    $safe_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $sidebar    = layout_sidebar();
    $flash      = layout_flash();
    $user       = htmlspecialchars($_SESSION['user'] ?? 'Admin', ENT_QUOTES, 'UTF-8');

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$safe_title} — ITB Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body>
    <div class="admin-container">

        <!-- Sidebar dinámico generado del schema -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <h2>ITB Admin</h2>
                <p>Panel de Control</p>
            </div>
            <nav>
                <ul>
                    {$sidebar}
                </ul>
            </nav>
            <div class="sidebar-footer">
                <span style="color:rgba(255,255,255,0.5);font-size:0.8rem;">👤 {$user}</span>
                <a href="logout.php" style="margin-top:8px;">🚪 Cerrar Sesión</a>
            </div>
        </aside>

        <!-- Contenido principal -->
        <main class="admin-main">
            <div class="admin-topbar">
                <h1>{$safe_title}</h1>
                <a href="/" target="_blank" class="btn btn-outline btn-sm">👁️ Ver Landing Page</a>
            </div>
            <div class="admin-content">
                {$flash}
HTML;
}

function layout_end(): string {
    return <<<HTML
            </div> <!-- /.admin-content -->
        </main>
    </div> <!-- /.admin-container -->

    <script src="/admin/assets/admin.js"></script>
</body>
</html>
HTML;
}
