<?php
if (file_exists(__DIR__ . '/../vendor/autoload.php'))
    require __DIR__ . '/../vendor/autoload.php';        //this is for dev, when we are using other dependencies
// else
//     require __DIR__ . '/../../../autoload.php';         //this is for prod, when we are the dependency

// spl_autoload_register( function($class_name) {
//     if (file_exists('src/'.$class_name.'.php'))
//         include_once 'src/'.$class_name.'.php';
//     else
//         include_once 'tests/'.$class_name.'.php';
// });
date_default_timezone_set('UTC');