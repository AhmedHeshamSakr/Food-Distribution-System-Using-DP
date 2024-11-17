<?php

// we put this file in HomepageView

// require_once __DIR__ . "/../Models/Admin.php";// Donor Model
require_once __DIR__ . '/../Controllers/EAdminController.php'; // Donor Controller
require_once 'EAdminView.php';  // Donor View

// // Start the session to access session variables
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// Instantiate the AdminView and AdminController
$view = new EventAdminView();
$controller = new EventAdminController($view);

// Handle the request and execute actions
$controller->handleRequest();

