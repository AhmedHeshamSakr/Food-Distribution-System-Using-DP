<?php

require_once __DIR__ . '/ReportData.php';

// Initialize the ReportingData object with sample data
echo "Creating a new report...\n";
$reportData = new ReportingData('John Doe', '123 Main St', '5551234567', 'This is a test report');

// Retrieve the report ID
$reportID = $reportData->getReportID();
echo "Report created successfully with ID: " . $reportID . "\n";

// Retrieve the report ID
$reportID = $reportData->getReportID();
echo "Report created successfully with ID: " . $reportID . "\n";

// Fetch report details
echo "Fetching report details...\n";
$details = $reportData->getReportDetails($reportID);
if ($details) {
    echo "Report Details:\n";
    print_r($details);
} else {
    echo "Failed to retrieve report details.\n";
}

// Update report status
echo "Updating report status...\n";
$updateFields = ['status' => 'Reviewed'];
$updateResult = $reportData->updateReport($updateFields, $reportID);
if ($updateResult) {
    echo "Report updated successfully.\n";
    // Fetch the updated details to confirm
    $updatedDetails = $reportData->getReportDetails($reportID);
    echo "Updated Report Details:\n";
    print_r($updatedDetails);
} else {
    echo "Failed to update report.\n";
}

// Soft delete the report
echo "Deleting the report...\n";
$deleteResult = $reportData->deleteReport($reportID);
if ($deleteResult) {
    echo "Report deleted successfully.\n";
} else {
    echo "Failed to delete report.\n";
}

// Try to fetch the deleted report details
echo "Fetching report details after deletion...\n";
$deletedDetails = $reportData->getReportDetails($reportID);
if ($deletedDetails) {
    echo "Report still exists (should be marked as deleted):\n";
    print_r($deletedDetails);
} else {
    echo "Report is marked as deleted and not accessible.\n";
}

// // Fetch all active reports to confirm only active reports are retrieved
// echo "Fetching all active reports...\n";
// $activeReports = $reportData->getAllActiveReports();
// if (!empty($activeReports)) {
//     echo "Active Reports:\n";
//     print_r($activeReports);
// } else {
//     echo "No active reports found.\n";
// }

