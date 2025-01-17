
<?php
require_once __DIR__ . "/../Views/VolunteerView.php";
require_once __DIR__ . "/../Controllers/VolunteerController.php";

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
