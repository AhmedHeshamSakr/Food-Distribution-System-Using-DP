<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../config/DB.php';
require_once 'User.php';
require_once 'ReportData.php';
require_once 'Login.php';
require_once 'Reporter.php';

// Ensure that the email is unique for testing
$email = "testuser" . time() . "@example.com";  // Unique email for each run

// Initialize login method
$emailLogin = new withEmail("testuser@example.com", "securepassword");

// Ensure the login is successful before proceeding


// Initialize Reporter with login method
$reporter = new Reporter(1, "Test", "User", $email, "123456789");

// Test submitting a new report
echo "Submitting a new report...\n";
$result = $reporter->submitReport("Jane Doe", "456 Another St", "5559876543", "Description for a test report");
if ($result) {
    echo "Report submitted successfully.\n";
} else {
    echo "Failed to submit report.\n";
}

// Test retrieving reports by user ID
echo "Retrieving reports by user ID...\n";
$reports = $reporter->getReportsByUserID($reporter->getUserID());
echo "<hr/>";
echo "Reports for User " . $reporter->getUserID() . ":\n";
print_r($reports);

    // Test updating report status
    // $reportID = $reports[0]['reportID']; // Using the first report ID retrieved
    // echo "Updating report status to 'Acknowledged'...\n";
    // $updated = $reporter->updateReportStatus($reportID, "Acknowledged");
    // if ($updated) {
    //     echo "Report status updated successfully.\n";
    // } else {
    //     echo "Failed to update report status.\n";
    // }

    // // Test recognizing the report
    // echo "Recognizing report...\n";
    // $recognized = $reporter->recognizeReport($reportID);
    // if ($recognized) {
    //     echo "Report recognized successfully.\n";
    // } else {
    //     echo "Failed to recognize report.\n";
    // }

    // // Test soft deleting the report
    // echo "Soft deleting the report...\n";
    // $deleteResult = $reporter->deleteReport($reportID);
    // if ($deleteResult) {
    //     echo "Report marked as deleted successfully.\n";
    // } else {
    //     echo "Failed to delete report.\n";
    // }

    // // Retrieve active reports after deletion to verify
    // echo "Retrieving active reports for User " . $reporter->getUserID() . " after deletion...\n";
    // $activeReports = $reporter->getAllActiveReports();
    // echo "<hr/>";
    // echo "Active reports after deletion:\n";
    // print_r($activeReports);

    // // Testing failed report recognition when report doesn't exist
    // echo "Attempting to recognize a non-existent report...\n";
    // $nonExistentReportID = 9999; // Assuming this report ID doesn't exist
    // $failedRecognition = $reporter->recognizeReport($nonExistentReportID);
    // if (!$failedRecognition) {
    //     echo "Correctly failed to recognize non-existent report.\n";
    // }

    // // Testing failed report deletion when report doesn't exist
    // echo "Attempting to delete a non-existent report...\n";
    // $failedDelete = $reporter->deleteReport($nonExistentReportID);
    // if (!$failedDelete) {
    //     echo "Correctly failed to delete non-existent report.\n";
    // }


?>