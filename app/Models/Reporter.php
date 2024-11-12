<?php 

// require_once __DIR__ . '/../../config/DB.php';
// use __DIR__ . when they are not in the same file only pleas()
require_once __DIR__ . '/../../config/DB.php';
require_once 'User.php';
require_once 'ReportData.php'; 
require_once 'Login.php'; 

class Reporter extends User {
    //private $reporting;

    private $reporting;

    public function __construct($userTypeID, $firstName, $lastName, $email, $phoneNo, iLogin $login)
    {
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
        $this->reporting = new Reporting($this->getUserID(), null);  // Initialize reporting
    }

    // Method for submitting a new report
    public function submitReport($personInName, $personInAddress, $personInPhone, $description)
    {
        // Create the report
        $reportingData = new ReportingData();
        $reportingData->createReport($personInName, $personInAddress, $personInPhone, $description);

        // Add reporting relationship (assumes getReportID is set correctly)
        $reportID = $reportingData->getReportID();
        $this->reporting->addReporting($this->getUserID(), $reportID);

        return true; // Or any other necessary return value
    }

    // Method to update the status of an existing report which will be used BY ADMIN
    public function updateReportStatus($reportID, $newStatus) {
        $reportData = new ReportingData();
        if ($reportData->getReportDetails($reportID)) {  // Make sure the report exists before updating
            $updated = $reportData->updateReport(['status' => $newStatus], $reportID);
            if ($updated) {
                // Update the timestamp in the Reporting table
                $reporting = new Reporting($this->getUserID(), $reportID);
                return $reporting->updateTimestamp();
            }
        } else {
            echo "Report not found.\n";
        }
        return false;
    }
    
    // Method to recognize a report, marking it as reviewed or acknowledged, to be used by ADMIN 
    public function recognizeReport($reportID) {
        // Fetch the report details
        $reportData = new ReportingData();
        $reportDetails = $reportData->getReportDetails($reportID);
    
        // Check if the report exists before updating
        if ($reportDetails) {
            // Update the recognized field to true
            return $reportData->updateReport(['recognized' => 1], $reportID); // Set recognized to 1 (true)
        }
    
        return false;  // Return false if the report was not found
    }

    public function deleteReport($reportID) {
        $reportData = new ReportingData();
        $reportData->getReportDetails($reportID);

        // If the report exists and is not already deleted
        if ($reportData->getReportID() === $reportID && $reportData->isDeleted() === false) {
            return $reportData->deleteReport();
        }
        echo "Report not found or already deleted.\n";
        return false;
    }

    public function getAllActiveReports() {
        $reportData = new ReportingData();
        return $reportData->getAllActiveReports();
    }

    public function getReportsByUserID($reporterID) {
        // Create an instance of Reporting class or ReportingData class
        $reporting = new Reporting();
        
        // Call the appropriate method in Reporting/ReportingData class to fetch reports
        return $reporting->getReportsByUserID($reporterID);
    }

}

class Reporting {
    private $reportID;
    private $reporterID;
    private $created_at;
    private $updated_at;
    private $is_deleted;
 
    private $db;

    public function __construct($reporterID = null, $reportID = null) {
        $this->db = Database::getInstance()->getConnection();
        $this->reporterID = $reporterID;
        $this->reportID = $reportID;
    }

    // create a new reporting relationship between a user and a report
    public function addReporting($reporterID, $reportID) {
        $query = "INSERT INTO reporting (userID, reportID, created_at, updated_at, is_deleted)
                  VALUES ('$reporterID', '$reportID', NOW(), NOW(), 0)";
        
        return run_query($query);
    }

    // retrieve details of a specific reporting record
    public function getReportingDetails($reporterID, $reportID) {
        $query = "SELECT * FROM reporting WHERE userID = $reporterID AND reportID = $reportID AND is_deleted = FALSE";
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

    //to fetch all the reports submitted by a certain user using his ID

    public function getReportsByUserID($reporterID) {
        // Find all reportIDs for this user in the 'reporting' table
        $query = "SELECT reportID FROM reporting WHERE userID = $reporterID AND is_deleted = FALSE";
        $reportIDs = run_select_query($query);
    
        // Check if any reportIDs were found
        if (!$reportIDs) {
            return []; // Return an empty array if no reports are found
        }
    
        // Extract the reportIDs into a comma-separated list for our next query
        $reportIDList = implode(',', array_column($reportIDs, 'reportID'));
    
        // Fetch all reports from the 'report' table where the reportID matches and is not deleted
        $query = "SELECT * FROM report WHERE reportID IN ($reportIDList) AND is_deleted = FALSE";
        return run_select_query($query); // Returns all active reports for the given user
    }


    // update the 'updated_at' timestamp for a reporting record
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
    public function getUserID() {
        return $this->userID;
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

