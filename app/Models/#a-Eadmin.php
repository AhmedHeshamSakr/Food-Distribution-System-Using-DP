<?php
require_once 'Person.php';
require_once 'Event.php';
require_once 'Address.php';

class EventAdmin extends Person
{
    // Constructor to initialize EventAdmin with required details
    public function __construct(int $userTypeID, string $firstName, string $lastName, string $email, string $phoneNo, iLogin $login)
    {
        parent::__construct($userTypeID, $firstName, $lastName, $email, $phoneNo, $login);
    }

    // Method to create a new event
    public function createEvent(int $eventID, string $eventDate, Address $eventLocation, string $eventName, string $eventDescription, int $reqCooks, int $reqForDelivery, int $reqCoordinators): bool
    {
        $event = new Event($eventID, $eventDate, $eventLocation, $eventName, $eventDescription, $reqCooks, $reqForDelivery, $reqCoordinators);
        return $event->create();
    }

    // Method to delete an existing event by ID
    public function deleteEvent(int $eventID): bool
    {
        $event = new Event($eventID);
        return $event->delete();
    }

    // Method to update an existing event's details
    public function updateEvent(int $eventID, string $eventDate, Address $eventLocation, string $eventName, string $eventDescription, int $reqCooks, int $reqForDelivery, int $reqCoordinators): bool
    {
        $event = new Event($eventID, $eventDate, $eventLocation, $eventName, $eventDescription, $reqCooks, $reqForDelivery, $reqCoordinators);
        return $event->update();
    }

    // Method to read and fetch an event by ID
    public function readEvent(int $eventID): ?Event
    {
        $event = new Event($eventID);
        return $event->read();
    }

}