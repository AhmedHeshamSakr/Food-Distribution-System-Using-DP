
<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../config/DB.php";
require_once 'Event.php';
require_once 'Address.php';

// Step 1: Create and verify the initial address location
$eventLocation = new Address(20, 'Downtown Venue', 6, 'City');
$addressCreated = $eventLocation->create();

echo $addressCreated ? "Address created successfully.<br>" : "Address already exists or failed to create.<br>";

// Step 2: Create an Event object with the valid Address object as eventLocation
$event = new Event(10, '2024-12-01', $eventLocation, 'Music Concert', 'A wonderful concert with popular artists.');
$eventCreated = $event->create();

echo $eventCreated ? "Event created successfully.<br>" : "Failed to create event.<br>";

// Step 3: Fetch and display event details
$eventDetails = $event->read();
if ($eventDetails) {
    echo "Event ID: " . $eventDetails->getEventID() . "<br>";
    echo "Event Name: " . $eventDetails->getEventName() . "<br>";
    echo "Event Date: " . $eventDetails->getEventDate() . "<br>";
    echo "Event Location Name: " . $eventDetails->getEventLocation()->getName() . "<br>";
    echo "Event Description: " . $eventDetails->getEventDescription() . "<br>";
} else {
    echo "Event not found.<br>";
}

// Step 4: Update the event with a new address
$newLocation = new Address(7, 'Updated Venue', 7, 'City');
$newLocationCreated = $newLocation->create();

if ($newLocationCreated) {
    $event->setEventLocation($newLocation);
    $eventUpdated = $event->update();
    echo $eventUpdated ? "Event updated successfully.<br>" : "Failed to update event.<br>";
} else {
    echo "Failed to create or confirm new address for updating event location.<br>";
}

// Step 5: Delete the event
$eventDeleted = $event->delete();
echo $eventDeleted ? "Event deleted successfully.<br>" : "Failed to delete event.<br>";

// Cleanup: Delete address records used in the test
$addressDeleted = $eventLocation->delete();
echo $addressDeleted ? "Initial address deleted successfully.<br>" : "Failed to delete initial address.<br>";

$newAddressDeleted = $newLocation->delete();
echo $newAddressDeleted ? "Updated address deleted successfully.<br>" : "Failed to delete updated address.<br>";