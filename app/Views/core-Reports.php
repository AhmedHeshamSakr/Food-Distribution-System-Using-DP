<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// First, make sure all required files are included
require_once __DIR__ . "/../Models/a-Vadmin.php";
require_once __DIR__ . "/../Views/vAdminView.php";
require_once __DIR__ . '/../Controllers/vAdminController.php';
require_once __DIR__ . '/../Models/Exporter.php';
require_once __DIR__ . '/../Views/HTMLExporter.php';  // Make sure this path is correct

try {
    // Create the HtmlExporter instance first
    $exporter = new HtmlExporter();
    
    // Then create the view with the exporter
    $view = new VAdminView($exporter);
    
    // Finally, create the controller with the view
    $controller = new VerificationAdminController($view);
    
    // Handle the request
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'recognize':
                if (isset($_POST['id'])) {
                    $controller->recognizeReport($_POST['id']);
                }
                break;
        }
    } else {
        $controller->showAdminPanel();
    }
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error in core-Reports.php: " . $e->getMessage());
    
    // Show a user-friendly error message
    echo "An error occurred while loading the page. Please contact the system administrator.";
    
    // For development, you might want to see the full error:
    if (true) {  // Change to false in production
        echo "<pre>";
        echo "Error details: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "</pre>";
    }
}
