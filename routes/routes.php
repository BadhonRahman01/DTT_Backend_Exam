<?php

/** @var Bramus\Router\Router $router */

// Define routes here
// $router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/', App\Controllers\IndexController::class . '@test');




$router->get('/facilities',  App\Controllers\FacilityController::class . '@readAll');
$router->get('/facilities/{id}', App\Controllers\FacilityController::class . '@readOne');
$router->post('/facilities', App\Controllers\FacilityController::class . '@create');
$router->put('/facilities/{id}', App\Controllers\FacilityController::class . '@update');
$router->delete('/facilities/{id}', App\Controllers\FacilityController::class . '@delete');
$router->get('/facilitysearch/{search}', App\Controllers\FacilityController::class . '@facilitysearch');
