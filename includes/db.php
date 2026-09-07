<?php
// includes/db.php
// Conexión centralizada a la base de datos MySQL mediante PDO

/**
 * Obtiene la instancia única de conexión PDO a MySQL.
 *
 * @throws RuntimeException Si ocurre un error de conexión
 * @return PDO
 */
function db_connect(): PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $configFile = dirname(__DIR__) . '/admin/config.php';
    if (!file_exists($configFile)) {
        throw new RuntimeException("Archivo de configuración no encontrado: admin/config.php");
    }

    $config = require $configFile;

    $host    = $config['db_host'] ?? '127.0.0.1';
    $port    = $config['db_port'] ?? 3306;
    $dbname  = $config['db_name'] ?? 'itb_admin';
    $user    = $config['db_user'] ?? 'root';
    $pass    = $config['db_pass'] ?? '';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión a la base de datos ITB: " . $e->getMessage());
        throw new RuntimeException("No fue posible conectar con la base de datos.");
    }
}
