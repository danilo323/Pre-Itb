<?php
// admin/config.php
// Configuración del panel ITB.
//
// Lee variables de entorno (Railway / Docker) y, si no están definidas, usa
// los valores locales de siempre (XAMPP, php -S). Así el mismo archivo sirve
// en local sin configurar nada y en el entorno de prueba con solo definir
// variables en el servicio.
//
// Variables soportadas:
//   ADMIN_USER, ADMIN_HASH            -> credenciales del panel
//   MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD
//                                     -> conexión MySQL (nombres del plugin MySQL de Railway)

$env = static function (string $key, $default) {
    $v = getenv($key);
    return ($v === false || $v === '') ? $default : $v;
};

return [
    // Credenciales de acceso al panel (Demo local: admin / 1234)
    'admin_user'            => $env('ADMIN_USER', 'admin'),
    'admin_hash'            => $env('ADMIN_HASH', '$2y$12$Vs2sYUVTTC9a8WNxxZXtNuysplnps6TO9Z.8j9UaeTRF4sZWYkaR.'),

    // Conexión a Base de Datos MySQL
    'db_host'               => $env('MYSQLHOST', '127.0.0.1'),
    'db_port'               => (int)$env('MYSQLPORT', 3306),
    'db_name'               => $env('MYSQLDATABASE', 'itb_admin'),
    'db_user'               => $env('MYSQLUSER', 'root'),
    'db_pass'               => $env('MYSQLPASSWORD', '123456789'),

    // Parámetros de sesión
    'session_name'          => 'itb_admin_sess',
    'session_timeout'       => 3600, // 1 hora de inactividad

    // Protección contra fuerza bruta (Rate Limiting)
    'login_max_attempts'    => 5,
    'login_lockout_seconds' => 900, // 15 minutos de bloqueo

    // Rutas del sistema
    'data_path'             => __DIR__ . '/../data',
    'uploads_path'          => __DIR__ . '/../uploads',
    'max_upload_mb'         => 32,
];
