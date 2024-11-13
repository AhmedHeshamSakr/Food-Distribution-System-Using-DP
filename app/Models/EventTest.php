<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../config/DB.php";
require_once 'Event.php';

// Create a new Event object
$event = new Event(1, '2024-12-01', 101, 'Music Concert', 'A wonderful concert with popular artists.');

// Attempt to create the event in the database
$eventCreated = $event->create();
echo $eventCreated ? "Event created successfully.<br>" : "Failed to create event.<br>";

// Fetch and display event details
$eventDetails = $event->read();
if ($eventDetails) {
    echo "Event ID: " . $eventDetails->getEventID() . "<br>";
    echo "Event Name: " . $eventDetails->getEventName() . "<br>";
    echo "Event Date: " . $eventDetails->getEventDate() . "<br>";
    echo "Event Location: " . $eventDetails->getEventLocation() . "<br>";
    echo "Event Description: " . $eventDetails->getEventDescription() . "<br>";
} else {
    echo "Event not found.<br>";
}

// Update the event details
$event->setEventName('Updated Music Concert');
$event->setEventDate('2024-12-05');
$event->setEventLocation(102);
$event->setEventDescription('Updated event description.');
$eventUpdated = $event->update();
echo $eventUpdated ? "Event updated successfully.<br>" : "Failed to update event.<br>";

// Delete the event
$eventDeleted = $event->delete();
echo $eventDeleted ? "Event deleted successfully.<br>" : "Failed to delete event.<br>";


