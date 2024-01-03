<?php
spl_autoload_register(function ($class) {
    $realpath = "vendor/";
    $realpathArr = explode("\\", $class);
    if ($realpathArr[0] === 'Main') {
        $realpath .= implode(DIRECTORY_SEPARATOR, array_slice($realpathArr, 1)).".php";
    } else if ($realpathArr[0] === 'Kernel') {
        $realpath .= implode(DIRECTORY_SEPARATOR, $realpathArr).".php";
    } else {
        $realpath = "src/".implode(DIRECTORY_SEPARATOR, $realpathArr).".php";
    }
    require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$realpath);
});