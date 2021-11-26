<?php
set_include_path(get_include_path()
    . PATH_SEPARATOR . 'Controllers'
    . PATH_SEPARATOR . 'Models'
    . PATH_SEPARATOR . 'Templates'
    . PATH_SEPARATOR . 'View');

spl_autoload_register(function ($class) {
    $path = str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        include_once($path);
    }
});

session_start();
$front = new Controllers\FrontController;
$front->route();
