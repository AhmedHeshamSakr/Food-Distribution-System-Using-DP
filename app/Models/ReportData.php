<?php 

require_once("User.php");
require_once __DIR__ . '/../../config/DB.php';

class ReportingData {
    private $reportID;
    private $personInName;
    private $personInAddress;
    private $personInPhone;
    private $status;
    private $recognized;
    private $description;
    private $isDeleted;

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createReport($personInName, $personInAddress, $personInPhone, $description) {
        $status = 'Pending'; // Default to "Pending"
        $recognized = 0; // Default to not recognized (false)
        $isDeleted = 0; // Default to not deleted (false)
    
        // Construct the query with clear placeholders and values
        $query = "INSERT INTO report (personINname, personINaddress, phoneINno, status, recognized, description, is_deleted) 
                  VALUES ('$personInName', '$personInAddress', '$personInPhone', '$status', $recognized, '$description', $isDeleted)";
    
        if (run_query($query)) {
            $this->reportID = mysqli_insert_id($this->db);
            return true;
        }
        return false;
    }

    public function deleteReport() {
        $query = "UPDATE report SET is_deleted = TRUE WHERE reportID = '{$this->reportID}'";
        return run_query($query);
    }

    // Update report details
    public function updateReport($fieldsToUpdate) {
        $setQuery = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $setQuery[] = "$field = '$value'";
        }
        $setQueryStr = implode(', ', $setQuery);
        $query = "UPDATE report SET $setQueryStr WHERE reportID = '{$this->reportID}' AND is_deleted = FALSE";
        return run_query($query);
    }

    public function getReportDetails($reportID) {
        $query = "SELECT * FROM report WHERE reportID = $reportID AND is_deleted = FALSE";
        $result = run_select_query($query);
        return $result ? $result[0] : null; // Return the first result as an associative array or null if not found
    }

    public function getReportID() {
        return $this->reportID;
    }

    // Getter and Setter for personInName
    public function getPersonInName() {
        return $this->personInName;
    }

    public function setPersonInName($personInName) {
        $this->personInName = $personInName;
    }

    // Getter and Setter for personInAddress
    public function getPersonInAddress() {
        return $this->personInAddress;
    }

    public function setPersonInAddress($personInAddress) {
        $this->personInAddress = $personInAddress;
    }

    // Getter and Setter for personInPhone
    public function getPersonInPhone() {
        return $this->personInPhone;
    }

    public function setPersonInPhone($personInPhone) {
        $this->personInPhone = $personInPhone;
    }

    // Getter and Setter for status
    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    // Getter and Setter for recognized
    public function isRecognized() {
        return $this->recognized;
    }

    public function setRecognized($recognized) {
        $this->recognized = $recognized;
    }

    // Getter and Setter for description
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    // Getter and Setter for isDeleted
    public function isDeleted() {
        return $this->isDeleted;
    }

    public function setIsDeleted($isDeleted) {
        $this->isDeleted = $isDeleted;
    }
 
}


