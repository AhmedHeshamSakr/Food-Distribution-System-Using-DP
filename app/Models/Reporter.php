<?php 

require_once("User.php");
require_once __DIR__ . "D:\xampp\htdocs\FDS project\Food-Distribution-System-Using-DP\sql\OUR DB.sql";

class Reporter extends User {
    public function submitReport($name) {
        // Logic to submit a new report
        // Returns boolean true if successful, false otherwise
    }

    public function verifyReport($reportID) {
        // Logic to verify a report
        // Returns boolean true if successful, false otherwise
    }

    public function updateReportStatus($reportID, $status) {
        // Logic to update the status of a report
        // Returns boolean true if successful, false otherwise
    }
}

class ReportingData {
    private $reportID;
    private $personInName;
    private $personInAddress;
    private $personInPhone;
    private $status;
    private $verified;

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters and Setters
    public function getReportID() {
        return $this->reportID;
    }

    public function setReportID($reportID) {
        $this->reportID = $reportID;
    }

    public function getPersonInName() {
        return $this->personInName;
    }

    public function setPersonInName($personInName) {
        $this->personInName = $personInName;
    }

    public function getPersonInAddress() {
        return $this->personInAddress;
    }

    public function setPersonInAddress($personInAddress) {
        $this->personInAddress = $personInAddress;
    }

    public function getPersonInPhone() {
        return $this->personInPhone;
    }

    public function setPersonInPhone($personInPhone) {
        $this->personInPhone = $personInPhone;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getVerified() {
        return $this->verified;
    }

    public function setVerified($verified) {
        $this->verified = $verified;
    }


    public function createReport($personInName, $personInAddress, $personInPhone) {
        $status = 'Pending';
        $verified = 0; // Not verified
        $query = "INSERT INTO report (personInName, personInAddress, personInPhone, status, verified) 
                  VALUES ('$personInName', '$personInAddress', '$personInPhone', '$status', $verified)";
        
        if (run_query($query)) {
            // Set the reportID to the last inserted ID
            $this->reportID = mysqli_insert_id($this->db);
            return true;
        }
        return false;
    }

    public function deleteReport() {
        $query = "DELETE FROM report WHERE reportID = '{$this->reportID}'";
        return run_query($query);
    }

    public function updateReport() {
        // Logic to update an existing report
        // Returns boolean true if successful, false otherwise
    }

    public function readReport() {
        // Logic to read report details
        // Returns data object or array
    }
}

class Reporting {
    public $reportID;
    public $reporterID;
    public $created_at;
    public $updated_at;

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); // Using the Singleton database connection
    }

    
    // Depending on functionality, you might add methods here that:
    // - Log when a report is assigned to a reporter
    // - Update the timestamp when changes are made
}


?>