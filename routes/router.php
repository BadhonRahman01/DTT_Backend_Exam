<?php

// Set the base path
$basePath = '/web_backend_test_catering_api';
require_once __DIR__ . '/../controllers/FacilityController.php';


// Include the necessary classes and configuration
require_once __DIR__ . '/../config/config.php';

// Database connection
try {
    $pdo = new PDO(
        "mysql:host={$databaseConfig['host']};dbname={$databaseConfig['dbname']}",
        $databaseConfig['username'],
        $databaseConfig['password']
    );
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Include the main index.php file
require_once __DIR__ . '/../public/index.php';


$router->get('/', function () {
    echo 'Hello World!';
});
$router->post($basePath . '/facilities', 'FacilityController@create');
$router->get($basePath . '/facilities/{id}', 'FacilityController@readOne');
$router->get($basePath . '/facilities', 'FacilityController@readAll');
$router->put($basePath . '/facilities/{id}', 'FacilityController@update');
$router->delete($basePath . '/facilities/{id}', 'FacilityController@delete');
$router->get($basePath . '/facilitysearch/{search}', 'FacilityController@facilitysearch');
