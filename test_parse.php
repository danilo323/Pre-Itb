<?php
session_start();
require_once 'admin/fields/_loader.php';

$raw = [
    0 => ['archivo' => ''], // usuario no subió foto nueva, viejo valor debe preservarse
];

$config = [
    'type' => 'repeater',
    'name_path' => 'hero__imagenes_fondo',
    '_old_value' => [
        0 => ['archivo' => 'img/salud.jpg']
    ],
    'subfields' => [
        'archivo' => [
            'type' => 'image',
            'label' => 'Foto',
        ]
    ]
];

$result = field_parse('repeater', $raw, $config);
var_dump($result);
