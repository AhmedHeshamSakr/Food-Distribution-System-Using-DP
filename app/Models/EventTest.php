<?php

require_once 'Event.php';
require_once 'Address.php';

// Test Observer Implementation
class TestObserver implements Observer {
    private array $notifications = [];

    public function update(string $message): void {
        $this->notifications[] = $message;
    }

    public function getNotifications(): array {
        return $this->notifications;
    }
}

// Helper function to print test results
function printTestResult(string $testName, bool $success, string $message = '') {
    echo str_repeat('-', 50) . "\n";
    echo "Test: $testName\n";
    echo "Result: " . ($success ? "PASSED" : "FAILED") . "\n";
    if ($message) {
        echo "Message: $message\n";
    }
    echo str_repeat('-', 50) . "\n\n";
}

// Helper function to create a test address

function createTestAddress(): Address {
    // Create a more complete address hierarchy
    try {
        // First, create a country
        $country = new Address("Test Country", null, "Country");
        $country->create();
        
        // Then create a city under that country
        $city = new Address("Test City", $country->getId(), "City");
        $city->create();
        
        return $city;
    } catch (Exception $e) {
        echo "Error creating test address: " . $e->getMessage() . "\n";
        throw $e;
    }
}

try {
    echo "Starting Event Class Tests...\n\n";

    // Test 1: Event Creation
    $testAddress = createTestAddress();
    echo "Created test address with ID: " . $testAddress->getId() . "\n";
    
    $event = new Event(
        null,
        '2025-12-25',
        $testAddress,
        'Christmas Food Drive',
        'Annual Christmas food distribution event',
        2,
        3,
        1
    );
    
    $createResult = $event->create();
    $eventId = $event->getEventID();
    printTestResult(
        "Event Creation",
        $createResult && $eventId > 0,
        "Event ID: " . $eventId
    );

    // Test 2: Event Read
    try {
        echo "Attempting to read event with ID: " . $eventId . "\n";
        $readEvent = Event::fetchById($eventId);
        $readSuccess = $readEvent !== null 
            && $readEvent->getEventName() === 'Christmas Food Drive'
            && $readEvent->getEventDate() === '2025-12-25';
        printTestResult(
            "Event Read",
            $readSuccess,
            "Read event name: " . ($readEvent ? $readEvent->getEventName() : 'null')
        );
    } catch (Exception $e) {
        printTestResult(
            "Event Read",
            false,
            "Error: " . $e->getMessage()
        );
    }


    // Test 3: Event Update
    $readEvent->setEventName('Updated Christmas Food Drive');
    $updateSuccess = $readEvent->update();
    printTestResult(
        "Event Update",
        $updateSuccess,
        "Updated event name to: " . $readEvent->getEventName()
    );

    // Test 4: Observer Pattern Test
    $observer = new TestObserver();
    $readEvent->addObserver($observer);
    
    // Test staff assignment
    $readEvent->assignCook();
    $readEvent->assignDelivery();
    $readEvent->assignCoordinator();
    
    $observerSuccess = count($observer->getNotifications()) > 0;
    printTestResult(
        "Observer Pattern",
        $observerSuccess,
        "Notifications received: " . count($observer->getNotifications())
    );

    // Test 5: Fetch All Events
    $allEvents = Event::fetchAll();
    printTestResult(
        "Fetch All Events",
        is_array($allEvents),
        "Number of events fetched: " . count($allEvents)
    );

    // Test 6: Fetch Upcoming Events
    $upcomingEvents = Event::fetchUpcomingEvents();
    printTestResult(
        "Fetch Upcoming Events",
        is_array($upcomingEvents),
        "Number of upcoming events: " . count($upcomingEvents)
    );

    // Test 7: Event List Iterator
    $eventList = Event::getAllEvents();
    $iteratorSuccess = $eventList !== null;
    printTestResult(
        "Event List Iterator",
        $iteratorSuccess,
        "Event list created successfully"
    );

    // Display all events using iterator
    echo "Displaying all events using iterator:\n";
    foreach ($eventList as $eventItem) {
        echo "- Event: " . $eventItem->getEventName() . 
             " (Date: " . $eventItem->getEventDate() . ")\n";
    }
    echo "\n";

    // Test 8: Event Delete
    // $deleteSuccess = $readEvent->delete();
    // printTestResult(
    //     "Event Delete",
    //     $deleteSuccess,
    //     "Event deleted successfully"
    // );

    // Clean up test data
    // $testAddress->delete();

    // if (isset($readEvent) && $readEvent) {
    //     $readEvent->delete();
    // }
    // if (isset($testAddress) && $testAddress) {
    //     // Delete the city first
    //     $testAddress->delete();
        
    //     // Then delete the country (parent)
    //     if ($testAddress->getParentId()) {
    //         $countryAddress = Address::read($testAddress->getParentId());
    //         if ($countryAddress) {
    //             $countryAddress->delete();
    //         }
    //     }
    // }

    echo "All tests completed!\n";

} catch (Exception $e) {
    echo "Test Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}