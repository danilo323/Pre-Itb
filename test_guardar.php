<?php
session_start();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'section' => 'inicio',
    'hero___visible' => '1',
    'hero__titulo' => 'Nuevo Titulo',
    'hero__imagenes_fondo' => [
        0 => [ 'archivo' => '' ]
    ]
];
$_FILES = [
    'hero__imagenes_fondo' => [
        'name' => [ 0 => [ 'archivo' => [ 'file' => '' ] ] ],
        'type' => [ 0 => [ 'archivo' => [ 'file' => '' ] ] ],
        'tmp_name' => [ 0 => [ 'archivo' => [ 'file' => '' ] ] ],
        'error' => [ 0 => [ 'archivo' => [ 'file' => 4 ] ] ],
        'size' => [ 0 => [ 'archivo' => [ 'file' => 0 ] ] ],
    ]
];
require 'admin/guardar.php';
