<?php
// admin/views/layout.php
// Esqueleto principal del Panel de Administración (Versión 2.0 - Rediseño Blanco)

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['admin_logged'])) {
    header('Location: ' . dirname($_SERVER['PHP_SELF'], 1) . '/login.php');
    exit;
}

function layout_sidebar($current_key = ''): string {
    $schema = require __DIR__ . '/../schema_mock.php';
    
    $html = "<div class=\"sidebar-brand\">\n";
    $html .= "    <i class=\"bi bi-mortarboard-fill\"></i>\n";
    $html .= "    <span>Configuración</span>\n";
    $html .= "</div>\n";
    $html .= "<nav class=\"sidebar-nav\">\n";
    $html .= "    <ul>\n";
    $html .= "        <li><a href=\"index.php\"><i class=\"bi bi-speedometer2\"></i> Dashboard</a></li>\n";

    // Agrupar items
    $groups = $schema['groups'] ?? [];
    $items_by_group = [];
    foreach ($schema['items'] as $key => $item) {
        $group = $item['group'] ?? 'otros';
        $items_by_group[$group][$key] = $item;
    }

    foreach ($groups as $group_key => $group_label) {
        if (empty($items_by_group[$group_key])) continue;
        
        $html .= "        <li class=\"sidebar-section-label\">" . htmlspecialchars($group_label) . "</li>\n";
        
        foreach ($items_by_group[$group_key] as $key => $item) {
            $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
            $icon  = $item['icon'] ?? 'bi bi-file-earmark-text';
            $active = ($current_key === $key) ? 'class="active"' : '';
            
            $url = ($item['type'] === 'collection') ? "coleccion.php?c={$key}" : "singleton.php?c={$key}";
            
            $html .= "        <li><a href=\"{$url}\" {$active}><i class=\"{$icon}\"></i> {$label}</a></li>\n";
        }
    }

    $html .= "    </ul>\n";
    $html .= "</nav>\n";

    return $html;
}

function layout_flash(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) return '';
    $html = '';
    if (!empty($_SESSION['flash_message'])) {
        $msg  = htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES, 'UTF-8');
        $type = $_SESSION['flash_type'] ?? 'success';

        // Icono Bootstrap Icons acorde al tipo (mismo criterio que admin/fields/alert.php)
        $icon = 'bi-check-circle-fill';
        if ($type === 'error') $icon = 'bi-x-octagon-fill';
        if ($type === 'warning') $icon = 'bi-exclamation-triangle-fill';

        $html = "<div id=\"panel-flash\" class=\"flash-message flash-{$type}\"><i class=\"bi {$icon}\"></i> <span>{$msg}</span></div>\n";
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
    return $html;
}

function flash_set(string $message, string $type = 'success'): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type']    = $type;
}

function layout_start(string $title = "Panel de Administración", string $current_key = ''): string {
    $safe_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $sidebar    = layout_sidebar($current_key);
    $flash      = layout_flash();
    $user       = htmlspecialchars($_SESSION['user'] ?? 'HOLA', ENT_QUOTES, 'UTF-8');
    $time = time();

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$safe_title} — Configuración</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/admin.css?v={$time}">
</head>
<body>
    <div class="admin-topbar">
        <div class="topbar-left">
            <!-- Oculto en móvil -->
        </div>
        <div class="topbar-right">
            <a href="../" target="_blank" class="topbar-link"><i class="bi bi-box-arrow-up-right"></i> Ver sitio</a>
            <span class="topbar-user"><i class="bi bi-person-fill"></i> {$user}</span>
            <a href="logout.php" class="topbar-link topbar-logout"><i class="bi bi-door-open-fill"></i> Cerrar sesión</a>
        </div>
    </div>

    <div class="admin-container">
        <!-- Sidebar dinámico -->
        <aside class="admin-sidebar">
            {$sidebar}
        </aside>

        <!-- Contenido principal -->
        <main class="admin-main">
            <div class="admin-content">
                {$flash}
HTML;
}

function layout_end(): string {
    return <<<HTML
            </div> <!-- /.admin-content -->
        </main>
    </div> <!-- /.admin-container -->

    <script src="/admin/assets/admin.js?v=<?= time() ?>"></script>
</body>
</html>
HTML;
}
