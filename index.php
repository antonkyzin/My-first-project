<?php
/* Пути по-умолчанию для поиска файлов */
set_include_path(get_include_path()
    . PATH_SEPARATOR . 'Controllers'
    . PATH_SEPARATOR . 'Models'
    . PATH_SEPARATOR . 'Views');

$massage = "";
/* Автозагрузчик классов */
spl_autoload_register(function ($class) {
    $path = str_replace("\\", "/", $class).".php";
    if (file_exists($path)){
    $parts = explode('\\', $class);
        include_once(end($parts) . '.php');
}});

session_start();

/* Инициализация и запуск FrontController */
$front = Controllers\FrontController::getInstance();
$front->route();



/* Вывод данных */
echo $front->getBody();