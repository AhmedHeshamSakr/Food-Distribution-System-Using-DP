<?php

require_once __DIR__ . '/../../config/DB.php';
require_once '../models/Reporter.php';
require_once '../models/ReportingData.php';
require_once '../models/Reporting.php';

class ReporterController {
    private Reporter $reporter;

    // Constructor to initialize the controller with a reporter instance
    public function __construct(Reporter $reporter) {
        $this->reporter = $reporter;
    }

    // Handle form submission for creating a report
    public function submitReport($personInName, $personInAddress, $personInPhone, $description) {
        try {
            $this->reporter->submitReport($personInName, $personInAddress, $personInPhone, $description);
            echo "Report submitted successfully!";
        } catch (Exception $e) {
            echo "Error submitting report: " . $e->getMessage();
        }
    }

    // Fetch all active reports for the reporter
    public function getAllActiveReports() {
        try {
            return $this->reporter->getAllActiveReports();
        } catch (Exception $e) {
            echo "Error fetching active reports: " . $e->getMessage();
            return [];
        }
    }

    // Delete a report by ID
    public function deleteReport($reportID) {
        try {
            if ($this->reporter->deleteReport($reportID)) {
                echo "Report deleted successfully!";
            } else {
                echo "Failed to delete the report.";
            }
        } catch (Exception $e) {
            echo "Error deleting report: " . $e->getMessage();
        }
    }

    // Update the status of a report
    public function updateReportStatus($reportID, $newStatus) {
        try {
            if ($this->reporter->updateReportStatus($reportID, $newStatus)) {
                echo "Report status updated successfully!";
            } else {
                echo "Failed to update report status.";
            }
        } catch (Exception $e) {
            echo "Error updating report status: " . $e->getMessage();
        }
    }
}

