<?php
// Include required files (adjust paths accordingly)
require_once __DIR__ . "/../Models/Reporter.php";
require_once __DIR__ . "/../Views/ReporterView.php";
require_once __DIR__ . "/../Controllers/ReporterController.php";

// Mock session for testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//$_SESSION['email'] = 'johnny.doe@example.com'; // Simulate a logged-in user

// Instantiate the view and controller
$view = new ReporterView();
$controller = new ReporterController($view);

// Handle the request (for testing purposes, simulate form submission)

$controller->handleRequest();
// Render the page
//$controller->renderPage();
?>
