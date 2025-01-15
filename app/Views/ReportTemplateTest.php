<?php
require_once 'HTMLExporter.php';
require_once '../Models/Exporter.php';
require_once '../Models/ReportTemplateModel.php';

// Test data
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

//Test HTML Export

echo "Testing HTML Exporter...\n";
$htmlExporter = new HtmlExporter();
$report = new DeliveryReport($deliveryData, $htmlExporter);
$report->generateReport();

// Test JSON Export

// echo "\nTesting JSON Exporter...\n";
// $jsonExporter = new JsonExporter();
// $report = new DeliveryReport($deliveryData, $jsonExporter);
// $report->generateReport();


// Test CSV Export

// echo "\nTesting CSV Exporter...\n";
// $csvExporter = new CsvExporter();
// $report = new DeliveryReport($deliveryData, $csvExporter);
// $report->generateReport();
