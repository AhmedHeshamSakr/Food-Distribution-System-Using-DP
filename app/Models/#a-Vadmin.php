<?php

require_once 'Person.php';
require_once 'Event.php';
require_once 'Reporter.php';
require_once 'ReportData.php';


class VerificationAdmin extends Person {

    private int $userTypeID = Person::V_ADMIN_FLAG;

    // Constructor
    public function __construct(string $firstName, string $lastName, string $email, string $phoneNo)
    {
        $this->userTypeID = Person::V_ADMIN_FLAG;
        parent::__construct($firstName, $lastName, $email, $phoneNo, $this->userTypeID);
    }

    // Method to view all reports
    public function viewAllReports() {
        $query = "SELECT * FROM report";
        $result = run_select_query($query);
        return $result !== false ? $result : [];
    }

    // Method to recognize a report
    public function recognizeReport($reportID) {
        $reportingData = new ReportingData('', '', '', '');
        $reportDetails = $reportingData->fetchReportDetails($reportID);
    
        if ($reportDetails) {
            return $reportingData->updateReportField($reportID, 'recognized', 1);
        }
    
        return false;
    }
    // Method to update report status
    public function updateReportStatus($reportID, $newStatus) {
        $validStatuses = ['Pending', 'Acknowledged', 'In Progress', 'Completed'];
        if (!in_array($newStatus, $validStatuses)) {
            return false;
        }

        $reportingData = new ReportingData('', '', '', '');
        $reportDetails = $reportingData->fetchReportDetails($reportID);

        if ($reportDetails) {
            $query = "UPDATE report SET status = '$newStatus' WHERE reportID = '$reportID' AND is_deleted = FALSE";
            return run_query($query) ? true : false;
        }

        return false;
    }

    public function getUserTypeID(): int
    {
        return $this->userTypeID;
    }
}