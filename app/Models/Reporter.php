<?php 

require_once("User.php");
require_once __DIR__ . '/../../config/DB.php';

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