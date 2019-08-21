<?php
// This file can be used if you do not use composer to get all the dependencies. 
//Then you need to download all the dependencies manually and change the first require line accordingly.
require __DIR__ . '/../vendor/autoload.php';
spl_autoload_register( function($class_name) {
    if (file_exists('src/'.$class_name.'.php'))
        include_once 'src/'.$class_name.'.php';
    else
        include_once 'tests/'.$class_name.'.php';
});
date_default_timezone_set('UTC');