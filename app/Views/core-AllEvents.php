<?php



// Start the session to access session variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Models/Event.php';
require_once __DIR__ . '/../Models/Address.php';
require_once __DIR__ . '/../Views/EventView.php';
require_once __DIR__ . '/../Controllers/EventController.php';

$controller = new EventController();
$controller->handleRequest();