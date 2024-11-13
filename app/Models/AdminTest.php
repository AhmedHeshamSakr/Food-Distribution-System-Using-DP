<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
require_once __DIR__ . "/../../config/DB.php";
require_once 'Person.php';
require_once 'Badges.php';
require_once 'Reporter.php';
require_once 'VerificationAdmin.php';

// Create a mock admin user (assuming iLogin class is already defined)
$login = new iLogin("admin", "password");  // Adjust login credentials as necessary
$verificationAdmin = new VerificationAdmin(1, 'Admin', 'User', 'admin@example.com', '1234567890', $login);

// Simulate testing report review, approval, and rejection

// Testing reviewing a report
echo "Testing Review Report:<br>";
$reportID = 1;  // Assume report with ID 1 exists
$reviewSuccess = $verificationAdmin->reviewReport($reportID);
echo $reviewSuccess ? "Report reviewed successfully.<br>" : "Failed to review the report.<br>";

// Testing approving a report
echo "<br>Testing Approve Report:<br>";
$approveSuccess = $verificationAdmin->approveReport($reportID);
echo $approveSuccess ? "Report approved successfully.<br>" : "Failed to approve the report.<br>";

// Testing rejecting a report
echo "<br>Testing Reject Report:<br>";
$rejectSuccess = $verificationAdmin->rejectReport($reportID);
echo $rejectSuccess ? "Report rejected successfully.<br>" : "Failed to reject the report.<br>";

// Assuming you have a mock of ReportingData class to simulate report fetch and status update (mock the database queries)
?>

