<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require './vendor/autoload.php';

use App\Controller\TaskController;

// Test route
Flight::route('GET /', function () {
    echo 'My FlightPHP backend is running!';
});


$taskController = new TaskController();

// Task API routes
Flight::route('GET /tasks', [$taskController, 'index']);
Flight::route('GET /tasks/status/@status', [$taskController, 'filterByStatus']);
Flight::route('GET /tasks/@id:[0-9]+', [$taskController, 'show']);

Flight::route('POST /tasks', [$taskController, 'store']);
Flight::route('POST /tasks/@id:[0-9]+', function($id) use ($taskController) {
    $taskController->update((int) $id);
});
Flight::route('DELETE /tasks/@id:[0-9]+', function($id) use ($taskController) {
    $taskController->destroy((int) $id);
});


Flight::start();
