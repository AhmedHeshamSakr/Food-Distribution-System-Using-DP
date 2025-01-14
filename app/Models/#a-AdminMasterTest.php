<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '#a-Eadmin.php';
require_once 'Event.php';
require_once 'Address.php';


// Log utility function
function logResult($message, $success) {
    echo $message . ($success ? " Success.<br>" : " Failed.<br>");
}

try {
    // Create an EventAdmin instance
    $admin = new EventAdmin("John", "Doe", "john.doe@example.com", "1234567890");
    echo "Creating an address...\n";
    $address = new Address("Cairo", null, "City");
    $addressCreated = $address->create();

    if ($addressCreated) {
        echo "Address created successfully with ID: " . $address->getId() . "<br>";
        
        // Assign the newly created address to $eventLocation
        $eventLocation = $address; // Ensure $eventLocation is assigned
        // Step 3: Create multiple events using the EventAdmin object
        echo "Creating multiple events...\n";

        // Create Event 1
        $event1Created = $admin->createEvent(
            '2024-12-01', 
            $eventLocation, 
            'Music Concert', 
            'A wonderful concert with popular artists.', 
            2, // Required Cooks
            1, // Required for Delivery
            1  // Required Coordinators
        );
        echo $event1Created ? "Event 1 created successfully.<br>" : "Event 1 creation failed.<br>";

        // Create Event 2
        $event2Created = $admin->createEvent(
            '2024-12-15', 
            $eventLocation, 
            'New Year Gala', 
            'A grand celebration to welcome the New Year.', 
            3, // Required Cooks
            2, // Required for Delivery
            2  // Required Coordinators
        );
        echo $event2Created ? "Event 2 created successfully.<br>" : "Event 2 creation failed.<br>";

        
    } else {
        echo "Failed to create address.<br>";
    }
    // // Step 3: Fetch a single event by ID
    // echo "\nFetching the first event from the database...\n";
    // $eventID = 1; // Assuming this is the eventID of the first created event.
    // $eventFetched = $admin->readEvent($eventID);
    // if ($eventFetched) {
    //     echo "Event fetched successfully. Event Name: " . $eventFetched->getEventName() . "<br>";
    // } else {
    //     echo "Failed to fetch event.<br>";
    // }

    // Step 4: Update event details
    // echo "\nUpdating event details for the first event...\n";
    // if ($eventFetched) {
    //     $eventFetched->setEventName("Updated Christmas Celebration");
    //     $eventFetched->setEventDescription("Updated event details for the Christmas Celebration.");
    //     logResult("Event updated", $eventFetched->update());
    // }

    // Step 5: Fetch all events using fetchAll()
    echo "\nFetching all events from the database...\n";
    $allEvents = $admin->getAllEvents();
    if (!empty($allEvents)) {
        echo "Fetched " . count($allEvents) . " events successfully:<br>";
        foreach ($allEvents as $event) {
            echo "Event ID: " . $event->getEventID() . "<br>";
            echo "Event Name: " . $event->getEventName() . "<br>";
            echo "Event Date: " . $event->getEventDate() . "<br>";
            echo "Event Location: " . $event->getEventLocation()->getName() . "<br>";
            echo "Event Description: " . $event->getEventDescription() . "<br>";
            echo "-----------------------------------<br>";
        }
    } else {
        echo "No events found.<br>";
    }

    // // Step 6: Delete all events created for testing
    // echo "\nDeleting all events...\n";
    // foreach ($allEvents as $event) {
    //     logResult("Event ID " . $event->getEventID() . " deleted", $event->delete());
    // }

    // // Cleanup: Delete the address used in the test
    // echo "\nDeleting test address...\n";
    // logResult("Address deleted", $address->delete());

} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
}
?>