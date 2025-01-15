<?php

require_once 'Person.php';
require_once 'Event.php';
require_once 'Address.php';



class Admin extends Person {
    
}

class EventAdmin extends Admin implements Observer
{
    private int $userTypeID = Person::E_ADMIN_FLAG;

    // Constructor to initialize EventAdmin with required details
    public function __construct(string $firstName, string $lastName, string $email, string $phoneNo)
    {
        // Set userTypeID as E_ADMIN_FLAG for EventAdmin
        $this->userTypeID = Person::E_ADMIN_FLAG;
        parent::__construct($firstName, $lastName, $email, $phoneNo, $this->userTypeID);
    }

    // Implementing Observer's update method for notifications
    public function update(string $message): void
    {
        echo "Admin Notification: $message\n";
    }

    // Method to create a new event
   public function createEvent(
    string $eventDate,
    Address $eventLocation,
    string $eventName,
    string $eventDescription,
    int $reqCooks,
    int $reqForDelivery,
    int $reqCoordinators
): bool {
    // Validate if the address ID is properly set (make sure the address is created first)
    if (!$eventLocation->getId()) {
        throw new Exception("Address ID must be set before creating an event.");
    }

    // Create a new Event object with the provided data
    $event = new Event(
        null, // Assuming eventID is auto-incremented, we pass null or leave it out
        $eventDate,
        $eventLocation,  // pass the address object
        $eventName,
        $eventDescription,
        $reqCooks,
        $reqForDelivery,
        $reqCoordinators
    );

    // Call the create method on the Event object to save it to the database
    return $event->create();
}


    // Method to delete an existing event by ID
    public function deleteEvent(int $eventID): bool
    {
        // Create an Event object with the given ID and delete it
        $event = new Event($eventID);
        return $event->delete();
    }

    // Method to update an existing event's details
    public function updateEvent(
        int $eventID,
        string $eventDate,
        Address $eventLocation,
        string $eventName,
        string $eventDescription,
        int $reqCooks,
        int $reqForDelivery,
        int $reqCoordinators
    ): bool {
        // Ensure the address ID is valid
        if (!$eventLocation->getId()) {
            throw new Exception("Invalid Address ID. Update failed.");
        }

        // Create an Event object with updated data
        $event = new Event(
            $eventID,
            $eventDate,
            $eventLocation,
            $eventName,
            $eventDescription,
            $reqCooks,
            $reqForDelivery,
            $reqCoordinators
        );

        // Call the update method to persist changes in the database
        return $event->update();
    }

    // Method to read and fetch an event by ID
    public function readEvent(int $eventID): ?Event
    {
        // Create a new Event object with the specified ID and read its data
        $event = new Event($eventID);
        return $event->read();
    }

    // Method to fetch all events
    public function getAllEvents(): array
    {
        // Utilize the static fetchAll method to retrieve all events
        return Event::fetchAll();
    }

    // // Method to assign a cook to an event
    // public function assignCookToEvent(Event $event): void
    // {
    //     $event->assignCook();
    //     $event->notifyObservers("Cook assigned to event '{$event->getEventName()}'.");
    // }

    // // Method to assign a delivery person to an event
    // public function assignDeliveryToEvent(Event $event): void
    // {
    //     $event->assignDelivery();
    //     $event->notifyObservers("Delivery person assigned to event '{$event->getEventName()}'.");
    // }

    // Method to assign a coordinator to an event
    // public function assignCoordinatorToEvent(Event $event): void
    // {
    //     $event->assignCoordinator();
    //     $event->notifyObservers("Coordinator assigned to event '{$event->getEventName()}'.");
    // }

    // Getter for userTypeID (specific to EventAdmin)
    public function getUserTypeID(): int
    {
        return $this->userTypeID;
    }
}