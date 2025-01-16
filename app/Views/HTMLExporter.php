<?php

require_once '../Models/Exporter.php';

class HtmlExporter implements ExporterInterface {
    public function export(array $reportData) {

        echo '<!DOCTYPE html>
        <html>
        <head>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        </head>
        <body>';
        
        echo '<div class="container mt-4">';
        echo '<div class="card">';
        echo '<div class="card-header">';
        echo '<h1 class="text-center">' . htmlspecialchars($reportData['header']) . '</h1>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<p class="text-muted">' . htmlspecialchars($reportData['timestamp']) . '</p>';
        echo '<h3 class="mb-4">' . htmlspecialchars($reportData['title']) . '</h3>';
        
        echo '<div class="row">';
        foreach ($reportData['body'] as $item) {
            echo '<div class="col-md-6 mb-3">';
            echo '<div class="card h-100">';
            echo '<div class="card-body">';
            foreach ($item as $key => $value) {
                echo '<p><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</p>';
            }
            echo '</div></div></div>';
        }
        echo '</div>';
        
        echo '<div class="mt-4">';
        echo '<p class="lead">' . htmlspecialchars($reportData['summary']) . '</p>';
        echo '</div>';
        
        echo '</div>'; // card-body
        echo '<div class="card-footer text-center">';
        echo '<h4>' . htmlspecialchars($reportData['footer']) . '</h4>';
        echo '</div>';
        echo '</div>'; // card
        echo '</div>'; // container
        echo '</body></html>';
    }
}