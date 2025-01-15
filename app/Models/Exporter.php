<?php

interface ExporterInterface {
    public function export(array $reportData);
}

class JsonExporter implements ExporterInterface {
    public function export(array $reportData) {
        header('Content-Type: application/json');
        echo json_encode([
            'header' => $reportData['header'],
            'timestamp' => $reportData['timestamp'],
            'title' => $reportData['title'],
            'body' => $reportData['body'],
            'summary' => $reportData['summary'],
            'footer' => $reportData['footer']
        ], JSON_PRETTY_PRINT);
    }
}

class CsvExporter implements ExporterInterface {
    public function export(array $reportData) {
        $filename = 'report_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Write report metadata
        fputcsv($output, ['Report Metadata']);
        fputcsv($output, ['Header', $reportData['header']]);
        fputcsv($output, ['Timestamp', $reportData['timestamp']]);
        fputcsv($output, ['Title', $reportData['title']]);
        fputcsv($output, ['Summary', $reportData['summary']]);
        fputcsv($output, ['Footer', $reportData['footer']]);
        fputcsv($output, []); // Empty row for separation
        
        // Write body data
        fputcsv($output, ['Report Data']);
        
        // Get all possible keys from body items
        $headers = [];
        foreach ($reportData['body'] as $item) {
            foreach (array_keys($item) as $key) {
                if (!in_array($key, $headers)) {
                    $headers[] = $key;
                }
            }
        }
        
        // Write headers
        fputcsv($output, $headers);
        
        // Write data rows
        foreach ($reportData['body'] as $item) {
            $row = [];
            foreach ($headers as $header) {
                $row[] = $item[$header] ?? '';
            }
            fputcsv($output, $row);
        }
        
        fclose($output);
    }
}