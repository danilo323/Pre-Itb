<?php
// admin/config.example.php
// Plantilla de configuración del panel ITB (SÍ va al repositorio)
// Copia este archivo a admin/config.php y ajusta los valores reales.
//
// Cada valor puede venir de una variable de entorno (Railway / Docker);
// si la variable no existe se usa el valor por defecto escrito aquí.
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
    'admin_user'            => $env('ADMIN_USER', 'admin'),
    // Genera un hash real con: php -r "echo password_hash('tu_password', PASSWORD_DEFAULT);"
    'admin_hash'            => $env('ADMIN_HASH', '$2y$12$exampleexampleexampleexampleexampleexampleexampleexam'),

    // Conexión a Base de Datos MySQL (Opcional si se usa storage JSON)
    'db_host'               => $env('MYSQLHOST', '127.0.0.1'),
    'db_port'               => (int)$env('MYSQLPORT', 3306),
    'db_name'               => $env('MYSQLDATABASE', 'itb_admin'),
    'db_user'               => $env('MYSQLUSER', 'root'),
    'db_pass'               => $env('MYSQLPASSWORD', ''),

    // Parámetros de sesión y seguridad
    'session_name'          => 'itb_admin_sess',
    'session_timeout'       => 3600, // 1 hora de inactividad

    // Protección contra fuerza bruta (Rate Limiting)
    'login_max_attempts'    => 5,
    'login_lockout_seconds' => 900, // 15 minutos de bloqueo

    // Rutas del sistema
    'data_path'             => __DIR__ . '/../data',
    'uploads_path'          => __DIR__ . '/../img',
    'max_upload_mb'         => 8,
];
