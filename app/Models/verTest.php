<?php

require_once 'VerificationAdmin.php'; // Make sure the path is correct.

function testVerificationAdmin() {
    echo "Running VerificationAdmin Tests...\n";

    // Create a VerificationAdmin object
    $admin = new VerificationAdmin("Admin", "User", "admin@example.com", "123456789");

    // Test: View All Reports
    echo "\n--- View All Reports ---\n";
    $allReports = $admin->viewAllReports();
    if (empty($allReports)) {
        echo "No reports found.\n";
    } else {
        foreach ($allReports as $report) {
            echo "Report ID: {$report['reportID']}, Name: {$report['personINname']}, Status: {$report['status']}, Recognized: " . ($report['recognized'] ? "Yes" : "No") . "\n";
        }
    }

    // Test: View Details of a Specific Report
    $testReportID = 1; // Change this ID to one that exists in your database
    echo "\n--- View Report Details (ID: $testReportID) ---\n";
    $reportDetails = $admin->viewReportDetails($testReportID);
    if ($reportDetails) {
        print_r($reportDetails);
    } else {
        echo "Report ID $testReportID not found.\n";
    }

    // Test: Update a Report's Status
    $newStatus = 'Completed'; // Change this as needed
    echo "\n--- Update Report Status (ID: $testReportID to '$newStatus') ---\n";
    if ($admin->updateReportStatus($testReportID, $newStatus)) {
        echo "Report ID $testReportID status updated to '$newStatus'.\n";
    } else {
        echo "Failed to update Report ID $testReportID.\n";
    }

    // Test: Recognize a Report
    echo "\n--- Recognize Report (ID: $testReportID) ---\n";
    if ($admin->recognizeReport($testReportID)) {
        echo "Report ID $testReportID recognized.\n";
    } else {
        echo "Failed to recognize Report ID $testReportID.\n";
    }

    // Test: Delete a Report
    echo "\n--- Delete Report (ID: $testReportID) ---\n";
    if ($admin->deleteReport($testReportID)) {
        echo "Report ID $testReportID deleted successfully.\n";
    } else {
        echo "Failed to delete Report ID $testReportID.\n";
    }
}

// Run the tests
testVerificationAdmin();

?>
