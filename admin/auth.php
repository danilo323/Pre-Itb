<?php
// admin/auth.php
require_once __DIR__ . '/base_url.php';

/**
 * Carga la configuración del sistema.
 */
function auth_config(): array {
    static $config = null;
    if ($config === null) {
        $file = __DIR__ . '/config.php';
        $config = file_exists($file) ? (require $file) : [];
    }
    return $config;
}

/**
 * Inicia la sesión aplicando las directivas de seguridad:
 * - Nombre de sesión propio (no PHPSESSID).
 * - Flags de cookie: HttpOnly, SameSite=Lax, Secure (si hay HTTPS).
 */
function auth_session_start(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $config = auth_config();
    $sessionName = $config['session_name'] ?? 'itb_admin_sess';

    if (!headers_sent()) {
        if (session_name() !== $sessionName) {
            session_name($sessionName);
        }

        $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    @session_start();
}

/**
 * Obtiene la IP del cliente (con soporte de proxies si aplican).
 */
function auth_client_ip(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $candidate = trim($parts[0]);
        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            $ip = $candidate;
        }
    }
    return $ip;
}

/**
 * Archivo donde se registra el Rate Limiting.
 */
function auth_rate_limit_file(): string {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir . '/rate_limits.json';
}

/**
 * Verifica si la IP actual está bloqueada por exceso de intentos fallidos.
 * Retorna array ['locked' => bool, 'seconds_left' => int]
 */
function auth_rate_limit_check(?string $ip = null): array {
    $ip = $ip ?? auth_client_ip();
    $config = auth_config();
    $maxAttempts = (int)($config['login_max_attempts'] ?? 5);
    $lockoutSeconds = (int)($config['login_lockout_seconds'] ?? 900);

    $file = auth_rate_limit_file();
    if (!file_exists($file)) {
        return ['locked' => false, 'seconds_left' => 0];
    }

    $json = @file_get_contents($file);
    $data = json_decode($json ?: '{}', true);
    if (!is_array($data) || !isset($data[$ip])) {
        return ['locked' => false, 'seconds_left' => 0];
    }

    $entry = $data[$ip];
    $attempts = (int)($entry['attempts'] ?? 0);
    $lastAttempt = (int)($entry['last_attempt'] ?? 0);
    $elapsed = time() - $lastAttempt;

    if ($attempts >= $maxAttempts) {
        if ($elapsed < $lockoutSeconds) {
            return [
                'locked' => true,
                'seconds_left' => $lockoutSeconds - $elapsed
            ];
        } else {
            // Ya expiró el bloqueo, resetear
            unset($data[$ip]);
            @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
            return ['locked' => false, 'seconds_left' => 0];
        }
    }

    return ['locked' => false, 'seconds_left' => 0];
}

/**
 * Registra un intento fallido de login para la IP.
 */
function auth_rate_limit_fail(?string $ip = null): void {
    $ip = $ip ?? auth_client_ip();
    $file = auth_rate_limit_file();
    $data = [];
    if (file_exists($file)) {
        $json = @file_get_contents($file);
        $data = json_decode($json ?: '{}', true) ?: [];
    }

    $entry = $data[$ip] ?? ['attempts' => 0, 'last_attempt' => 0];
    $entry['attempts'] = (int)($entry['attempts'] ?? 0) + 1;
    $entry['last_attempt'] = time();
    $data[$ip] = $entry;

    @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

/**
 * Resetea el contador de intentos fallidos al tener un login exitoso.
 */
function auth_rate_limit_reset(?string $ip = null): void {
    $ip = $ip ?? auth_client_ip();
    $file = auth_rate_limit_file();
    if (!file_exists($file)) {
        return;
    }
    $json = @file_get_contents($file);
    $data = json_decode($json ?: '{}', true) ?: [];
    if (isset($data[$ip])) {
        unset($data[$ip]);
        @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
    }
}

/**
 * Control de expiración de sesión por inactividad.
 */
function auth_check_inactivity(): bool {
    auth_session_start();
    if (empty($_SESSION['admin_logged'])) {
        return true;
    }

    $config = auth_config();
    $timeout = (int)($config['session_timeout'] ?? 3600);
    $now = time();

    if (isset($_SESSION['last_activity'])) {
        $inactive = $now - (int)$_SESSION['last_activity'];
        if ($inactive > $timeout) {
            // Sesión expirada por inactividad
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            @session_destroy();
            return false;
        }
    }

    $_SESSION['last_activity'] = $now;
    return true;
}

/**
 * Exige que el usuario esté autenticado.
 * Si no lo está o la sesión expiró, redirige inmediatamente a login.php.
 *
 * El aviso de "sesión expirada" solo se añade cuando de verdad había una
 * sesión y ha caducado. Antes se añadía siempre, así que quien entraba por
 * primera vez a /admin leía un aviso de sesión caducada sin haber entrado
 * nunca al panel.
 */
function auth_require(): void {
    auth_session_start();

    // Hay que mirarlo ANTES de auth_check_inactivity(): esa función vacía la
    // sesión cuando detecta que caducó, y después ya no se distinguirían los
    // dos casos.
    $habia_sesion = !empty($_SESSION['admin_logged']);
    $sigue_viva   = auth_check_inactivity();

    if (!$sigue_viva || empty($_SESSION['admin_logged'])) {
        require_once __DIR__ . '/base_url.php';
        $caduco = ($habia_sesion && !$sigue_viva);
        header('Location: ' . admin_base() . '/login.php' . ($caduco ? '?expired=1' : ''));
        exit;
    }
}

/**
 * Autentica credenciales contra la configuración usando password_verify estrictamente.
 */
function auth_verify_credentials(string $user, string $password): bool {
    $config = auth_config();
    $adminUser = $config['admin_user'] ?? 'admin';
    $adminHash = $config['admin_hash'] ?? '';

    if ($user !== $adminUser) {
        return false;
    }

    if (empty($adminHash)) {
        return false;
    }

    return password_verify($password, $adminHash);
}
