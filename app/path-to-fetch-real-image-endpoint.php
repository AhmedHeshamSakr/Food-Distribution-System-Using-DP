<?php

require_once __DIR__ . '/ImageProxy.php';

// Get the filePath from the query string
$filePath = $_GET['filePath'] ?? null;

if ($filePath) {
    // Use the proxy to load and display the real image
    $proxy = new ImageProxy($filePath);
    $realImage = $proxy->loadRealImage(); // Delegate to RealImage
    $realImage->display(); // Output real image HTML
} else {
    echo "<p>Error: File path not provided.</p>";
}
