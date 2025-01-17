<?php

require_once __DIR__ . '/../Models/a-Vadmin.php';
require_once __DIR__ . '/../Views/vAdminView.php';
require_once __DIR__ . '/../Models/Exporter.php';
require_once __DIR__ . '/../Views/HTMLExporter.php'; 
require_once __DIR__ . '/../Models/ControlePanelDPs.php';

class VerificationAdminController {
    private $verificationAdmin;
    private ReportReceiver $reportReceiver;
    private $controlPanel;
    private $adminView;

    // Constructor now includes parameter validation and error handling
    public function __construct(VAdminView $view) {
        if (!$view) {
            throw new InvalidArgumentException("View cannot be null");
        }
        
        // Initialize model components with error handling
        try {
            $this->verificationAdmin = new VerificationAdmin(
                "Admin", 
                "User", 
                "admin@example.com", 
                "123456789"
            );
            
            $this->reportReceiver = new ReportReceiver(
                new ReportingData('', '', '', '')
            );
            
            $this->controlPanel = new ControlPanel();
            $this->adminView = $view;
        } catch (Exception $e) {
            throw new RuntimeException("Failed to initialize controller components: " . $e->getMessage());
        }
    }

    // Added response handling for different HTTP methods
    public function handleRequest() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            
            switch ($method) {
                case 'POST':
                    if (isset($_POST['action'])) {
                        $this->handlePostAction($_POST['action']);
                    }
                    break;
                case 'GET':
                default:
                    $this->showAdminPanel();
                    break;
            }
        } catch (Exception $e) {
            // Log the error and show an appropriate error page
            error_log("Error handling request: " . $e->getMessage());
            $this->adminView->renderError("An error occurred while processing your request.");
        }
    }

    // Added validation for report recognition
    public function recognizeReport($reportID) {
        if (!$reportID || !is_numeric($reportID)) {
            throw new InvalidArgumentException("Invalid report ID provided");
        }

        try {
            $command = new RecognizeReportCommand($this->reportReceiver);
            $command->setReportID($reportID);
            $this->controlPanel->setCommand($command);
            $this->controlPanel->executeCommand();
        } catch (Exception $e) {
            throw new RuntimeException("Failed to recognize report: " . $e->getMessage());
        }
    }

    // Added error handling for data retrieval and rendering
    public function showAdminPanel() {
        try {
            $reports = $this->verificationAdmin->viewAllReports();
            $reportData = $this->prepareReportData($reports);
            $this->adminView->renderAdminPanel($reportData);
        } catch (Exception $e) {
            error_log("Error showing admin panel: " . $e->getMessage());
            $this->adminView->renderError("Unable to load admin panel data.");
        }
    }

    // Improved data preparation with validation
    private function prepareReportData($reports) {
        if (!is_array($reports)) {
            throw new InvalidArgumentException("Reports must be an array");
        }

        $reportData = [
            'header' => 'Verification Admin Panel',
            'timestamp' => date('Y-m-d H:i:s'),
            'title' => 'All Active Reports',
            'body' => [],
            'summary' => $this->generateSummary($reports),
            'footer' => 'Footer information'
        ];

        foreach ($reports as $report) {
            $reportData['body'][] = $this->formatReportData($report);
        }

        return $reportData;
    }

    // New method to handle different POST actions
    private function handlePostAction($action) {
        switch ($action) {
            case 'recognize':
                if (isset($_POST['id'])) {
                    $this->recognizeReport($_POST['id']);
                }
                break;
            default:
                throw new InvalidArgumentException("Unknown action: " . $action);
        }
    }

    // Separated report formatting logic
    private function formatReportData($report) {
        return [
            'Person in Need Name' => htmlspecialchars($report['personINname'] ?? ''),
            'Address' => htmlspecialchars($report['personINaddress'] ?? ''),
            'Phone' => htmlspecialchars($report['phoneINno'] ?? ''),
            'Description' => htmlspecialchars($report['description'] ?? ''),
            'Status' => htmlspecialchars($report['status'] ?? ''),
            'Actions' => $this->generateActionButtons($report['reportID'])
        ];
    }

    // Enhanced button generation with XSS protection
    private function generateActionButtons($reportID) {
        $reportID = htmlspecialchars($reportID);
        return sprintf(
            '<form method="post">
                <input type="hidden" name="action" value="recognize">
                <input type="hidden" name="id" value="%s">
                <button type="submit" class="btn btn-primary">Recognize</button>
            </form>',
            $reportID
        );
    }

    // New method to generate summary statistics
    private function generateSummary($reports) {
        $totalReports = count($reports);
        $pendingReports = count(array_filter($reports, function($report) {
            return $report['status'] === 'pending';
        }));

        return sprintf(
            'Total Reports: %d, Pending: %d',
            $totalReports,
            $pendingReports
        );
    }
}
?>