<?php
// includes/content_helper.php
// Lee el contenido editable (Versión 2.0)

function content_get(string $section, string $field, string $default = ''): string {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $value = $_SESSION['admin_data'][$section][$field] ?? $default;
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function content_raw(string $section, string $field, $default = '') {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    return $_SESSION['admin_data'][$section][$field] ?? $default;
}

/**
 * Formatea un título permitiendo usar asteriscos *texto* 
 * para pintarlo con la clase text-orange.
 */
function content_title(string $section, string $field, string $default = ''): string {
    $raw = content_raw($section, $field, $default);
    // Escapar todo por seguridad
    $safe = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    // Reemplazar *texto* por <span class="text-orange">texto</span>
    $html = preg_replace('/\*(.*?)\*/', '<span class="text-orange">$1</span>', $safe);
    return $html;
}

/**
 * Verifica si la sección completa debe mostrarse en la Landing Page.
 */
function is_visible(string $section): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    // Si no está seteado en la sesión, por defecto asumimos visible (1)
    $val = $_SESSION['admin_data'][$section]['_visible'] ?? '1';
    return $val === '1';
}

/**
 * Obtiene los items de una colección (como Equipo o Testimonios).
 * (Simulación: En el futuro Persona 3 conectará esto a la BD).
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
