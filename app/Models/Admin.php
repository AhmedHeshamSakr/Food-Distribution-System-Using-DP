<?php

require_once __DIR__ . "/../../config/DB.php";
require_once 'Person.php';
require_once 'Badges.php';
require_once 'Reporter.php';

class BadgeAdmin extends User
{
    public function __construct(
        int $userTypeID,
        string $firstName,
        string $lastName,
        string $email,
        string $phoneNo,
        iLogin $login,
    ) {
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }
    public function assignBadge(Person $person, Badges $badge): bool
    {
        // Example logic to assign a badge to a person
        $query = "INSERT INTO badge (userID, badgeID, expiryDate) 
                  VALUES ('{$person->getUserID()}', '{$badge->getBadgeID()}', '{$badge->getExpiryDate()}')";

        return run_query($query);
    }

    public function revokeBadge(Person $person, int $badgeID): bool
    {
        // Example logic to revoke a badge from a person
        $query = "DELETE FROM badge WHERE userID = '{$person->getUserID()}' AND badgeID = '{$badgeID}'";

        return run_query($query);
    }
}


class EventAdmin extends User
{
    public function __construct(
        int $userTypeID,
        string $firstName,
        string $lastName,
        string $email,
        string $phoneNo,
        iLogin $login,
    ) {
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }

    // public function createEvent(string $eventDetails): bool
    // {
        
    // }
}

class VerificationAdmin extends User {
    // Constructor to initialize the admin user
    public function __construct($userTypeID, $firstName, $lastName, $email, $phoneNo, iLogin $login) {
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }

    // Function to review a report (fetch report details)
    public function reviewReport($reportID) {
        // Fetch the report details using ReportingData class
        $reportData = new ReportingData();
        $reportDetails = $reportData->getReportDetails($reportID);

        if ($reportDetails) {
            // Mark the report as under review (this could be a status update or just viewing the details)
            echo "Reviewing Report ID: " . $reportID . "\n";
            print_r($reportDetails);  // Or format the report details as needed
            return true;
        } else {
            echo "Report not found or invalid.\n";
            return false;
        }
    }

    // Function to approve a report
    public function approveReport($reportID) {
        $reportData = new ReportingData();
        
        // Fetch the report details first to ensure it exists
        $reportDetails = $reportData->getReportDetails($reportID);

        if ($reportDetails) {
            // Update the report status to 'approved'
            $updated = $reportData->updateReport(['status' => 'approved'], $reportID);

            if ($updated) {
                // Optionally, we could update the reporting relationship status as well
                echo "Report ID: " . $reportID . " has been approved.\n";
                return true;
            } else {
                echo "Failed to approve the report.\n";
                return false;
            }
        } else {
            echo "Report not found.\n";
            return false;
        }
    }

    // Function to reject a report
    public function rejectReport($reportID) {
        $reportData = new ReportingData();
        
        // Fetch the report details first to ensure it exists
        $reportDetails = $reportData->getReportDetails($reportID);

        if ($reportDetails) {
            // Update the report status to 'rejected'
            $updated = $reportData->updateReport(['status' => 'rejected'], $reportID);

            if ($updated) {
                // Optionally, we could also update the reporting relationship status to reflect rejection
                echo "Report ID: " . $reportID . " has been rejected.\n";
                return true;
            } else {
                echo "Failed to reject the report.\n";
                return false;
            }
        } else {
            echo "Report not found.\n";
            return false;
        }
    }
}
?>