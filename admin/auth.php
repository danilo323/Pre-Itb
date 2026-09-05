<?php
// admin/auth.php
// Módulo central de seguridad, sesiones, autenticación y protección CSRF / Rate Limiting

require_once dirname(__DIR__) . '/includes/db.php';

/**
 * Carga la configuración del panel con almacenamiento en caché estático.
 *
 * @return array
 */
function auth_get_config(): array {
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $configFile = __DIR__ . '/config.php';
    if (!file_exists($configFile)) {
        // Fallback a config.example.php si config.php aún no existe
        $exampleFile = __DIR__ . '/config.example.php';
        if (file_exists($exampleFile)) {
            $config = require $exampleFile;
            return $config;
        }
        throw new RuntimeException("No se encontró el archivo de configuración del admin.");
    }

    $config = require $configFile;
    return $config;
}

/**
 * Inicia la sesión PHP con cookies seguras y verifica el timeout por inactividad.
 */
function auth_session_start(): void {
    if (session_status() === PHP_SESSION_NONE) {
        $config = auth_get_config();
        $sessionName = $config['session_name'] ?? 'itb_admin_sess';

        if (!headers_sent()) {
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

            session_name($sessionName);

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        @session_start();
    }

    // Verificación de timeout por inactividad si hay sesión activa
    if (!empty($_SESSION['admin_logged'])) {
        $config = auth_get_config();
        $timeout = (int)($config['session_timeout'] ?? 3600);

        if (isset($_SESSION['last_activity']) && (time() - (int)$_SESSION['last_activity'] > $timeout)) {
            auth_logout();
            header('Location: /admin/login.php?expired=1');
            exit;
        }

        $_SESSION['last_activity'] = time();
    }
}

/**
 * Detecta la dirección IP real del cliente.
 *
 * @return string
 */
function auth_get_client_ip(): string {
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]);
            $ip = trim($ipList[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '127.0.0.1';
}

/**
 * Obtiene la ruta del archivo temporal de rate limiting para una IP.
 *
 * @param string $ip
 * @return string
 */
function _auth_rate_limit_file(string $ip): string {
    return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'itb_rl_' . md5($ip) . '.json';
}

/**
 * Comprueba el estado del rate limiting para la IP actual.
 *
 * @param string|null $ip
 * @return array ['allowed' => bool, 'remaining' => int, 'retry_after' => int]
 */
function auth_rate_limit_check(?string $ip = null): array {
    $ip = $ip ?: auth_get_client_ip();
    $config = auth_get_config();
    $maxAttempts = (int)($config['login_max_attempts'] ?? 5);
    $lockoutSeconds = (int)($config['login_lockout_seconds'] ?? 900);

    $file = _auth_rate_limit_file($ip);
    if (!file_exists($file)) {
        return ['allowed' => true, 'remaining' => $maxAttempts, 'retry_after' => 0];
    }

    $data = @json_decode((string)@file_get_contents($file), true);
    if (!is_array($data) || empty($data['attempts']) || empty($data['last_attempt'])) {
        @unlink($file);
        return ['allowed' => true, 'remaining' => $maxAttempts, 'retry_after' => 0];
    }

    $elapsed = time() - (int)$data['last_attempt'];

    // Si transcurrió el tiempo de bloqueo, se reinicia
    if ($elapsed >= $lockoutSeconds) {
        @unlink($file);
        return ['allowed' => true, 'remaining' => $maxAttempts, 'retry_after' => 0];
    }

    $attempts = (int)$data['attempts'];
    if ($attempts >= $maxAttempts) {
        $retryAfter = $lockoutSeconds - $elapsed;
        return ['allowed' => false, 'remaining' => 0, 'retry_after' => $retryAfter];
    }

    return [
        'allowed'     => true,
        'remaining'   => max(0, $maxAttempts - $attempts),
        'retry_after' => 0,
    ];
}

/**
 * Registra un intento fallido de inicio de sesión para la IP dada.
 *
 * @param string|null $ip
 */
function auth_rate_limit_record_fail(?string $ip = null): void {
    $ip = $ip ?: auth_get_client_ip();
    $file = _auth_rate_limit_file($ip);

    $data = ['attempts' => 0, 'last_attempt' => time()];
    if (file_exists($file)) {
        $existing = @json_decode((string)@file_get_contents($file), true);
        if (is_array($existing) && !empty($existing['attempts'])) {
            $data = $existing;
        }
    }

    $data['attempts'] = ((int)$data['attempts']) + 1;
    $data['last_attempt'] = time();

    @file_put_contents($file, json_encode($data), LOCK_EX);
}

/**
 * Limpia el contador de intentos fallidos tras un login exitoso.
 *
 * @param string|null $ip
 */
function auth_rate_limit_clear(?string $ip = null): void {
    $ip = $ip ?: auth_get_client_ip();
    $file = _auth_rate_limit_file($ip);
    if (file_exists($file)) {
        @unlink($file);
    }
}

/**
 * Intenta autenticar un usuario contra la base de datos MySQL.
 *
 * @param string $identifier Email o nombre de usuario
 * @param string $password Contraseña en texto plano
 * @return bool True si las credenciales son válidas, false en caso contrario
 */
function auth_attempt(string $identifier, string $password): bool {
    $identifier = trim($identifier);
    $password = trim($password);

    if ($identifier === '' || $password === '') {
        return false;
    }

    $ip = auth_get_client_ip();
    $rl = auth_rate_limit_check($ip);
    if (!$rl['allowed']) {
        return false;
    }

    try {
        $pdo = db_connect();
        $stmt = $pdo->prepare("SELECT id, email, password_hash FROM admin_users WHERE email = ? LIMIT 1");
        $stmt->execute([$identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Protección contra fijación de sesión
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }

            // Datos de sesión esperados por el panel y frontend
            $_SESSION['admin_logged'] = true;
            $_SESSION['user']         = $user['email'];
            $_SESSION['admin_id']     = (int)$user['id'];
            $_SESSION['last_activity'] = time();

            // Garantizar token CSRF para formularios
            csrf_token();

            // Limpiar contador de intentos fallidos
            auth_rate_limit_clear($ip);

            return true;
        }
    } catch (Exception $e) {
        error_log("Error en auth_attempt(): " . $e->getMessage());
    }

    // Registrar intento fallido
    auth_rate_limit_record_fail($ip);
    return false;
}

/**
 * Retorna true si existe una sesión de administrador activa.
 *
 * @return bool
 */
function auth_check(): bool {
    return !empty($_SESSION['admin_logged']);
}

/**
 * Exige sesión activa; si no existe, redirige al login y termina la ejecución.
 *
 * @param string $redirectTo
 */
function auth_require(string $redirectTo = '/admin/login.php'): void {
    if (!auth_check()) {
        header("Location: {$redirectTo}");
        exit;
    }
}

/**
 * Cierra la sesión de administración de forma completa y segura.
 */
function auth_logout(): void {
    if (session_status() === PHP_SESSION_NONE) {
        auth_session_start();
    }

    $_SESSION = [];

    if (!headers_sent() && ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Obtiene o genera el token CSRF actual de la sesión.
 *
 * @return string
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Genera el campo oculto HTML para protección CSRF.
 *
 * @return string
 */
function csrf_input(): string {
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Valida de forma segura (timing-safe) el token CSRF recibido.
 *
 * @param string|null $token Si es null se toma de $_POST['csrf_token']
 * @return bool
 */
function csrf_verify(?string $token = null): bool {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? '';
    }
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if ($token === '' || $sessionToken === '') {
        return false;
    }
    return hash_equals($sessionToken, $token);
}

if (!function_exists('h')) {
    /**
     * Helper de escape HTML contra vulnerabilidades XSS.
     *
     * @param string|null $string
     * @return string
     */
    function h(?string $string): string {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('flash_set')) {
    /**
     * Establece un mensaje flash en la sesión.
     * Soporta flash_set($message, $type) y flash_set($type, $message).
     *
     * @param string $param1 Mensaje o Tipo
     * @param string $param2 Tipo o Mensaje
     */
    function flash_set(string $param1, string $param2 = ''): void {
        $validTypes = ['success', 'error', 'info', 'warning'];

        if (in_array($param1, $validTypes, true) && $param2 !== '') {
            $type = $param1;
            $msg  = $param2;
        } elseif (in_array($param2, $validTypes, true)) {
            $type = $param2;
            $msg  = $param1;
        } else {
            $type = $param2 ?: 'success';
            $msg  = $param1;
        }

        $_SESSION['flash_type'] = $type;
        $_SESSION['flash_message'] = $msg;
    }
}

if (!function_exists('flash_get')) {
    /**
     * Obtiene y remueve el mensaje flash actual de la sesión.
     *
     * @return array|null ['type' => string, 'message' => string] o null si no hay
     */
    function flash_get(): ?array {
        if (!empty($_SESSION['flash_message'])) {
            $flash = [
                'type'    => $_SESSION['flash_type'] ?? 'info',
                'message' => $_SESSION['flash_message'],
            ];
            unset($_SESSION['flash_message'], $_SESSION['flash_type']);
            return $flash;
        }
        return null;
    }
}
