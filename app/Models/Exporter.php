<?php

interface ExporterInterface {
    public function export(array $reportData);
}

class JsonExporter implements ExporterInterface {
    public function export(array $reportData) {
        echo json_encode($reportData, JSON_PRETTY_PRINT);
    }
}

class CsvExporter implements ExporterInterface {
    public function export(array $reportData) {
        // Create a temporary file to store CSV data
        $filename = tempnam(sys_get_temp_dir(), 'report_') . '.csv';
        $file = fopen($filename, 'w');

        // Write the header row
        fputcsv($file, ['Header', 'Timestamp', 'Title', 'Summary', 'Footer']);

        // Write the main report data
        fputcsv($file, [
            $reportData['header'],
            $reportData['timestamp'],
            $reportData['title'],
            $reportData['summary'],
            $reportData['footer']
        ]);

        // Write the body data
        fputcsv($file, []); // Empty row for separation
        fputcsv($file, ['Body Data']);
        foreach ($reportData['body'] as $item) {
            fputcsv($file, $item);
        }

        // Close the file
        fclose($file);

        // Output the CSV file to the browser
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report.csv"');
        readfile($filename);

        // Delete the temporary file
        unlink($filename);
    }
}
