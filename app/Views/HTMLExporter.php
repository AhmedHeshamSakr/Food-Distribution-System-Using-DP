<?php

require_once '../Models/Exporter.php';

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