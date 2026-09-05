<?php
// admin/fields/repeater.php

function field_repeater_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $subfields = $config['subfields'] ?? [];
    
    // Si no hay valor previo, asumimos un array vacío
    $items = is_array($value) ? $value : [];
    
    $html = "<div class='repeater-group' data-name-path='{$name_path}'>\n";
    $html .= "  <label>{$label}</label>\n";
    $html .= "  <div class='repeater-items'>\n";
    
    // Renderizar los items existentes
    foreach ($items as $index => $item_data) {
        $html .= "    <div class='repeater-item' data-index='{$index}'>\n";
        $html .= "      <h4>Item {$index} <button type='button' class='btn-remove'>Eliminar</button></h4>\n";
        // Renderizar subcampos
        foreach ($subfields as $sub_key => $sub_config) {
            $sub_name = "{$name_path}[{$index}][{$sub_key}]";
            $sub_val = $item_data[$sub_key] ?? '';
            $html .= field_render($sub_name, $sub_val, $sub_config);
        }
        $html .= "    </div>\n";
    }
    
    $html .= "  </div>\n";
    
    // Botón para añadir (JavaScript lo usará)
    $html .= "  <button type='button' class='btn-add'>+ Añadir</button>\n";
    $html .= "</div>\n";
    
    return $html;
}

function field_repeater_parse($raw, array $config) {
    // raw debería ser un array de arrays, por ejemplo:
    // [ 0 => ['pregunta' => '...', 'respuesta' => '...'], 1 => [...] ]
    if (!is_array($raw)) return [];
    
    $cleaned = [];
    $subfields = $config['subfields'] ?? [];
    
    foreach ($raw as $item) {
        if (!is_array($item)) continue;
        
        $cleaned_item = [];
        foreach ($subfields as $sub_key => $sub_config) {
            if (isset($item[$sub_key])) {
                $cleaned_item[$sub_key] = field_parse($sub_config['type'], $item[$sub_key], $sub_config);
            }
        }
        // Solo guardar si no está vacío
        if (!empty(array_filter($cleaned_item))) {
            $cleaned[] = $cleaned_item;
        }
    }
    
    return array_values($cleaned); // Reindexar
}
