
<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoloader
require_once __DIR__ . '/../config/config.php'; // Configuration file
require_once __DIR__ . '/../config/database.php'; // Database configuration
require_once __DIR__ . '/../controllers/FacilityController.php';
// $pdo = new PDO("mysql:host=localhost;dbname=dtt_exam", "root", "");


// // Instantiate the FacilityController with the database connection
// $facilityController = new \App\Controllers\FacilityController($db);
// You are creating an instance of FacilityController without passing the expected argument


use Bramus\Router\Router;

$router = new Router();

// Define a route
$router->get('/', function () {
    echo 'Hello World!';
});

$router->get('/facilities', function () {
$host = "localhost";
$dbname = "dtt_exam";
$username = "root";
$password = "";

$controller = new \App\Controllers\FacilityController($host, $dbname, $username, $password);

// $data now contains the fetched data from the database
$controller->readAll();
});

$router->get('/facilities/{id}', function ($id) {
$host = "localhost";
$dbname = "dtt_exam";
$username = "root";
$password = "";

$controller = new \App\Controllers\FacilityController($host, $dbname, $username, $password);
$controller->readOne($id);
});

$router->get('/facilitysearch/{search}', function ($search) {
    $host = "localhost";
    $dbname = "dtt_exam";
    $username = "root";
    $password = "";

    $controller = new \App\Controllers\FacilityController($host, $dbname, $username, $password);
    $controller->facilitysearch($search);
});

$router->post('/facilities', function () {
$host = "localhost";
$dbname = "dtt_exam";
$username = "root";
$password = "";
$controller = new \App\Controllers\FacilityController($host, $dbname, $username, $password);
$controller->create();
});

$router->delete('/facilities/{id}', function ($id) {
    $host = "localhost";
    $dbname = "dtt_exam";
    $username = "root";
    $password = "";
    $controller = new \App\Controllers\FacilityController($host, $dbname, $username, $password);
    $controller->delete($id);
    });

$router->put('/facilities/{id}', function ($id) {
$host = "localhost";
$dbname = "dtt_exam";
$username = "root";
$password = "";
$controller = new \App\Controllers\FacilityController($host, $dbname, $username, $password);
$controller->update($id);
});


// Run the router
$router->run();