<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

use App\Controllers\FileController;
use App\Controllers\HomeController;
use App\Router;
use App\View;


$router = new Router();

$router
    ->get('/public/index.php', [HomeController::class, 'index'])
    ->get('/public/index.php/download', [HomeController::class, 'download'])
    ->post('/public/index.php/upload', [HomeController::class, 'upload']);

(new App\App(
    $router,
    ['uri' => $_SERVER['REQUEST_URI'], 'method' => $_SERVER['REQUEST_METHOD']],
))->run();
