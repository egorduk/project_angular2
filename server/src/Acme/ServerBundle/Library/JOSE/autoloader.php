<?php

define('BASE_PATH', realpath(dirname(__FILE__)));

function jws_autoloader($class) {
    $filename = BASE_PATH . str_replace('Namshi\JOSE', '', $class) . '.php';

    if (file_exists($filename)) {
        require_once($filename);
    }
}

spl_autoload_register('jws_autoloader');