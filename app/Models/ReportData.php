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

    public function __construct($personInName,$personInAddress,$personInPhone,$description ) {
        $this->db = Database::getInstance()->getConnection();
        $this->status = 'Pending'; // Default to "Pending"
        $this->recognized = 0; // Default to not recognized (false)
        $this->isDeleted = 0; // Default to not deleted (false)
        $this->description = $description;
        $this->createReport($personInName, $personInAddress, $personInPhone, $description);
    
    }

    public function createReport($personInName, $personInAddress, $personInPhone, $description) {
        
        // Construct the query with clear placeholders and values
        $query = "INSERT INTO report (personINname, personINaddress, phoneINno, status, recognized, description, is_deleted) 
                  VALUES ('$personInName', '$personInAddress', '$personInPhone', '{$this->status}', {$this->recognized}, '$description', {$this->isDeleted})";
    
        if (run_query($query)) {
            $this->reportID = mysqli_insert_id($this->db);
            return true;
        }
        return false;
    }

    public function deleteReport($reportID = null) {
        // Use the specified reportID, or default to the object's reportID if available
        $id = $reportID ?? $this->reportID;
    
        // Check if reportID is available
        if (!$id) {
            echo "Error: Report ID not specified.\n";
            return false;
        }
    
        // Perform the soft delete by setting `is_deleted` to TRUE
        $query = "UPDATE report SET is_deleted = TRUE WHERE reportID = '$id'";
        return run_query($query);
    }
    

    // Update report details
    public function updateReport($fieldsToUpdate, $reportID = null) {
        $id = $reportID ?? $this->reportID; // Use passed reportID or object’s reportID
        if (!$id) {
            echo "Error: Report ID not specified.\n";
            return false;
        }

        $setQuery = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $setQuery[] = "$field = '$value'";
        }
        $setQueryStr = implode(', ', $setQuery);
        $query = "UPDATE report SET $setQueryStr WHERE reportID = '$id' AND is_deleted = FALSE";
        return run_query($query);
    }

    //REAADD
    public function getReportDetails($reportID) {
        $query = "SELECT * FROM report WHERE reportID = $reportID AND is_deleted = FALSE";
        $result = run_select_query($query);
        return $result ? $result[0] : null; // Return the first result as an associative array or null if not found
    }

    public function getAllActiveReports() {
        $query = "SELECT * FROM report WHERE is_deleted = FALSE";
        return run_select_query($query);
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