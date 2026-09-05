<?php
// includes/content_helper.php
// Lee el contenido editable: primero de la base de datos MySQL (persistente),
// si no existe, devuelve el valor por defecto del HTML original.

require_once dirname(__DIR__) . '/admin/storage.php';

/**
 * Obtiene el contenido escapado contra XSS.
 *
 * @param string $section
 * @param string $field
 * @param string $default
 * @return string
 */
function content_get(string $section, string $field, string $default = ''): string {
    $val = storage_get($section, $field, '');
    if ($val !== '') {
        return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
    }

    if (!empty($_SESSION['admin_data'][$section][$field])) {
        return htmlspecialchars((string)$_SESSION['admin_data'][$section][$field], ENT_QUOTES, 'UTF-8');
    }

    return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
}

/**
 * Versión sin escapar para URLs, src de imágenes, arrays, etc.
 *
 * @param string $section
 * @param string $field
 * @param mixed $default
 * @return mixed
 */
function content_raw(string $section, string $field, $default = '') {
    $val = storage_get($section, $field, '');
    if ($val !== '') {
        // Si el valor es JSON (por ejemplo menús o repeaters), decodificarlo
        if (is_string($val) && (str_starts_with($val, '{') || str_starts_with($val, '['))) {
            $json = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }
        }
        return $val;
    }

    if (!empty($_SESSION['admin_data'][$section][$field])) {
        return $_SESSION['admin_data'][$section][$field];
    }

    return $default;
}

/**
 * Formatea un título permitiendo usar asteriscos *texto* 
 * para pintarlo con la clase text-orange.
 */
function content_title(string $section, string $field, string $default = ''): string {
    $raw = content_raw($section, $field, $default);
    $safe = htmlspecialchars((string)$raw, ENT_QUOTES, 'UTF-8');
    $html = preg_replace('/\*(.*?)\*/', '<span class="text-orange">$1</span>', $safe);
    return $html;
}

/**
 * Verifica si la sección completa debe mostrarse en la Landing Page.
 */
function is_visible(string $section): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $val = storage_get($section, '_visible', '');
    if ($val === '') {
        $val = $_SESSION['admin_data'][$section]['_visible'] ?? '1';
    }
    return $val === '1';
}

/**
 * Obtiene los items de una colección (como Equipo o Testimonios).
 */
function collection_items(string $collection_name): array {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    
    if (!isset($_SESSION['admin_data'][$collection_name]['items'])) {
        if ($collection_name === 'equipo') {
            $_SESSION['admin_data'][$collection_name]['items'] = [
                1 => [
                    'id' => 1, 
                    'orden' => '1',
                    'nombre_completo' => 'PhD. Roberto Tolozano Benites', 
                    'cargo' => 'Canciller', 
                    'linkedin' => '#',
                    'email' => '#',
                    'foto' => 'img/autoridad-1.jpg',
                    'mostrar_en_home' => '1', 
                    'publicado' => '1'
                ],
                2 => [
                    'id' => 2, 
                    'orden' => '2',
                    'nombre_completo' => 'PhD. Elena Tolozano Benites', 
                    'cargo' => 'Rectora', 
                    'linkedin' => '#',
                    'email' => '#',
                    'foto' => 'img/autoridad-2.jpg',
                    'mostrar_en_home' => '1', 
                    'publicado' => '1'
                ],
                3 => [
                    'id' => 3, 
                    'orden' => '3',
                    'nombre_completo' => 'PhD. Luis Alzate Peralta', 
                    'cargo' => 'Vicerrector Académico y de Investigación', 
                    'linkedin' => '#',
                    'email' => '#',
                    'foto' => 'img/autoridad-3.jpg',
                    'mostrar_en_home' => '1', 
                    'publicado' => '1'
                ],
                4 => [
                    'id' => 4, 
                    'orden' => '4',
                    'nombre_completo' => 'PhD. Michelle Tolozano Lapierre', 
                    'cargo' => 'Vicerrectora de Extensión y Gestión Administrativa', 
                    'linkedin' => '#',
                    'email' => '#',
                    'foto' => 'img/autoridad-4.jpg',
                    'mostrar_en_home' => '1', 
                    'publicado' => '1'
                ]
            ];
        } else {
            $_SESSION['admin_data'][$collection_name]['items'] = [];
        }
    }
    
    return $_SESSION['admin_data'][$collection_name]['items'];
}
