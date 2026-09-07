<?php
// admin/csrf.php
// Helpers de protección CSRF para el panel de administración

function csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $sent = $_POST['csrf_token'] ?? '';
    $real = $_SESSION['csrf_token'] ?? '';
    if ($real === '' || !hash_equals($real, $sent)) {
        http_response_code(403);
        exit('Solicitud inválida (token de seguridad no coincide). Recarga la página e intenta de nuevo.');
    }
}
