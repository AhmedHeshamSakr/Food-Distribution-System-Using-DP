<?php
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
if ($emailLogin->login(["email" => "testuser@example.com", "password" => "securepassword"])) {
    echo "Login successful.\n";

    // Initialize Reporter with login method
    $reporter = new Reporter(1, "Test", "User", $email, "123456789", $emailLogin);

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
    $reportID = 42; // Assuming the report ID we want to update
    echo "Updating report status to 'Acknowledged'...\n";
    $updated = $reporter->updateReportStatus($reportID, "Acknowledged");
    if ($updated) {
        echo "Report status updated successfully.\n";
    } else {
        echo "Failed to update report status.\n";
    }

    // Test recognizing the report
    echo "Recognizing report...\n";
    $recognized = $reporter->recognizeReport($reportID);
    if ($recognized) {
        echo "Report recognized successfully.\n";
    } else {
        echo "Failed to recognize report.\n";
    }

    // Test soft deleting the report
    echo "Soft deleting the report...\n";
    $deleteResult = $reporter->deleteReport($reportID);
    if ($deleteResult) {
        echo "Report marked as deleted successfully.\n";
    } else {
        echo "Failed to delete report.\n";
    }

    // Retrieve active reports after deletion to verify
    echo "Retrieving active reports for User " . $reporter->getUserID() . " after deletion...\n";
    $activeReports = $reporter->getAllActiveReports();
    echo "<hr/>";
    echo "Active reports after deletion:\n";
    print_r($activeReports);
} else {
    echo "Login failed. Cannot proceed with tests.\n";
}
?>
