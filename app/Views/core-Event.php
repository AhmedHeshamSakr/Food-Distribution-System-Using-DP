<?php

require_once __DIR__ . "/../Controllers/EventController.php";
require_once __DIR__ . '/../Models/Event.php';
require_once __DIR__ . '/../Models/Address.php';


$eventLocation = new Address( 'Egypt',NULL, 'Country');
$addressCreated = $eventLocation->create();
echo $addressCreated ? "Address created successfully.<br>" : "Address already exists or failed to create.<br>";
// Step 1: Create and verify the initial address location
$eventLocation = new Address( 'Downtown Venue', Address::getIdByName('Egypt'), 'City');
$addressCreated = $eventLocation->create();

echo $addressCreated ? "Address created successfully.<br>" : "Address already exists or failed to create.<br>";

// Step 2: Create an Event object with the valid Address object as eventLocation
$event = new Event(10, '2024-12-01', $eventLocation, 'Music Concert', 'A wonderful concert with popular artists.');
$eventCreated = $event->create();

// Initialize the controller
$controller = new EventController();

// Display the events
// $controller->displayEvents();

