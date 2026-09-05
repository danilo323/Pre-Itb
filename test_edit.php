<?php
session_start();
$_SESSION['admin_data'] = [
    'equipo' => [
        'items' => [
            ['id' => 1, 'nombre' => 'Test', 'orden' => '2', 'foto' => 'img/test.jpg']
        ]
    ]
];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'nombre' => 'Test 2'
];
$_GET = ['c' => 'equipo', 'id' => '1'];

ob_start();
include 'admin/editar.php';
$output = ob_get_clean();

var_dump($_SESSION['admin_data']['equipo']['items']);
