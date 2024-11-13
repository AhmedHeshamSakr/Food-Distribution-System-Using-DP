<?php

require_once  'ReportData.php';

require_once __DIR__ . '/../../config/DB.php';

// Instantiate the ReportingData class
$reportingData = new ReportingData();

// Step 1: Create a New Report
echo "Creating a new report...\n";
$created = $reportingData->createReport(
    'Jane Doe',
    '456 Another St',
    '5559876543',
    'This is a test description for the report.'
);

if ($created) {
    echo "Report created successfully with report ID: " . $reportingData->getReportID() . "\n";
} else {
    echo "Failed to create report.\n";
    exit();
}

// Store the generated reportID for further testing
$reportID = $reportingData->getReportID();

// Step 2: Retrieve and Display Report Details
echo "\nRetrieving report details...\n";
$reportDetails = $reportingData->getReportDetails($reportID);

if ($reportDetails) {
    echo "Report details retrieved:\n";
    print_r($reportDetails);
} else {
    echo "Failed to retrieve report details.\n";
}

// Step 3: Update Report Details (Change status and recognized fields)
echo "\nUpdating report status to 'Acknowledged' and setting recognized to true...\n";
$updateData = ['status' => 'Acknowledged', 'recognized' => 1];
$updated = $reportingData->updateReport($updateData, $reportID);

if ($updated) {
    echo "Report updated successfully.\n";
    // Retrieve the updated details to verify the changes
    $updatedDetails = $reportingData->getReportDetails($reportID);
    echo "Updated report details:\n";
    print_r($updatedDetails);
} else {
    echo "Failed to update report.\n";
}

// Step 4: Soft Delete the Report
echo "\nSoft deleting the report...\n";
$deleted = $reportingData->deleteReport($reportID);

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

// Step 5: Retrieve All Active Reports
echo "\nRetrieving all active reports (non-deleted)...\n";
$activeReports = $reportingData->getAllActiveReports();
if (!empty($activeReports)) {
    echo "Active reports:\n";
    print_r($activeReports);
} else {
    echo "No active reports found.\n";
}

?>