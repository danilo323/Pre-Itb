<?php
// admin/fields/repeater.php

function field_repeater_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $item_label = htmlspecialchars($config['item_label'] ?? 'Item', ENT_QUOTES, 'UTF-8');
    $subfields = $config['subfields'] ?? [];
    $help = isset($config['help']) ? '<small class="repeater-help">' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';

    // Si no hay valor previo, asumimos un array vacío
    $items = is_array($value) ? $value : [];

    $html = "<div class='repeater-group' data-name-path='{$name_path}' data-item-label='{$item_label}'>\n";
    if (!empty($label)) {
        $html .= "  <label class='repeater-label'>{$label}</label>\n";
    }
    $html .= $help;
    $html .= "  <div class='repeater-items'>\n";

    // Renderizar los items existentes
    foreach ($items as $index => $item_data) {
        $display_index = $index + 1;
        $html .= "    <div class='repeater-item' data-index='{$index}'>\n";
        $html .= "      <div class='repeater-item-header'>\n";
        $html .= "          <h4 class='repeater-item-title'>{$item_label} {$display_index}</h4>\n";

        if (empty($config['fixed_items'])) {
            $html .= "          <div class='repeater-item-actions'>\n";
            $html .= "              <button type='button' class='btn btn-sm btn-outline btn-move-up' title='Subir'><i class='bi bi-arrow-up'></i></button>\n";
            $html .= "              <button type='button' class='btn btn-sm btn-outline btn-move-down' title='Bajar'><i class='bi bi-arrow-down'></i></button>\n";
            $html .= "              <button type='button' class='btn-remove'>Eliminar</button>\n";
            $html .= "          </div>\n";
        }

        $html .= "      </div>\n";
        // Renderizar subcampos
        foreach ($subfields as $sub_key => $sub_config) {
            $sub_name = "{$name_path}[{$index}][{$sub_key}]";
            $sub_val = $item_data[$sub_key] ?? '';
            // Si el subcampo es imagen, ocultar su propio label si se llama "Foto de Fondo" o dejarlo
            if (isset($sub_config['label']) && $sub_config['label'] === 'Foto de Fondo') {
                $sub_config['label'] = ''; // Ocultar para que sea más limpio
            }
            $html .= field_render($sub_name, $sub_val, $sub_config);
        }
        $html .= "    </div>\n";
    }
    
    $html .= "  </div>\n";
    
    // Botón para añadir (JavaScript lo usará)
    if (empty($config['fixed_items'])) {
        $html .= "  <button type='button' class='btn-add btn btn-outline'><i class='bi bi-plus-circle'></i> Añadir {$item_label}</button>\n";
    }
    $html .= "</div>\n";
    
    return $html;
}

function field_repeater_parse($raw, array $config) {
    if (!is_array($raw)) return [];
    
    $cleaned = [];
    $subfields = $config['subfields'] ?? [];
    
    // Extraer valores antiguos si existen (para pasarlos a los subcampos)
    // El motor inyecta _old_value como el array del repeater anterior
    $old_items = is_array($config['_old_value'] ?? null) ? $config['_old_value'] : [];
    
    foreach ($raw as $index => $item) {
        if (!is_array($item)) continue;
        
        $cleaned_item = [];
        $old_item = $old_items[$index] ?? [];
        
        foreach ($subfields as $sub_key => $sub_config) {
            // Reconstruir el name_path exacto que usó el frontend
            $sub_name_path = $config['name_path'] . '[' . $index . '][' . $sub_key . ']';
            $sub_config['name_path'] = $sub_name_path;
            
            // Inyectar el valor viejo del subcampo
            $sub_config['_old_value'] = $old_item[$sub_key] ?? ($sub_config['default'] ?? '');
            
            // El input hidden o el valor post enviado
            $raw_sub_val = $item[$sub_key] ?? null;
            
            $cleaned_item[$sub_key] = field_parse($sub_config['type'], $raw_sub_val, $sub_config);
        }
        
        // Solo guardar si no está vacío
        if (!empty(array_filter($cleaned_item))) {
            $cleaned[] = $cleaned_item;
        }
    }
    
    return array_values($cleaned); // Reindexar
}
