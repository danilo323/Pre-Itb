<?php
// admin/panel_overlay.php

if (empty($_SESSION['admin_logged'])) return;

require_once __DIR__ . '/fields/_loader.php';
$schema    = require __DIR__ . '/schema_mock.php';
$csrf      = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$user      = htmlspecialchars($_SESSION['user'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
$saved     = $_SESSION['admin_data'] ?? [];

// Flash message
$flash_html = '';
if (!empty($_SESSION['flash_message'])) {
    $msg  = htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES, 'UTF-8');
    $type = $_SESSION['flash_type'] ?? 'success';
    $flash_html = "<div class=\"panel-flash panel-flash--{$type}\" id=\"panel-flash\">{$msg}</div>";
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Cards de sección (el ID del HTML del frontend coincide con la clave del schema)
$section_cards = '';
foreach ($schema as $key => $section) {
    $label  = htmlspecialchars($section['label'], ENT_QUOTES, 'UTF-8');
    $icon   = htmlspecialchars($section['icon'] ?? '📄', ENT_QUOTES, 'UTF-8');
    $type   = $section['type'];
    $badge  = ($type === 'collection')
        ? '<span class="scard-badge scard-badge--list">📋 Lista</span>'
        : '<span class="scard-badge scard-badge--single">✏️ Único</span>';

    $section_cards .= "
    <button class=\"section-card\" data-section=\"{$key}\" data-anchor=\"{$key}\">
        <span class=\"scard-icon\">{$icon}</span>
        <span class=\"scard-label\">{$label}</span>
        {$badge}
    </button>";
}

// Formularios de cada sección
$section_forms = '';
foreach ($schema as $key => $section) {
    $label      = htmlspecialchars($section['label'], ENT_QUOTES, 'UTF-8');
    $icon       = htmlspecialchars($section['icon'] ?? '📄', ENT_QUOTES, 'UTF-8');
    $type_label = ($section['type'] === 'collection') ? '📋 Colección — lista de items' : '✏️ Singleton — registro único';

    $fields_html = '';
    foreach ($section['fields'] as $field_key => $field_config) {
        // Usar el valor guardado en sesión si existe, si no el default del schema
        $value = $saved[$key][$field_key] ?? ($field_config['default'] ?? null);
        $fields_html .= field_render($field_key, $value, $field_config);
    }

    $section_forms .= "
    <div class=\"panel-section-form\" id=\"form-{$key}\" style=\"display:none;\">
        <div class=\"pform-header\">
            <button class=\"pform-back\" data-back>← Volver</button>
            <div class=\"pform-title\">
                <span>{$icon} {$label}</span>
                <small>{$type_label}</small>
            </div>
        </div>
        <form method=\"POST\" action=\"/admin/guardar.php\" class=\"panel-form\" enctype=\"multipart/form-data\">
            <input type=\"hidden\" name=\"section\" value=\"{$key}\">
            <input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">
            <div class=\"pform-fields\">
                {$fields_html}
            </div>
            <div class=\"panel-form-actions\">
                <button type=\"submit\" class=\"panel-btn-save\">💾 Guardar Cambios</button>
            </div>
        </form>
    </div>";
}
?>

<!-- ====== PANEL FLOTANTE ADMIN ====== -->
<link rel="stylesheet" href="/admin/assets/admin.css">

<div id="admin-panel" class="admin-panel">

    <!-- Header -->
    <div class="panel-header">
        <span class="panel-title">⚙️ PANEL ADMIN</span>
        <div style="display:flex;align-items:center;gap:8px;">
            <button class="panel-close" id="panel-close-btn">✕</button>
        </div>
    </div>

    <!-- Flash message -->
    <?= $flash_html ?>

    <!-- Tabs -->
    <div class="panel-tabs">
        <button class="panel-tab active" data-tab="secciones">SECCIONES</button>
        <button class="panel-tab" data-tab="cuenta">CUENTA</button>
    </div>

    <!-- TAB SECCIONES -->
    <div class="panel-tab-content active" id="tab-secciones">

        <!-- Vista: Grid de secciones -->
        <div id="panel-sections-grid" class="panel-sections-grid">
            <p class="grid-hint">Haz clic para ir a la sección y editarla</p>
            <div class="section-cards-grid">
                <?= $section_cards ?>
            </div>
        </div>

        <!-- Vista: Formulario de edición (oculta por defecto) -->
        <div id="panel-form-container">
            <?= $section_forms ?>
        </div>

    </div>

    <!-- TAB CUENTA -->
    <div class="panel-tab-content" id="tab-cuenta" style="display:none;">
        <div class="panel-account-info">
            <div class="account-avatar">👤</div>
            <p class="account-name"><?= $user ?></p>
            <p class="account-role">Administrador</p>
            <a href="/admin/logout.php" class="panel-btn-logout">🚪 Cerrar Sesión</a>
        </div>
    </div>

</div>

<!-- Botón flotante para reabrir -->
<button id="admin-toggle-btn" class="admin-toggle-btn" title="Abrir Panel Admin">⚙️</button>

<script src="/admin/assets/admin.js"></script>
