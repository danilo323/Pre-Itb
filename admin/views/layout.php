<?php
// admin/views/layout.php
// Esqueleto principal del Panel de Administración (Versión 2.0 - Rediseño Blanco)

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../base_url.php';
auth_require();

require_once __DIR__ . '/../csrf.php';
csrf_token(); // asegura que $_SESSION['csrf_token'] exista antes de renderizar formularios

function layout_sidebar($current_key = ''): string {
    $schema = require __DIR__ . '/../schema_mock.php';
    $ab = admin_base();

    $html = "<div class=\"sidebar-brand\">\n";
    $html .= "    <i class=\"bi bi-mortarboard-fill\"></i>\n";
    $html .= "    <span>Configuración</span>\n";
    $html .= "</div>\n";
    $html .= "<nav class=\"sidebar-nav\">\n";
    $html .= "    <ul>\n";
    $html .= "        <li><a href=\"{$ab}/index.php\"><i class=\"bi bi-speedometer2\"></i> Dashboard</a></li>\n";

    // Agrupar items
    $groups = $schema['groups'] ?? [];
    $items_by_group = [];
    foreach ($schema['items'] as $key => $item) {
        $group = $item['group'] ?? 'otros';
        $items_by_group[$group][$key] = $item;
    }

    // Cargar páginas personalizadas creadas por el admin
    require_once __DIR__ . '/../storage.php';
    $saved_data = storage_load();
    $paginas_creadas = $saved_data['_paginas_creadas'] ?? [];

    foreach ($groups as $group_key => $group_label) {
        $has_items = !empty($items_by_group[$group_key]);
        $is_paginas_group = ($group_key === 'paginas');

        if (!$has_items && !$is_paginas_group) continue;
        
        $html .= "        <li class=\"sidebar-section-label\">" . htmlspecialchars($group_label) . "</li>\n";
        
        // Renderizar items estáticos del schema
        if ($has_items) {
            foreach ($items_by_group[$group_key] as $key => $item) {
                $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
                $icon  = $item['icon'] ?? 'bi bi-file-earmark-text';
                $active = ($current_key === $key) ? 'class="active"' : '';
                
                if (!empty($item['url'])) {
                    $url = $ab . '/' . ltrim($item['url'], '/');
                } else {
                    $url = ($item['type'] === 'collection') ? "{$ab}/coleccion.php?c={$key}" : "{$ab}/singleton.php?c={$key}";
                }
                
                $html .= "        <li><a href=\"{$url}\" {$active}><i class=\"{$icon}\"></i> {$label}</a></li>\n";
            }
        }

        // Si es el grupo de PÁGINAS, inyectar páginas creadas dinámicamente y el botón "Crear Página"
        if ($is_paginas_group) {
            foreach ($paginas_creadas as $p_id => $p_data) {
                $p_label = htmlspecialchars($p_data['nombre'] ?? 'Página Sin Título', ENT_QUOTES, 'UTF-8');
                $p_icon  = htmlspecialchars($p_data['icon'] ?? 'bi bi-file-earmark-text', ENT_QUOTES, 'UTF-8');
                $p_key   = 'custom_' . $p_id;
                $active  = ($current_key === $p_key) ? 'class="active"' : '';
                $p_url   = "{$ab}/pagina_custom.php?id=" . urlencode($p_id);

                $html .= "        <li><a href=\"{$p_url}\" {$active}><i class=\"{$p_icon}\"></i> {$p_label}</a></li>\n";
            }

            // Elemento especial FIJO: Crear Página (ubicado siempre al final de PÁGINAS)
            $btn_active = ($current_key === 'paginas_crear') ? 'class="active"' : '';
            $html .= "        <li><a href=\"{$ab}/pagina_custom.php\" {$btn_active}><i class=\"bi bi-plus-circle-fill\" style=\"color: #F15A24;\"></i> <strong>Crear página</strong></a></li>\n";
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
    global $admin_page_css;
    $safe_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $sidebar    = layout_sidebar($current_key);
    $flash      = layout_flash();
    $user       = htmlspecialchars($_SESSION['user'] ?? 'HOLA', ENT_QUOTES, 'UTF-8');
    $time       = time();
    $ab         = admin_base();
    $sb         = site_base();
    $extra_css = '';
    foreach ((array)($admin_page_css ?? []) as $asset) {
        $href = preg_match('#^https?://#', $asset) ? $asset : "{$ab}/assets/" . ltrim($asset, '/');
        $version = preg_match('#^https?://#', $asset) ? '' : "?v={$time}";
        $extra_css .= "    <link rel=\"stylesheet\" href=\"{$href}{$version}\">\n";
    }

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
    <link rel="stylesheet" href="{$ab}/assets/admin.css?v={$time}">
    <link rel="stylesheet" href="{$ab}/assets/biblioteca.css?v={$time}">
    <link rel="stylesheet" href="{$ab}/assets/documentos.css?v={$time}">
{$extra_css}</head>
<body>
    <div class="admin-topbar">
        <div class="topbar-left">
            <!-- Oculto en móvil -->
        </div>
        <div class="topbar-right">
            <a href="{$sb}" target="_blank" class="topbar-link"><i class="bi bi-box-arrow-up-right"></i> Ver sitio</a>
            <span class="topbar-user"><i class="bi bi-person-fill"></i> {$user}</span>
            <a href="{$ab}/logout.php" class="topbar-link topbar-logout"><i class="bi bi-door-open-fill"></i> Cerrar sesión</a>
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
    global $admin_page_js;
    $ab   = admin_base();
    $time = time();
    $extra_js = '';
    foreach ((array)($admin_page_js ?? []) as $asset) {
        $src = preg_match('#^https?://#', $asset) ? $asset : "{$ab}/assets/" . ltrim($asset, '/');
        $version = preg_match('#^https?://#', $asset) ? '' : "?v={$time}";
        $extra_js .= "    <script src=\"{$src}{$version}\"></script>\n";
    }
    return <<<HTML
            </div> <!-- /.admin-content -->
        </main>
    </div> <!-- /.admin-container -->

    <script src="{$ab}/assets/admin.js?v={$time}"></script>
    <script src="{$ab}/assets/biblioteca.js?v={$time}"></script>
    <script src="{$ab}/assets/documentos.js?v={$time}"></script>
{$extra_js}</body>
</html>
HTML;
}
