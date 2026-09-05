<?php
// admin/fields/image.php

function field_image_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    
    $has_image = !empty($val);
    // Estilos de visibilidad correctos (sin conflicto)
    $preview_display     = $has_image ? 'block' : 'none';
    $placeholder_display = $has_image ? 'none'  : 'flex';
    $remove_display      = $has_image ? 'inline-flex' : 'none';
    
    // Corregir la ruta de la imagen para que siempre cargue desde la raíz (/) si es relativa
    $img_src = $val;
    if ($has_image && strpos($img_src, 'http') !== 0 && strpos($img_src, '/') !== 0) {
        $img_src = '/' . $img_src;
    }
    
    // Un ID único por si hay varios campos de imagen
    $inputId = 'file_' . md5($name_path . rand());
    
    return <<<HTML
<div class="form-group field-image">
    <label>{$label}</label>
    {$help}
    
    <div class="image-preview-wrapper image-upload-box">
        <!-- Preview (siempre en el DOM para que el JS pueda actualizar el src) -->
        <div class="image-preview" style="display:{$preview_display};">
            <img src="{$img_src}" alt="Preview" style="max-width:100%;max-height:220px;object-fit:contain;border-radius:6px;border:1px solid #e2e8f0;display:block;">
            <div class="image-current-path" style="font-size:12px;color:#64748b;margin-top:8px;">Ruta actual: {$val}</div>
        </div>
        
        <!-- Placeholder cuando no hay imagen -->
        <div class="image-placeholder" style="display:{$placeholder_display};flex-direction:column;align-items:center;justify-content:center;gap:8px;text-align:center;color:#64748b;padding:20px;min-height:100px;">
            <i class="bi bi-image" style="font-size:32px;"></i>
            <span>Arrastra una imagen aqui o haz clic en Seleccionar</span>
        </div>

        <div class="image-actions" style="display:flex;align-items:center;gap:8px;margin-top:12px;flex-wrap:wrap;">
            <label class="btn btn-outline" for="{$inputId}" style="cursor:pointer;">
                <i class="bi bi-folder-fill"></i> Seleccionar archivo
            </label>
            <button type="button" class="btn btn-danger btn-remove-image" style="display:{$remove_display};" title="Eliminar imagen">
                <i class="bi bi-trash-fill"></i> Eliminar
            </button>
            <input type="file" id="{$inputId}" name="{$name_path}_file"
                   accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/avif,image/bmp,image/tiff,image/x-icon,image/heic,image/heif,image/*"
                   style="display:none;">
            <input type="hidden" name="{$name_path}" value="{$val}">
        </div>
    </div>
</div>
HTML;
}

function field_image_parse($raw, array $config) {
    // El motor genérico inyecta 'name_path' y '_old_value'
    $name_path = $config['name_path'] ?? '';
    $old_val = $config['_old_value'] ?? '';
    
    // Si no tenemos nombre del campo, devolvemos el valor por defecto/viejo
    if (!$name_path) return $old_val;
    
    $file_key = $name_path . '_file';
    
    // Si hay una subida válida
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$file_key]['tmp_name'];
        $name     = basename($_FILES[$file_key]['name']);
        
        $upload_dir = dirname(__DIR__, 2) . '/img/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif', 'bmp', 'tiff', 'tif', 'ico', 'heic', 'heif'];
        if (in_array($ext, $allowed)) {
            $cleanBase = preg_replace('/[^a-zA-Z0-9_\-]/', '', pathinfo($name, PATHINFO_FILENAME));
            if (empty($cleanBase)) $cleanBase = 'upload';
            $new_name = time() . '_' . $cleanBase . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            
            if (move_uploaded_file($tmp_name, $dest)) {
                $newRelPath = 'img/' . $new_name;
                // Eliminar imagen anterior si fue subida por el panel
                if (!empty($old_val) && $old_val !== $newRelPath) {
                    require_once dirname(__DIR__) . '/storage.php';
                    storage_delete_old_file($old_val);
                }
                return $newRelPath;
            }
        }
    }
    
    // Si el usuario eliminó la imagen deliberadamente
    if ($raw === '' && !empty($old_val)) {
        require_once dirname(__DIR__) . '/storage.php';
        storage_delete_old_file($old_val);
        return '';
    }
    
    // Si no subieron nada nuevo, mantenemos la imagen anterior
    return !empty($raw) ? $raw : $old_val;
}
