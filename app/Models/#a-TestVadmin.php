<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
require_once 'Person.php';
require_once 'Reporter.php';
require_once 'ReportData.php';
require_once '#a-Vadmin.php';

// Create a sample VerificationAdmin instance
$admin = new VerificationAdmin(
    'Admin',
    'User',
    'admin@example.com',
    '1234567890'
);
// Test viewAllReports() method
echo "Testing viewAllReports():\n";
$reports = $admin->viewAllReports();
if (is_array($reports)) {
    echo "Success: Retrieved " . count($reports) . " reports.\n";
} else {
    echo "Error: Method did not return an array.\n";
}

// Test recognizeReport($reportID) method
echo "\nTesting recognizeReport() with valid report ID:\n";
$validReportID = 42; // Replace with an actual report ID from your test database
if ($admin->recognizeReport($validReportID)) {
    echo "Success: Report recognized successfully.\n";
} else {
    echo "Error: Failed to recognize report.\n";
}

echo "\nTesting recognizeReport() with invalid report ID:\n";
$invalidReportID = 9999; // Non-existing report ID
if (!$admin->recognizeReport($invalidReportID)) {
    echo "Success: Correctly handled invalid report ID.\n";
} else {
    echo "Error: Recognized a non-existing report.\n";
}

// Test updateReportStatus($reportID, $newStatus) method
echo "\nTesting updateReportStatus() with valid inputs:\n";
$validStatus = 'Acknowledged';
if ($admin->updateReportStatus($validReportID, $validStatus)) {
    echo "Success: Report status updated successfully.\n";
} else {
    echo "Error: Failed to update report status.\n";
}

echo "\nTesting updateReportStatus() with invalid status:\n";
$invalidStatus = 'InvalidStatus';
if (!$admin->updateReportStatus($validReportID, $invalidStatus)) {
    echo "Success: Correctly rejected invalid status.\n";
} else {
    echo "Error: Accepted an invalid status.\n";
}

echo "\nTesting updateReportStatus() with invalid report ID:\n";
if (!$admin->updateReportStatus($invalidReportID, $validStatus)) {
    echo "Success: Correctly handled invalid report ID.\n";
} else {
    echo "Error: Updated status for non-existing report.\n";
}

?>