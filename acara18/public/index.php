<?php

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/core/autoload.php';

$router = new Router();
$router->dispatch();
