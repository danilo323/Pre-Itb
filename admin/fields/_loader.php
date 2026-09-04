<?php
// admin/fields/_loader.php
// Despachador de campos (Dispatcher). Responsabilidad: Persona 2.
// Su función es leer qué tipo de campo pide el schema y cargar el archivo correcto.

// Cargar todos los tipos de campo disponibles
require_once __DIR__ . '/text.php';
require_once __DIR__ . '/textarea.php';
require_once __DIR__ . '/repeater.php';
require_once __DIR__ . '/image.php';
require_once __DIR__ . '/bool.php';
require_once __DIR__ . '/select.php';
require_once __DIR__ . '/date.php';
require_once __DIR__ . '/divider.php';

/**
 * Renderiza el HTML de un campo específico basándose en su tipo.
 *
 * @param string $name_path El atributo 'name' del input (ej: "titulo" o "faq[0][pregunta]")
 * @param mixed $value El valor actual guardado en la base de datos (para pre-llenar el input)
 * @param array $config La configuración del campo extraída del schema (label, help, type, etc.)
 * @return string HTML generado del campo
 */
function field_render(string $name_path, $value, array $config): string {
    $type = $config['type'] ?? 'text';
    $fn = "field_{$type}_render";
    
    // Si la función para renderizar ese tipo de campo existe, la llamamos.
    if (function_exists($fn)) {
        return $fn($name_path, $value, $config);
    }
    
    // Fallback si alguien pide un tipo de campo que no has programado aún
    return "<div style='color:red;'>Error: Tipo de campo '{$type}' no soportado.</div>";
}

/**
 * Recibe el valor enviado por POST y lo limpia/parsea antes de guardarlo.
 */
function field_parse(string $type, $raw_value, array $config) {
    $fn = "field_{$type}_parse";
    
    if (function_exists($fn)) {
        return $fn($raw_value, $config);
    }
    
    // Fallback por defecto: simplemente retornar el valor sanitizado como string
    return htmlspecialchars((string)$raw_value, ENT_QUOTES, 'UTF-8');
}
