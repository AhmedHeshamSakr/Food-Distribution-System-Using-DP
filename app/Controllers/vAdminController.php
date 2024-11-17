<?php

require_once __DIR__ . '/../Models/VerificationAdmin.php';
require_once __DIR__ . '/../Views/vAdminView.php';

class VerificationAdminController
{
    private $verificationAdmin;

    public function __construct()
    {
        $this->verificationAdmin = new VerificationAdmin("Admin", "User", "admin@example.com", "123456789");
    }

    public function handleRequest()
    {
        $action = $_POST['action'] ?? null;
        $reportID = $_POST['id'] ?? null;
        $newStatus = $_POST['status'] ?? null;

        switch ($action) {
            case 'update':
                $result = $this->verificationAdmin->updateReportStatus($reportID, $newStatus);
                $this->showAdminPanel($result ? "Report status updated to $newStatus." : "Failed to update report status.");
                break;
            default:
                $this->showAdminPanel();
        }
    }

    private function showAdminPanel($actionResult = null)
    {
        $reports = $this->verificationAdmin->viewAllReports();
        renderAdminPanel($reports, $actionResult);
    }
}
?>
