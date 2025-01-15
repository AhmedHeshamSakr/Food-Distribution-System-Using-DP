<?php

require_once __DIR__ . "/../../config/DB.php";
require_once 'Address.php';

interface Observer
{
    public function update(string $message): void;
}

interface Subject 
{
    public function addObserver(Observer $observer): void;
    public function removeObserver(Observer $observer): void;
    public function notifyObservers(string $message): void;
}

class Event implements Subject
{
    private ?int $eventID;
    private string $eventDate;
    private Address $eventLocation;
    private string $eventName;
    private string $eventDescription;
    private int $reqCooks;
    private int $reqForDelivery;
    private int $reqCoordinators;
    private array $observers = [];

    public function __construct(
        ?int $eventID = 0, 
        string $eventDate = '', 
        Address $eventLocation = null, 
        string $eventName = '', 
        string $eventDescription = '', 
        int $reqCooks = 0, 
        int $reqForDelivery = 0, 
        int $reqCoordinators = 0
    ) {
        if (!$eventLocation || !$eventLocation->getLevel()) {
            throw new Exception("Invalid Address passed to Event.");
        }

        $this->eventID = $eventID;
        $this->eventDate = $eventDate;
        $this->eventLocation = $eventLocation;
        $this->eventName = $eventName;
        $this->eventDescription = $eventDescription;
        $this->reqCooks = $reqCooks;
        $this->reqForDelivery = $reqForDelivery;
        $this->reqCoordinators = $reqCoordinators;
    }

    // Getters and Setters
    public function getEventID(): int { return $this->eventID; }
    public function setEventID(int $eventID): void { $this->eventID = $eventID; }

    public function getEventDate(): string { return $this->eventDate; }
    public function setEventDate(string $eventDate): void { $this->eventDate = $eventDate; }

    public function getEventLocation(): Address { return $this->eventLocation; }
    public function setEventLocation(Address $location): void { $this->eventLocation = $location; }

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

    // Observer pattern methods
    public function addObserver(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function removeObserver(Observer $observer): void
    {
        $this->observers = array_filter($this->observers, fn($obs) => $obs !== $observer);
    }

    public function notifyObservers(string $message): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($message);
        }
    }

    // Check if event requirements are fulfilled
    public function checkRequirements(): void
    {
        if ($this->reqCooks <= 0 && $this->reqForDelivery <= 0 && $this->reqCoordinators <= 0) {
            $this->notifyObservers("All requirements for event '{$this->eventName}' are fulfilled.");
        }
    }

    // Methods to assign staff
    public function assignCook(): void
    {
        if ($this->reqCooks > 0) {
            $this->reqCooks--;
        }
        $this->checkRequirements();
    }

    public function assignDelivery(): void
    {
        if ($this->reqForDelivery > 0) {
            $this->reqForDelivery--;
        }
        $this->checkRequirements();
    }

    public function assignCoordinator(): void
    {
        if ($this->reqCoordinators > 0) {
            $this->reqCoordinators--;
        }
        $this->checkRequirements();
    }

    // Database operations
    public function create(): bool
    {
        if ($this->eventLocation->getId() === 0) {
            $this->eventLocation->create();
        }

        $locationID = $this->eventLocation->getId();
        $sql = "INSERT INTO `event` (eventDate, eventLocation, `name`, eventDescription) 
                VALUES ('{$this->eventDate}', {$locationID}, '{$this->eventName}', '{$this->eventDescription}')";

        $result = run_query($sql);
        if ($result) {
            $this->eventID = Database::getInstance()->get_last_inserted_id();
        }
        return $result;
    }

    public function read(): ?Event
    {
        $sql = "SELECT e.*, a.* 
                FROM `event` e
                JOIN address a ON e.eventLocation = a.id
                WHERE e.eventID = {$this->eventID}";

        $result = run_select_query($sql);
        if ($result && !empty($result[0])) {
            $event = $result[0];
            $address = new Address($event['name'], $event['parent_id'], $event['level']);
            $address->setId((int)$event['eventLocation']);

            return new Event(
                (int)$event['eventID'],
                $event['eventDate'],
                $address,
                $event['name'],
                $event['eventDescription'],
                (int)($event['reqCooks'] ?? 0),
                (int)($event['reqForDelivery'] ?? 0),
                (int)($event['reqCoordinators'] ?? 0)
            );
        }

        return null;
    }

    public static function fetchAll(): array
    {
        $query = "SELECT * FROM `event`";
        $results = run_select_query($query);
        $events = [];

        foreach ($results as $row) {
            $eventLocation = Address::read((int)$row['eventLocation']) ?? new Address('Unknown', null, 'Unknown');
            $events[] = new Event(
                (int)$row['eventID'],
                $row['eventDate'],
                $eventLocation,
                $row['name'],
                $row['eventDescription'],
                (int)($row['reqCooks'] ?? 0),
                (int)($row['reqForDelivery'] ?? 0),
                (int)($row['reqCoordinators'] ?? 0)
            );
        }

        return $events;
    }

    public static function fetchUpcomingEvents(): array
    {
        $today = date('Y-m-d');
        $query = "SELECT * FROM `event` WHERE eventDate > '$today'";
        $results = run_select_query($query);
        $upcomingEvents = [];

        foreach ($results as $row) {
            if (isset($row['eventID'], $row['eventDate'], $row['name'], $row['eventDescription'], $row['eventLocation'])) {
                $eventLocation = Address::read($row['eventLocation']) ?? new Address('Unknown', 0, 'Unknown');
                $upcomingEvents[] = new Event(
                    (int)$row['eventID'],
                    $row['eventDate'],
                    $eventLocation,
                    $row['name'],
                    $row['eventDescription'],
                    (int)($row['reqCooks'] ?? 0),
                    (int)($row['reqForDelivery'] ?? 0),
                    (int)($row['reqCoordinators'] ?? 0)
                );
            }
        }

        return $upcomingEvents;
    }

    public function update(): bool
    {
        $locationID = $this->eventLocation->getId();
        $sql = "UPDATE `event` 
                SET eventDate = '{$this->eventDate}', eventLocation = {$locationID}, `name` = '{$this->eventName}', eventDescription = '{$this->eventDescription}' 
                WHERE eventID = {$this->eventID}";
        return run_query($sql);
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM `event` WHERE eventID = {$this->eventID}";
        return run_query($sql);
    }
}
