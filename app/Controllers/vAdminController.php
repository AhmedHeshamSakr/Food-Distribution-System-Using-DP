<?php

require_once __DIR__ . '/../Models/VerificationAdmin.php';
require_once __DIR__ . '/../Views/vAdminView.php';
require_once __DIR__ . '/../Controllers/ControlePanelDPs.php';
require_once __DIR__ . '/../Models/Exporter.php';
require_once __DIR__ . '/../Models/HTMLExporter.php';

class VerificationAdminController
{
    private $verificationAdmin;
    private ReportReceiver $reportReceiver;
    private $controlPanel;

    public function __construct()
    {
        $this->verificationAdmin = new VerificationAdmin("Admin", "User", "admin@example.com", "123456789");
        $this->reportReceiver = new ReportReceiver(
            new ReportingData('', '', '', ''),
        );
        $this->controlPanel = new ControlPanel();
    }

    public function handleRequest()
    {
    
    }

    public function recognizeReport($reportID)
    {
        $command = new RecognizeReportCommand($this->reportReceiver);
        $command->setReportID($reportID);
        $this->controlPanel->setCommand($command);
        $this->controlPanel->executeCommand();
    }

    public function showAdminPanel()
    {
        $reports = $this->verificationAdmin->viewAllReports();
        $exporter = new HtmlExporter();
        $reportData = [
            'header' => 'Verification Admin Panel',
            'timestamp' => date('Y-m-d H:i:s'),
            'title' => 'All Active Reports',
            'body' => [],
            'summary' => 'Summary of reports',
            'footer' => 'Footer information'
        ];

        foreach ($reports as $report) {
            $reportData['body'][] = [
                'Person in Need Name' => $report['personINname'],
                'Address' => $report['personINaddress'],
                'Phone' => $report['phoneINno'],
                'Description' => $report['description'],
                'Status' => $report['status'],
                'Actions' => '<form method="post">
                                <input type="hidden" name="action" value="recognize">
                                <input type="hidden" name="id" value="' . $report['reportID'] . '">
                                <button type="submit">Recognize</button>
                              </form>'
            ];
        }

        $exporter->export($reportData);
    }
}
?>