<?php
require_once '../Models/ReportData.php';
require_once '../Models/Event.php';
require_once '../Models/Donation.php';
require_once '../Models/Reporter.php';
require_once 'ReportTemplateView.php';


interface ExporterInterface {
    public function export(array $reportData);
}

class HtmlExporter implements ExporterInterface {
    public function export(array $reportData) {
        echo '<div class="container">';
        echo '<h1>' . $reportData['header'] . '</h1>';
        echo '<p>' . $reportData['timestamp'] . '</p>';
        echo '<h3>' . $reportData['title'] . '</h3>';
        foreach ($reportData['body'] as $item) {
            echo '<div class="item">';
            foreach ($item as $key => $value) {
                echo '<p>' . $key . ': ' . $value . '</p>';
            }
            echo '</div>';
        }
        echo '<p>' . $reportData['summary'] . '</p>';
        echo '<h4>' . $reportData['footer'] . '</h4>';
        echo '</div>';
    }
}

class JsonExporter implements ExporterInterface {
    public function export(array $reportData) {
        echo json_encode($reportData, JSON_PRETTY_PRINT);
    }
}

class CsvExporter implements ExporterInterface {
    public function export(array $reportData) {
        // Implementation to generate CSV from $reportData
    }
}

