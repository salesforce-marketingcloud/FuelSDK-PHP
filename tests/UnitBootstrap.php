<?php
spl_autoload_register( function($class_name) {
    if (file_exists('src/'.$class_name.'.php'))
        include_once 'src/'.$class_name.'.php';
    else
        include_once 'tests/'.$class_name.'.php';
});
date_default_timezone_set('UTC');