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
        // Si es una ruta local de imagen y no existe en disco, usar fallback
        if (is_string($val) && (str_starts_with($val, 'img/') || str_starts_with($val, 'uploads/'))) {
            $fullPath = dirname(__DIR__) . '/' . ltrim($val, '/');
            if (!file_exists($fullPath)) {
                return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
            }
        }
        return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
    }

    if (!empty($_SESSION['admin_data'][$section][$field])) {
        $sessVal = (string)$_SESSION['admin_data'][$section][$field];
        if (str_starts_with($sessVal, 'img/') || str_starts_with($sessVal, 'uploads/')) {
            $fullPath = dirname(__DIR__) . '/' . ltrim($sessVal, '/');
            if (!file_exists($fullPath)) {
                return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
            }
        }
        return htmlspecialchars($sessVal, ENT_QUOTES, 'UTF-8');
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
        // Si es una ruta local de imagen y no existe en disco, usar fallback
        if (is_string($val) && (str_starts_with($val, 'img/') || str_starts_with($val, 'uploads/'))) {
            $fullPath = dirname(__DIR__) . '/' . ltrim($val, '/');
            if (!file_exists($fullPath)) {
                return $default;
            }
        }

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
        $sessVal = $_SESSION['admin_data'][$section][$field];
        if (is_string($sessVal) && (str_starts_with($sessVal, 'img/') || str_starts_with($sessVal, 'uploads/'))) {
            $fullPath = dirname(__DIR__) . '/' . ltrim($sessVal, '/');
            if (!file_exists($fullPath)) {
                return $default;
            }
        }
        return $sessVal;
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
 * Obtiene los items de una colección (como Equipo o Testimonios),
 * leyendo primero de MySQL para persistencia permanente.
 */
function collection_items(string $collection_name): array {
    // 1. Consultar en base de datos MySQL
    $dbJson = storage_get($collection_name, 'items', '');
    if (!empty($dbJson)) {
        $decoded = json_decode($dbJson, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    // 2. Consultar en sesión
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!empty($_SESSION['admin_data'][$collection_name]['items'])) {
        return $_SESSION['admin_data'][$collection_name]['items'];
    }

    // 3. Fallback por defecto si nunca se ha configurado en el panel
    if ($collection_name === 'equipo') {
        return [
            1 => [
                'id' => 1, 
                'orden' => '1',
                'nombre_completo' => 'PhD. Roberto Tolozano Benites', 
                'cargo' => 'Canciller', 
                'linkedin' => '#',
                'email' => '#',
                'foto' => 'img/PHD_Roberto.jpg',
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
                'foto' => 'img/salud.jpg',
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
                'foto' => 'img/student.jpg',
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
                'foto' => 'img/student 2.jpg',
                'mostrar_en_home' => '1', 
                'publicado' => '1'
            ]
        ];
    }

    return [];
}
