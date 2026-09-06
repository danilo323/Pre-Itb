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
 * Arma el texto que gira alrededor de los botones circulares de video.
 *
 * El administrador escribe UNA FRASE POR LÍNEA, sin ningún símbolo raro:
 *
 *     ¿Cómo inscribirse?
 *     Haz clic aquí
 *
 * y esta función las une con " • " y agrega el separador final, para que al
 * dar la vuelta al círculo la última frase no se pegue con la primera.
 *
 * También acepta el formato antiguo (todo en un renglón con los • escritos a
 * mano), así que el contenido ya guardado sigue viéndose igual.
 */
function content_circular(string $section, string $field, string $default = ''): string {
    $raw = (string)content_raw($section, $field, $default);

    // Separa por saltos de línea (formato nuevo) y también por • (formato viejo),
    // así de paso se normaliza el espaciado alrededor de cada punto.
    $partes = preg_split('/[\r\n]+|\s*•\s*/u', $raw);
    if (!is_array($partes)) {
        $partes = [$raw]; // por si el texto trae caracteres inválidos
    }

    $frases = [];
    foreach ($partes as $p) {
        $p = trim($p);
        if ($p !== '') $frases[] = $p;
    }

    if (empty($frases)) return '';

    return htmlspecialchars(implode(' • ', $frases) . ' • ', ENT_QUOTES, 'UTF-8');
}

/**
 * Convierte una URL normal de YouTube a formato "embed", para poder
 * reproducirla dentro del modal del sitio.
 *
 * Acepta las dos formas que un administrador pega normalmente:
 *   https://youtu.be/XXXXXXXXXXX
 *   https://www.youtube.com/watch?v=XXXXXXXXXXX
 *
 * Si no reconoce el formato devuelve la URL tal cual, para no romper nada.
 */
function youtube_embed_url(string $url): string {
    $url = trim($url);
    if ($url === '') return '';

    $patron = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
    if (preg_match($patron, $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1';
    }

    return $url;
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
                    'nombre' => 'Roberto Tolozano Benites',
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
                    'nombre' => 'Elena Tolozano Benites',
                    'nombre_completo' => 'PhD. Elena Tolozano Benites', 
                    'cargo' => 'Rectora', 
                    'linkedin' => '#',
                    'email' => '#',
                    'foto' => 'img/PHD.Elena_Tolozano.jpg',
                    'mostrar_en_home' => '1', 
                    'publicado' => '1'
                ],
                3 => [
                    'id' => 3, 
                    'orden' => '3',
                    'nombre' => 'Luis Alzate Peralta',
                    'nombre_completo' => 'PhD. Luis Alzate Peralta', 
                    'cargo' => 'Vicerrector Académico y de Investigación', 
                    'linkedin' => '#',
                    'email' => '#',
                    'foto' => 'img/PHD.Luis_alzate.jpg',
                    'mostrar_en_home' => '1', 
                    'publicado' => '1'
                ],
                4 => [
                    'id' => 4, 
                    'orden' => '4',
                    'nombre' => 'Michelle Tolozano Lapierre',
                    'nombre_completo' => 'PhD. Michelle Tolozano Lapierre', 
                    'cargo' => 'Vicerrectora de Extensión y Gestión Administrativa', 
                    'linkedin' => '#',
                    'email' => '#',
                    'foto' => 'img/PHD.Michelle_tolozano.webp',
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
