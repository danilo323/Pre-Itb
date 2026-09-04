<?php
// admin/views/layout.php
// Esqueleto principal del Panel de Administración (Responsabilidad: Persona 2)

/**
 * Genera el sidebar dinámicamente leyendo el schema.
 * No se escribe a mano: se itera el schema y se arma solo.
 */
function layout_sidebar(): string {
    $schema = require __DIR__ . '/../schema_mock.php';
    
    $html = "<li><a href=\"index.php\">Dashboard</a></li>\n";
    
    foreach ($schema as $key => $section) {
        $label = htmlspecialchars($section['label'], ENT_QUOTES, 'UTF-8');
        $type = $section['type'];
        
        if ($type === 'singleton') {
            $html .= "<li><a href=\"singleton.php?c={$key}\">{$label}</a></li>\n";
        } elseif ($type === 'collection') {
            $html .= "<li><a href=\"coleccion.php?c={$key}\">{$label}</a></li>\n";
        }
    }
    
    return $html;
}

/**
 * Muestra flash messages (mensajes de guardado/error/eliminado).
 * Se guardan en $_SESSION y se muestran UNA sola vez.
 */
function layout_flash(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) return '';
    
    $html = '';
    if (!empty($_SESSION['flash_message'])) {
        $msg = htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES, 'UTF-8');
        $type = $_SESSION['flash_type'] ?? 'success'; // success, error, warning
        $html = "<div class=\"flash-message flash-{$type}\">{$msg}</div>\n";
        
        // Se muestra una vez y se borra
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
    return $html;
}

/**
 * Helper para que singleton.php o coleccion.php guarden un mensaje flash.
 */
function flash_set(string $message, string $type = 'success'): void {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function layout_start($title = "Panel de Administración") {
    $safe_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $sidebar = layout_sidebar();
    $flash = layout_flash();
    
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$safe_title}</title>
    <!-- Aquí irán los estilos propios del admin más adelante (admin/assets/admin.css) -->
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar dinámico generado del schema -->
        <aside class="admin-sidebar">
            <h2>Panel Admin</h2>
            <nav>
                <ul>
                    {$sidebar}
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="admin-main">
            <header>
                <h1>{$safe_title}</h1>
            </header>
            {$flash}
            <div class="admin-content">
HTML;
}

function layout_end() {
    return <<<HTML
            </div> <!-- /.admin-content -->
        </main>
    </div> <!-- /.admin-container -->
    
    <!-- Aquí irán los scripts propios del admin más adelante (admin/assets/admin.js) -->
</body>
</html>
HTML;
}
