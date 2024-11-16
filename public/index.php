<?php
// index.php - Routes requests to controllers

// Include the necessary controller class
require_once __DIR__ . '/Controllers/ReportingController.php';

// Parse the request URL to determine the action
$controller = new ReportingController();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $reportID = isset($_GET['id']) ? $_GET['id'] : null;

    switch ($action) {
        case 'view':
            // Display the report details
            $controller->viewReport($reportID);
            break;

        case 'update':
            // Update the report status (POST request)
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $newStatus = $_POST['status'];
                $controller->updateReportStatus($reportID, $newStatus);
            }
            break;

        case 'delete':
            // Delete the report
            $controller->deleteReport($reportID);
            break;

        default:
            echo "Invalid action.";
            break;
    }
} else {
    // Default action (e.g., list reports)
    echo "Welcome to the reporting system!";
}
?>
