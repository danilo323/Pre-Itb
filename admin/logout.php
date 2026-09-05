<?php
// admin/logout.php
// Cierre seguro de sesión del panel ITB

require_once __DIR__ . '/auth.php';

auth_logout();
header('Location: login.php?logout=1');
exit;
