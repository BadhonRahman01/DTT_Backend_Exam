
<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoloader
require_once __DIR__ . '/../config/config.php'; // Configuration file
require_once __DIR__ . '/../controllers/FacilityController.php';
$pdo = new PDO("mysql:host=localhost;dbname=dtt_exam", "root", "");

// You are creating an instance of FacilityController without passing the expected argument
$facilityController = new \App\Controllers\FacilityController();
$facilityController->readAll();

use Bramus\Router\Router;

$router = new Router();

// Define a route
$router->get('/', function () {
    echo 'Hello World!';
});


// Run the router
$router->run();