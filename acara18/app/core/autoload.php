<?php

spl_autoload_register(function ($className) {
    $dirs = [
        __DIR__ . '/../core/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../config/'
    ];

    foreach ($dirs as $dir) {
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
