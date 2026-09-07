<?php
$cookie = file_get_contents('cookies.txt');
if (preg_match('/PHPSESSID\s+([a-zA-Z0-9]+)/', $cookie, $m)) {
    session_id($m[1]);
    session_start();
    var_dump($_SESSION['admin_data']['inicio']['hero'] ?? []);
} else {
    echo "No cookie found";
}
