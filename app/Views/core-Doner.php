<?php

// we put this file in HomepageView

require_once __DIR__ . "/../Models/Donor.php";// Donor Model
require_once __DIR__ . '/../Controllers/DonorController.php'; // Donor Controller
require_once 'DonorView.php';  // Donor View

// Start the session to access session variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Proceed with handling the request (e.g., render the view or handle donation)
$view = new DonorView();
$controller = new DonorController( $view);
$controller->handleRequest();

?>