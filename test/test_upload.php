<?php
session_start();
$_SERVER['REQUEST_METHOD'] = 'POST';

// Mock post data
$_POST = [
    'section' => 'inicio',
    'hero___visible' => '1',
    'hero__titulo' => 'Nuevo titulo',
    'hero__imagenes_fondo' => [
        0 => [
            'archivo' => 'img/salud.jpg'
        ],
        1 => [
            'archivo' => ''
        ]
    ]
];

// Mock a real file upload
file_put_contents('fake_image.jpg', 'fake image data');

$_FILES = [
    'hero__imagenes_fondo' => [
        'name' => [ 1 => [ 'archivo' => [ 'file' => 'fake_image.jpg' ] ] ],
        'type' => [ 1 => [ 'archivo' => [ 'file' => 'image/jpeg' ] ] ],
        'tmp_name' => [ 1 => [ 'archivo' => [ 'file' => __DIR__ . '/fake_image.jpg' ] ] ],
        'error' => [ 1 => [ 'archivo' => [ 'file' => UPLOAD_ERR_OK ] ] ],
        'size' => [ 1 => [ 'archivo' => [ 'file' => 15 ] ] ],
    ]
];

// Override move_uploaded_file for the test using runkit or just copy
// Actually we can't easily override move_uploaded_file in pure PHP without runkit, but move_uploaded_file only works for REAL uploads via HTTP.
