<?php

/**
*   Обертка для функции var_dump
*
*   Принимает переменную, которую нужно распечатать и метку - чтобы обозначить какой именно var_dump сработал
*    
*   @param mixed $var Переменная для распечатки
*
*   @param string $metka Метка
* 
*/

function vardump($var,$metka) {
    if ($metka) echo "$metka: <br\>\n";
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

/**
$classesDir = __DIR__ . "/classes/";
$extension = ".php";
spl_autoload_register(
    function($className) use ($classesDir, $extension) {	
        $normalizedClass = str_replace('..', '', $className);	
        $fileName = $normalizedClass;	
        //$fileName = str_replace('_', '/', $fileName);	
        $fileName = $classesDir . $fileName . $extension;	
        if (!file_exists($fileName)) {	
            die("<b>Autoload fatal error:</b> Cannot find class $fileName file!");
        }	
        require_once $fileName;	
        if (!class_exists($normalizedClass)) {	
            die('<b>Autoload fatal error:</b> Class file included, but class not found.');	
        }
});
 */

include_once 'classes/include_classes.php';
include_once 'functions/core.php';
include_once 'functions/init.php';

