<?php


require_once __DIR__ . '/../Controllers/EventController.php';
require_once 'EventView.php';

// Start the session to access session variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$view = new EventView();
$controller = new EventController($view);
$controller->handleFetchUpcomingEvents();