<?php

// Include your class files and database connection
require_once __DIR__ . '/../../config/DB.php';
require_once 'ReportData.php';

// Instantiate the ReportingData class
$reportingData = new ReportingData();

// Step 2: Create a New Report
echo "Creating a new report...\n";
$created = $reportingData->createReport(
    'John Doe',
    '123 Main St',
    '5551234567',
    'This is a description of the report.'
);

if ($created) {
    echo "Report created successfully with report ID: " . $reportingData->getReportID() . "\n";
} else {
    echo "Failed to create report.\n";
    exit();
}

// Store the generated reportID
$reportID = $reportingData->getReportID();

// Step 3: Retrieve and Display Report Details
echo "Retrieving report details...\n";
$reportDetails = $reportingData->getReportDetails($reportID);

if ($reportDetails) {
    echo "Report details:\n";
    print_r($reportDetails);
} else {
    echo "Failed to retrieve report details.\n";
}

// Step 4: Update Report Details
echo "Updating report status to 'Acknowledged'...\n";
$updateData = ['status' => 'Acknowledged'];
$updated = $reportingData->updateReport($updateData);

if ($updated) {
    echo "Report status updated successfully.\n";
    // Retrieve the updated details to verify
    $updatedDetails = $reportingData->getReportDetails($reportID);
    echo "Updated report details:\n";
    print_r($updatedDetails);
} else {
    echo "Failed to update report.\n";
}

// Step 5: Soft Delete the Report
echo "Soft deleting the report...\n";
$deleted = $reportingData->deleteReport();

if ($deleted) {
    echo "Report marked as deleted successfully.\n";
    // Attempt to retrieve the deleted report (should not return results)
    $deletedReport = $reportingData->getReportDetails($reportID);
    if ($deletedReport) {
        echo "Report still retrievable (something went wrong with delete):\n";
        print_r($deletedReport);
    } else {
        echo "Report is deleted and cannot be retrieved.\n";
    }
} else {
    echo "Failed to delete report.\n";
}

?>
