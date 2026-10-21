<?php
$allowedOrigins = [
    'http://127.0.0.1:5500',
    'http://localhost:5500',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Vary: Origin');
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

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
