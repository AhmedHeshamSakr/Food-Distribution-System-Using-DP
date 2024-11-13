
<?php

require_once __DIR__ . "/../../config/DB.php";
require_once 'Address.php';

class Event
{
    // Properties
    private int $eventID;
    private string $eventDate;
    private Address $eventLocation; // Updated to Address type
    private string $eventName;
    private string $eventDescription;
    private int $reqCooks;
    private int $reqForDelivery;
    private int $reqCoordinators;

    // Constructor to initialize the Event object
    public function __construct(int $eventID = 0, string $eventDate = '', Address $eventLocation = null, string $eventName = '', string $eventDescription = '', int $reqCooks = 0, int $reqForDelivery = 0, int $reqCoordinators = 0)
    {
        $this->eventID = $eventID;
        $this->eventDate = $eventDate;
        $this->eventLocation = $eventLocation ?? new Address(0, '', 0, ''); // Initialize as an Address object
        $this->eventName = $eventName;
        $this->eventDescription = $eventDescription;
        $this->reqCooks = $reqCooks;
        $this->reqForDelivery = $reqForDelivery;
        $this->reqCoordinators = $reqCoordinators;
    }

    // Getter and Setter methods for all properties
    public function getEventID(): int { return $this->eventID; }
    public function setEventID(int $eventID): void { $this->eventID = $eventID; }

    public function getEventDate(): string { return $this->eventDate; }
    public function setEventDate(string $eventDate): void { $this->eventDate = $eventDate; }

    public function getEventLocation(): Address { return $this->eventLocation; }
    public function setEventLocation(Address $eventLocation): void { $this->eventLocation = $eventLocation; }

    public function getEventName(): string { return $this->eventName; }
    public function setEventName(string $eventName): void { $this->eventName = $eventName; }

    public function getEventDescription(): string { return $this->eventDescription; }
    public function setEventDescription(string $eventDescription): void { $this->eventDescription = $eventDescription; }

    public function getReqCooks(): int { return $this->reqCooks; }
    public function setReqCooks(int $reqCooks): void { $this->reqCooks = $reqCooks; }

    public function getReqForDelivery(): int { return $this->reqForDelivery; }
    public function setReqForDelivery(int $reqForDelivery): void { $this->reqForDelivery = $reqForDelivery; }

    public function getReqCoordinators(): int { return $this->reqCoordinators; }
    public function setReqCoordinators(int $reqCoordinators): void { $this->reqCoordinators = $reqCoordinators; }

    // Method to create a new event
    public function create(): bool
    {
        $query = "INSERT INTO Event (eventID, name, eventDate, eventLocation, eventDescription) 
                  VALUES ({$this->eventID}, '{$this->eventName}', '{$this->eventDate}', {$this->eventLocation->getId()}, '{$this->eventDescription}')";
        return run_query($query, true);
    }

    // Method to delete an event
    public function delete(): bool
    {
        $query = "DELETE FROM Event WHERE eventID = {$this->eventID}";
        return run_query($query, true);
    }

    // Method to update an existing event
    public function update(): bool
    {
        $query = "UPDATE Event 
                  SET name = '{$this->eventName}', eventDate = '{$this->eventDate}', eventLocation = {$this->eventLocation->getId()}, eventDescription = '{$this->eventDescription}' 
                  WHERE eventID = {$this->eventID}";
        return run_query($query, true);
    }

    // Method to read (fetch) an event by its ID
    public function read(): ?Event
    {
        $query = "SELECT * FROM Event WHERE eventID = {$this->eventID}";
        $result = run_select_query($query);

        if ($result && count($result) > 0) {
            $eventData = $result[0];

            // Load the Address object for event location
            $eventLocation = Address::read($eventData['eventLocation']);
            return new Event($eventData['eventID'], $eventData['eventDate'], $eventLocation, $eventData['name'], $eventData['eventDescription']);
        }

        return null;
    }
}