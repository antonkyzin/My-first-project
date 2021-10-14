<?php
set_include_path(get_include_path()
    . PATH_SEPARATOR . 'Controllers'
    . PATH_SEPARATOR . 'Models'
    . PATH_SEPARATOR . 'Templates');

spl_autoload_register(function ($class) {
    $path = str_replace('\\', '/', $class).'.php';
    if (file_exists($path)){
    $parts = explode('\\', $class);
        include_once(end($parts) . '.php');
}});

session_start();

$front = Controllers\FrontController::getInstance();
$front->route();
