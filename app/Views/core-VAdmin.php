<?php
// Define the base path for the app to ensure proper includes
require_once __DIR__ . "/../Models/VerificationAdmin.php";
require_once __DIR__ . "/../Views/vAdminView.php";
require_once __DIR__ . "/../Controllers/vAdminController.php";


// Instantiate the Verification Admin Controller
$controller = new VerificationAdminController();

// Handle the request using the controller
$controller->handleRequest();

?>