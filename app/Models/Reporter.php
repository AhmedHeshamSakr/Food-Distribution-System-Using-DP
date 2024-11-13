<?php 

// require_once __DIR__ . '/../../config/DB.php';
// use __DIR__ . when they are not in the same file only pleas()
require_once __DIR__ . '/../../config/DB.php';
require_once 'User.php';
require_once 'ReportData.php'; 
require_once 'Login.php'; 

class Reporter extends User {
    private Reporting $reporting;
    private ReportingData $reportingData;
    // Constructor
    public function __construct($userTypeID, $firstName, $lastName, $email, $phoneNo, iLogin $login)
    {
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
          // Initialize reporting (pass userID and null for reportID initially)
    }

    // Method for submitting a new report
    public function submitReport($personInName, $personInAddress, $personInPhone, $description)
    {
        // Create the report using ReportingData (this keeps creating report data separate)
        $this->reportingData = new ReportingData($personInName, $personInAddress, $personInPhone, $description);

        // Add reporting relationship (assumes getReportID is set correctly in ReportingData)
        $reportID = $this->reportingData->getReportID();
        $this->reporting = new Reporting($this->getUserID(), $reportID);
        $this->reporting->addReporting($this->getUserID(), $reportID);  // Create the relationship in the reporting table

        return true; // Or any other necessary return value
    }

    // Method to update the status of an existing report, which will be used by ADMIN
    public function updateReportStatus($reportID, $newStatus) {
        //$reportData = new ReportingData(null, null, null, null); // Create an instance of ReportingData
        if ($this->reportingData->getReportDetails($reportID)) {  // Make sure the report exists before updating
            $updated = $this->reportingData->updateReport(['status' => $newStatus], $reportID);
            if ($updated) {
                // Update the timestamp in the Reporting table
                //$reporting->Reporting($this->getUserID(), $reportID);
                return $this->reporting->updateTimestamp();
            }
        } else {
            echo "Report not found.\n";
        }
        return false;
    }

    // Method to recognize a report, marking it as reviewed or acknowledged, to be used by ADMIN 
    public function recognizeReport($reportID) {
        // Fetch the report details
        //$reportData = new ReportingData(null, null, null, null);
        $reportDetails = $this->reportingData->getReportDetails($reportID);

        // Check if the report exists before updating
        if ($reportDetails) {
            // Update the recognized field to true
            return $this->reportingData->updateReport(['recognized' => 1], $reportID); // Set recognized to 1 (true)
        }

        return false;  // Return false if the report was not found
    }

    // Method to delete a report
    public function deleteReport($reportID) {
        // Retrieve the report details
        $reportDetails = $this->reportingData->getReportDetails($reportID);
    
        // Check if the report exists and is not already deleted
        if ($reportDetails && isset($reportDetails['is_deleted']) && $reportDetails['is_deleted'] == 0) {
            return $this->reportingData->deleteReport($reportID);
        }
    
        echo "Report not found or already deleted.\n";
        return false;
    }
    
    // Method to get all active reports
    public function getAllActiveReports() {
        //$reportData = new ReportingData(null, null, null, null); // Create an instance of ReportingData
        return $this->reportingData->getAllActiveReports();
    }

    // Method to get reports by a specific user's ID
    public function getReportsByUserID($reporterID) {
        // Use Reporting class to get reports by user ID
        //$reporting = new Reporting($this->getUserID(), null);  // Initialize Reporting with userID
        return $this->reporting->getReportsByUserID($reporterID);
    }
}

class Reporting {
    private $reportID;
    private $reporterID;
    private $created_at;
    private $updated_at;
    private $is_deleted;

    private $db;

    // Constructor
    public function __construct($reporterID = null, $reportID = null) {
        $this->db = Database::getInstance()->getConnection();
        $this->reporterID = $reporterID;
        $this->reportID = $reportID;
    }

    // Create a new reporting relationship between a user and a report
    public function addReporting($reporterID, $reportID) {
        $query = "INSERT INTO reporting (userID, reportID, created_at, updated_at, is_deleted)
                  VALUES ('$reporterID', '$reportID', NOW(), NOW(), 0)";
        
        return run_query($query);
    }

    // Retrieve details of a specific reporting record
    public function getReportingDetails($reporterID, $reportID) {
        $query = "SELECT * FROM reporting WHERE userID = '$reporterID' AND reportID = '$reportID' AND is_deleted = FALSE";
        $result = run_select_query($query);
        
        if ($result) {
            $this->reporterID = $result[0]['userID'];
            $this->reportID = $result[0]['reportID'];
            $this->created_at = $result[0]['created_at'];
            $this->updated_at = $result[0]['updated_at'];
            return $result[0];
        }
        return null;
    }

    // Fetch all the reports submitted by a certain user using their ID
    public function getReportsByUserID($reporterID) {
        $query = "SELECT reportID FROM reporting WHERE userID = '$reporterID' AND is_deleted = FALSE";
        $result = run_select_query($query);

        if (!$result) {
            return []; // Return an empty array if no reports are found
        }

        $reportIDList = implode(',', array_column($result, 'reportID'));
        $query = "SELECT * FROM report WHERE reportID IN ($reportIDList) AND is_deleted = FALSE";
        return run_select_query($query); // Returns all active reports for the given user
    }

    // Update the 'updated_at' timestamp for a reporting record
    public function updateTimestamp() {
        $query = "UPDATE reporting SET updated_at = NOW() WHERE userID = '{$this->reporterID}' AND reportID = '{$this->reportID}'";
        return run_query($query);
    }

    // Method to delete a reporting relationship (optional)
    public function removeReporting() {
        $query = "UPDATE reporting SET is_deleted = TRUE WHERE userID = '{$this->reporterID}' AND reportID = '{$this->reportID}'";
        return run_query($query);
    }

    // Getters for attributes
    public function getReporterID() {
        return $this->reporterID;
    }

    public function getReportID() {
        return $this->reportID;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }
}
