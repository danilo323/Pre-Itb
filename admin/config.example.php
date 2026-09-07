<?php
// admin/config.example.php
// Plantilla de configuración del panel ITB (SÍ va al repositorio)
// Copia este archivo a admin/config.php y ajusta los valores reales.

return [
    'admin_user' => 'admin',
    // Genera un hash real con: php -r "echo password_hash('tu_password', PASSWORD_DEFAULT);"
    'admin_hash' => '$2y$12$exampleexampleexampleexampleexampleexampleexampleexam',
];
