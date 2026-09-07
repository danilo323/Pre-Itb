<?php
function normalize_files_array($files) {
    $out = [];
    foreach ($files as $top_key => $f) {
        if (!is_array($f['name'])) {
            $out[$top_key] = $f;
            continue;
        }
        $keys = ['name', 'type', 'tmp_name', 'error', 'size'];
        $walker = function($data, $path) use (&$walker, &$out, $keys, $top_key, $f) {
            foreach ($data as $k => $v) {
                $cur = $path . '[' . $k . ']';
                if (is_array($v)) {
                    $walker($v, $cur);
                } else {
                    $item = [];
                    foreach ($keys as $prop) {
                        $val = $f[$prop];
                        preg_match_all('/\[(.*?)\]/', $cur, $m);
                        foreach ($m[1] as $pk) { $val = $val[$pk]; }
                        $item[$prop] = $val;
                    }
                    $out[$top_key . $cur] = $item;
                }
            }
        };
        $walker($f['name'], '');
    }
    return $out;
}

$mock_files = [
    'hero__imagenes_fondo' => [
        'name' => [ 0 => [ 'archivo' => [ 'file' => 'test.jpg' ] ] ],
        'type' => [ 0 => [ 'archivo' => [ 'file' => 'image/jpeg' ] ] ],
        'tmp_name' => [ 0 => [ 'archivo' => [ 'file' => '/tmp/php1234' ] ] ],
        'error' => [ 0 => [ 'archivo' => [ 'file' => 0 ] ] ],
        'size' => [ 0 => [ 'archivo' => [ 'file' => 1234 ] ] ],
    ]
];

$normalized = normalize_files_array($mock_files);
var_export($normalized);
