<?php

require_once 'Reporter.php';
require_once 'Person.php';

class VerificationAdmin extends Person
{
    private Reporter $reporter;

    public function __construct($firstName, $lastName, $email, $phoneNo)
    {
        parent::__construct(Person::V_ADMIN_FLAG, $firstName, $lastName, $email, $phoneNo);
        $this->reporter = new Reporter(Person::REPORTER_FLAG, "Temp", "User", "temp@example.com", "0000000000");
    }

    public function viewAllReports()
    {
        return $this->reporter->getAllActiveReports();
    }

    public function updateReportStatus($reportID, $newStatus)
    {
        return $this->reporter->updateReportStatus($reportID, $newStatus);
    }
}
