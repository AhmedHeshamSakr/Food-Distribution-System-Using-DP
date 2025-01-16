<?php

require_once 'Event.php';
require_once 'Delivery.php';
require_once 'ReportData.php';
require_once 'Iterator.php';


$eventList = Event::getAllEvents();
$iterator = $eventList->createIterator();

while ($iterator->hasNext()) {
    $event = $iterator->next();
    echo "Event: " . $event->getEventName() . "\n";
}

// 2. Using Delivery iterator
$deliveryList = Delivery::getAllDeliveries();
$iterator = $deliveryList->createIterator();

while ($iterator->hasNext()) {
    $delivery = $iterator->next();
    echo "Delivery Status: " . $delivery->getStatus() . "\n";
}

// 3. Using ReportingData iterator
$reportList = ReportingData::getAllActiveReports();
$iterator = $reportList->createIterator();

while ($iterator->hasNext()) {
    $report = $iterator->next();
    echo "Report Description: " . $report->getDescription() . "\n";
}