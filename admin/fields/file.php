<?php
// admin/fields/file.php
//
// Campo de documento genérico. YA NO SUBE ARCHIVOS: los documentos entran por Globales →
// Documentos y aquí solo se ELIGE uno de los que ya están ahí.

require_once __DIR__ . '/../documentos_lib.php';

function field_file_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val   = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help  = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';

    $has_file = !empty($val);

    // ¿El archivo sigue estando?
    $file_exists = true;
    if ($has_file && strpos($val, 'http') !== 0) {
        $file_exists = file_exists(dirname(__DIR__, 2) . '/' . ltrim($val, '/'));
    }

    $show_preview = $has_file && $file_exists;
    $preview_hidden_class     = $show_preview ? '' : ' is-hidden';
    $placeholder_hidden_class = $show_preview ? ' is-hidden' : '';

    $placeholder_icon = 'bi-file-earmark-pdf';
    $placeholder_text = 'Ningún documento seleccionado';
    if ($has_file && !$file_exists) {
        $placeholder_icon = 'bi-exclamation-triangle-fill';
        $placeholder_text = 'El documento ya no está en la galería';
    }

    $texto_boton = $has_file ? 'Cambiar documento' : 'Elegir de Documentos';
    
    // Icono dinámico según extensión
    $ext = pathinfo($val, PATHINFO_EXTENSION);
    $ext = strtolower($ext);
    $doc_icon = 'bi-file-earmark-text';
    if ($ext === 'pdf') $doc_icon = 'bi-file-earmark-pdf-fill';
    elseif (in_array($ext, ['doc', 'docx'])) $doc_icon = 'bi-file-earmark-word-fill';
    elseif (in_array($ext, ['xls', 'xlsx'])) $doc_icon = 'bi-file-earmark-excel-fill';

    $doc_name = basename($val);

    return <<<HTML
<div class="form-group field-image">
    <label>{$label}</label>
    {$help}

    <div class="image-preview-wrapper" style="border: 1px dashed #ccc; padding: 15px; border-radius: 8px; text-align: center;">
        <div class="image-preview{$preview_hidden_class}" style="margin-bottom: 10px;">
            <i class="bi {$doc_icon}" style="font-size: 3rem; color: #1A3B70;"></i>
            <div style="margin-top: 10px; font-weight: 500;">{$doc_name}</div>
        </div>

        <div class="image-placeholder{$placeholder_hidden_class}" style="margin-bottom: 10px; color: #666;">
            <i class="bi {$placeholder_icon}" style="font-size: 2rem;"></i>
            <div style="margin-top: 5px;">{$placeholder_text}</div>
        </div>

        <div class="image-actions">
            <button type="button" class="btn btn-outline js-abrir-documentos">
                <i class="bi bi-folder-fill"></i> <span class="js-texto-elegir">{$texto_boton}</span>
            </button>
            <button type="button" class="btn btn-danger btn-remove-image{$preview_hidden_class}" title="Quitar documento">
                <i class="bi bi-x-circle"></i> Quitar
            </button>
            <input type="hidden" name="{$name_path}" value="{$val}" class="js-doc-input">
        </div>
    </div>
</div>
HTML;
}

function field_file_parse($raw, array $config) {
    $old_val = $config['_old_value'] ?? '';

    if (!is_string($raw)) return $old_val;
    if ($raw === '') return '';
    if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) return $raw;

    if (documentos_ruta_valida($raw)) {
        return documentos_ruta(documentos_nombre_de_ruta($raw));
    }

    return $old_val;
}
