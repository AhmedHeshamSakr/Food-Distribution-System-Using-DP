<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../config/DB.php";
require_once 'Event.php';
require_once 'Address.php';

/**
 * Utility function to log test results
 */
function logResult($message, $success) {
    echo $message . ($success ? " Success.<br>" : " Failed.<br>");
}

try {
    // Step 1: Create and verify the initial address
    // Assuming 'City' is a valid level and null for parent_id indicates it's a top-level city
    $eventLocation = new Address('Downtown Venue', null, 'City');
    logResult("Address created", $eventLocation->create());

    // Step 2: Create an Event object with the valid Address object as eventLocation
    $event1 = new Event(null, '2024-12-01', $eventLocation, 'Music Concert', 'A wonderful concert with popular artists.', 2, 1, 1);
    logResult("Event 1 created", $event1->create());

    // Step 3: Create another event to have multiple events for fetching
    $event2 = new Event(null, '2024-12-15', $eventLocation, 'Food Festival', 'A fun-filled food festival.', 3, 2, 1);
    logResult("Event 2 created", $event2->create());

    // Step 4: Fetch and display individual event details
    $eventDetails = $event1->read();
    if ($eventDetails) {
        echo "<strong>Event 1 Details:</strong><br>";
        echo "Event ID: {$eventDetails->getEventID()}<br>";
        echo "Event Name: {$eventDetails->getEventName()}<br>";
        echo "Event Date: {$eventDetails->getEventDate()}<br>";
        echo "Event Location: {$eventDetails->getEventLocation()->getName()}<br>";
        echo "Event Description: {$eventDetails->getEventDescription()}<br><br>";
    } else {
        echo "Event 1 not found.<br>";
    }

    // Step 5: Test the fetchAll() method to retrieve all events
    echo "<strong>Testing fetchAll() Method:</strong><br>";
    $allEvents = Event::fetchAll();

    if (!empty($allEvents)) {
        foreach ($allEvents as $index => $event) {
            echo "<strong>Event " . ($index + 1) . ":</strong><br>";
            echo "Event ID: " . $event->getEventID() . "<br>";
            echo "Event Name: " . $event->getEventName() . "<br>";
            echo "Event Date: " . $event->getEventDate() . "<br>";
            echo "Event Location: " . $event->getEventLocation()->getName() . "<br>";
            echo "Event Description: " . $event->getEventDescription() . "<br><br>";
        }
    } else {
        echo "No events found.<br>";
    }

    // Step 6: Cleanup - Delete the created events
    logResult("Event 1 deleted", $event1->delete());
    logResult("Event 2 deleted", $event2->delete());

    // Step 7: Cleanup - Delete the address used in the test
    logResult("Initial address deleted", $eventLocation->delete());

} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
}