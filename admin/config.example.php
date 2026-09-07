<?php
// admin/config.example.php
// Plantilla de configuración del panel ITB (SÍ va al repositorio)
// Copia este archivo a admin/config.php y ajusta los valores reales.

return [
    'admin_user'            => 'admin',
    // Genera un hash real con: php -r "echo password_hash('tu_password', PASSWORD_DEFAULT);"
    'admin_hash'            => '$2y$12$exampleexampleexampleexampleexampleexampleexampleexam',

    // Conexión a Base de Datos MySQL (Opcional si se usa storage JSON)
    'db_host'               => '127.0.0.1',
    'db_port'               => 3306,
    'db_name'               => 'itb_admin',
    'db_user'               => 'root',
    'db_pass'               => '',

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
