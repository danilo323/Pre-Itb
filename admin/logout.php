<?php
// admin/logout.php
session_start();
require_once __DIR__ . '/base_url.php';
$_SESSION = [];
session_destroy();
header('Location: ' . admin_base() . '/login.php');
exit;
