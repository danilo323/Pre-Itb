<?php
// admin/index.php
// Este archivo ya no es el dashboard principal.
// Después de hacer login, el admin ve la landing page con el panel flotante.
session_start();

if (empty($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}

// Si ya está logueado, lo mandamos a la landing page con el panel flotante
header('Location: /');
exit;
