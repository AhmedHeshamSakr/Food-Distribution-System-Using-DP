<?php

require_once __DIR__ . "/../../config/DB.php";
require_once 'Address.php';
require_once 'Iterator.php';

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
    private ?Address $eventLocation;
    private string $eventName;
    private string $eventDescription;
    private int $reqCooks;
    private int $reqForDelivery;
    private int $reqCoordinators;
    private array $observers = [];

    public function __construct(
        ?int $eventID = 0, 
        string $eventDate = '', 
        ?Address $eventLocation = null, 
        string $eventName = '', 
        string $eventDescription = '', 
        int $reqCooks = 0, 
        int $reqForDelivery = 0, 
        int $reqCoordinators = 0
    ) {
        if ($eventLocation !== null && !$eventLocation->getLevel()) {
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

    // Getter and Setter methods for all properties
    public function getEventID(): int
    {
        return $this->eventID;
    }
    public function setEventID(int $eventID): void
    {
        $this->eventID = $eventID;
    }

    public function getEventDate(): string
    {
        return $this->eventDate;
    }
    public function setEventDate(string $eventDate): void
    {
        $this->eventDate = $eventDate;
    }

    public function getEventLocation(): Address { return $this->eventLocation; }
    public function setEventLocation(Address $location): void { $this->eventLocation = $location; }

    public function getEventName(): string { return $this->eventName; }
    public function setEventName(string $eventName): void { $this->eventName = $eventName; }

    public function getEventDescription(): string
    {
        return $this->eventDescription;
    }
    
    public function setEventDescription(string $eventDescription): void
    {
        $this->eventDescription = $eventDescription;
    }

    public function getReqCooks(): int
    {
        return $this->reqCooks;
    }
    public function setReqCooks(int $reqCooks): void
    {
        $this->reqCooks = $reqCooks;
    }

    public function getReqForDelivery(): int
    {
        return $this->reqForDelivery;
    }
    public function setReqForDelivery(int $reqForDelivery): void
    {
        $this->reqForDelivery = $reqForDelivery;
    }

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

    // Fetch all events from the database
    public static function fetchAll(): array
    {
        // Assuming `run_select_query` returns an array of rows
        $query = "SELECT * FROM `event`";
        $results = run_select_query($query);
        $events = [];
        // Loop through each result row
        foreach ($results as $row) {
            // Ensure $row is an array and $row['eventLocation'] is the correct data for Address
            if (isset($row['eventLocation'])) {
                $eventLocation = Address::read($row['eventLocation']);
            } else {
                // Handle the case if eventLocation is not found or is invalid
                $eventLocation = null;  // Adjust accordingly
            }
            // Ensure required fields exist in $row before using them
            if (isset($row['eventID'], $row['eventDate'], $row['name'], $row['eventDescription'],$row['reqCooks'], $row['reqForDelivery'], $row['reqCoordinators'])) {
                // Create the Event object and add to the events array
                $events[] = new Event(
                    (int)$row['eventID'],
                    $row['eventDate'],
                    $eventLocation,
                    $row['name'],
                    $row['eventDescription'],
                    $row['reqCooks'],
                    $row['reqForDelivery'],
                    $row['reqCoordinators']
                );
            } else {
                // Log an error or handle missing fields gracefully
                error_log("Missing required fields in event data: " . json_encode($row));
            }
        }
    
        return $events;
    }
    
    // Fetch a single event by its ID
   

    public function create(): bool
    {
        if ($this->eventLocation->getId() === 0) {
            $this->eventLocation->create();
        }
    
        $locationID = $this->eventLocation->getId();
        $sql = "INSERT INTO `event` (eventDate, eventLocation, `name`, eventDescription, reqCooks, reqForDelivery, reqCoordinators) 
                VALUES ('{$this->eventDate}', {$locationID}, '{$this->eventName}', '{$this->eventDescription}', {$this->reqCooks}, {$this->reqForDelivery}, {$this->reqCoordinators})";
    
        $result = run_query($sql);
        if ($result) {
            $this->eventID = Database::getInstance()->get_last_inserted_id();
        }
        return $result;
    }

        
    public static function fetchById(int $eventID): ?Event
{
    if ($eventID <= 0) {
        throw new Exception("Invalid Event ID.");
    }

    $sql = "SELECT e.eventID, e.eventDate, e.name AS eventName, e.eventDescription,e.reqCooks, e.reqForDelivery, e.reqCoordinators,
    a.id AS address_id, a.name AS address_name, a.parent_id, a.level
    FROM `event` e
    LEFT JOIN address a ON e.eventLocation = a.id
    WHERE e.eventID = " . intval($eventID);

    $result = run_select_query($sql);

    if (!$result) {
        error_log("No results found for event ID: $eventID");
        return null;
    }

    if (empty($result[0])) {
        error_log("Empty result row for event ID: $eventID");
        return null;
    }

    $row = $result[0];

    // Verify we have all required address fields
    $requiredAddressFields = ['address_id', 'address_name', 'parent_id', 'level'];
    foreach ($requiredAddressFields as $field) {
        if (!isset($row[$field])) {
            error_log("Missing required address field '$field' for event ID: $eventID");
            return null;
        }
    }

    try {
        if (!isset($row['address_id'], $row['address_name'], $row['level'])) {
            throw new Exception("Missing required address fields");
        }
        
        $address = new Address(
            $row['address_name'],
            (int)($row['parent_id'] ?? 0),
            $row['level']
        );
        $address->setId((int)$row['address_id']);
        
        // Create and return Event object
        return new Event(
            (int)$row['eventID'],
            $row['eventDate'],
            $address,
            $row['eventName'],
            $row['eventDescription'],
            (int)($row['reqCooks'] ?? 0),
            (int)($row['reqForDelivery'] ?? 0),
            (int)($row['reqCoordinators'] ?? 0)
        );
    } catch (Exception $e) {
        error_log("Error creating Event/Address object for event ID $eventID: " . $e->getMessage());
        throw $e;
    }
}
    public function read(): ?Event
    {
    if ($this->eventID === null || $this->eventID <= 0) {
        throw new Exception("Event ID is not set.");
    }
    
    return self::fetchById($this->eventID); 
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
                SET eventDate = '{$this->eventDate}', 
                    eventLocation = {$locationID}, 
                    `name` = '{$this->eventName}', 
                    eventDescription = '{$this->eventDescription}',
                    reqCooks = {$this->reqCooks},
                    reqForDelivery = {$this->reqForDelivery},
                    reqCoordinators = {$this->reqCoordinators}
                WHERE eventID = {$this->eventID}";
    
        if (!run_query($sql)) {
            error_log("Update error: " . mysqli_error(Database::getInstance()->getConnection()));
            return false;
        }
        return true;
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM `event` WHERE eventID = {$this->eventID}";
        return run_query($sql);
    }

    public static function getAllEvents(): EventList
    {
        $eventList = new EventList();
        // Get the database connection
        $connection = Database::getInstance()->getConnection();
        if (!$connection) {
            die("Database connection failed: " . mysqli_connect_error());
        }
        // SQL query to select all events
        $query = "SELECT * FROM event";
        $result = mysqli_query($connection, $query);
        // Check if the query executed successfully
        if (!$result) {
            die("Query error: " . mysqli_error($connection));
        }
        // Check if the query returned any rows
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Safely handle eventLocation
                $eventLocation = null;
                if (!empty($row['eventLocation'])) {
                    try {
                        $eventLocation = Address::read($row['eventLocation']);
                    } catch (Exception $e) {
                        print("Error reading event location: " . $e->getMessage() . "\n");
                    }
                }

                // Validate required fields before creating an Event object
                if (isset($row['eventID'], $row['eventDate'], $row['name'], $row['eventDescription'])) {
                    $event = new Event(
                        (int)$row['eventID'], // Convert to integer for safety
                        $row['eventDate'],
                        $eventLocation,
                        $row['name'],
                        $row['eventDescription']
                    );
    
                    // Add the event to the event list
                    $eventList->addEvent($event);
                } else {
                    print("Missing required fields in event data: " . json_encode($row) . "\n");
                }
            }
        } else {
            print("No events found in the database.\n");
        }
    
        return $eventList;
    }
    




}
