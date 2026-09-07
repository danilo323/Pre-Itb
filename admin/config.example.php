<?php
// admin/config.example.php
// Plantilla de configuración para ITB Admin (SÍ va al repositorio)

return [
    // Conexión a Base de Datos MySQL
    'db_host'               => '127.0.0.1',
    'db_port'               => 3306,
    'db_name'               => 'itb_admin',
    'db_user'               => 'root',
    'db_pass'               => '', // NUNCA poner contraseñas reales en este archivo

    // Parámetros de sesión
    'session_name'          => 'itb_admin_sess',
    'session_timeout'       => 3600, // 1 hora de inactividad

    // Protección contra fuerza bruta (Rate Limiting)
    'login_max_attempts'    => 5,
    'login_lockout_seconds' => 900, // 15 minutos de bloqueo

    // Rutas del sistema
    'data_path'             => __DIR__ . '/../data',
    'uploads_path'          => __DIR__ . '/../uploads',
    'max_upload_mb'         => 8,
];
