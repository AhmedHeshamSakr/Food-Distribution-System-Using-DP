<?php
require_once 'HtmlExporter.php';
require_once '../Models/Exporter.php';
require_once '../Models/ReportTemplateModel.php';
$deliveryData = [
    [
        'deliveryID' => 'D001',
        'deliveryDate' => '2023-10-01',
        'startLocation' => 'Warehouse A',
        'endLocation' => 'Customer X',
        'status' => 'Delivered'
    ],
    [
        'deliveryID' => 'D002',
        'deliveryDate' => '2023-10-02',
        'startLocation' => 'Warehouse B',
        'endLocation' => 'Customer Y',
        'status' => 'In Transit'
    ]
];

function testHtmlExporter($reportData) {
    ob_start();
    $htmlExporter = new HtmlExporter();
    $report = new DeliveryReport($reportData, $htmlExporter);
    $report->generateReport();
    $output = ob_get_clean();
    // Verify that the output contains expected HTML content
    assert(strpos($output, 'Delivery Details Report') !== false, 'HTML export failed');
    // Add more assertions as needed
}

function testJsonExporter($reportData) {
    ob_start();
    $jsonExporter = new JsonExporter();
    $report = new DeliveryReport($reportData, $jsonExporter);
    $report->generateReport();
    $output = ob_get_clean();
    $decodedJson = json_decode($output, true);
    // Verify that the JSON data matches the expected structure
    assert($decodedJson['title'] === 'Delivery Details Report', 'JSON export failed');
    // Add more assertions as needed
}

// function testPdfExporter($reportData) {
//     $pdfExporter = new PdfExporter();
//     $report = new DeliveryReport($reportData, $pdfExporter);
//     $report->generateReport();
//     // Manually check the generated PDF file
//     echo "PDF generated, please manually verify.\n";
// }

function testCsvExporter($reportData) {
    $csvExporter = new CsvExporter();
    $report = new DeliveryReport($reportData, $csvExporter);
    $report->generateReport();
    // Manually check the generated CSV file
    echo "CSV generated, please manually verify.\n";
}

echo "Testing HTML Exporter...\n";
testHtmlExporter($deliveryData);

echo "Testing JSON Exporter...\n";
testJsonExporter($deliveryData);

// echo "Testing PDF Exporter...\n";
// testPdfExporter($deliveryData);

echo "Testing CSV Exporter...\n";
testCsvExporter($deliveryData);

echo "All tests completed.\n";