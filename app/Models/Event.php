<?php

require_once __DIR__ . "/../../config/DB.php";
require_once 'Address.php';
require_once 'Iterator.php';

interface Observer
{
    // Method that gets called when a notification is sent
    public function update(string $message): void;
}

interface Subject 
{
    // Method to add an observer
    public function addObserver(Observer $observer): void;

    // Method to remove an observer
    public function removeObserver(Observer $observer): void;

    // Method to notify all observers
    public function notifyObservers(string $message): void;
}

class Event implements Subject
{
    private ?int $eventID;
    private string $eventDate;
    private Address $eventLocation; // Address is now a required parameter
    private string $eventName;
    private string $eventDescription;
    private int $reqCooks;
    private int $reqForDelivery;
    private int $reqCoordinators;
    private array $observers = [];

    // Constructor
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
         // Ensure valid address is passed or set a default
         if (!$eventLocation || !$eventLocation->getLevel()) {
            throw new Exception("Invalid Address passed to Event.");
        }
        

        $this->eventID = $eventID;
        $this->eventDate = $eventDate;
        $this->eventLocation = $eventLocation ?? new Address('', 0, ''); // Default to an Address object if null
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

    public function setEventLocation(Address $location)
    {
        $this->eventLocation = $location;
    }
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

    // Implementing Subject methods
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
            if (isset($row['eventID'], $row['eventDate'], $row['name'], $row['eventDescription'])) {
                // Create the Event object and add to the events array
                $events[] = new Event(
                    (int)$row['eventID'],
                    $row['eventDate'],
                    $eventLocation,
                    $row['name'],
                    $row['eventDescription']
                );
            } else {
                // Log an error or handle missing fields gracefully
                error_log("Missing required fields in event data: " . json_encode($row));
            }
        }
    
        return $events;
    }
    
    // Fetch a single event by its ID
    public static function fetchById(int $eventID): ?Event
    {
        $query = "SELECT * FROM `event` WHERE eventID = {$eventID}";
        $result = run_select_query($query);

        if ($result) {
            $row = $result[0]; // Assuming one event will be returned

            // Ensure $row is an array and $row['eventLocation'] is the correct data for Address
            if (isset($row['eventLocation'])) {
                $eventLocation = Address::read($row['eventLocation']);
            } else {
                // Handle the case if eventLocation is not found or is invalid
                $eventLocation = null;  // Adjust accordingly
            }

            // Ensure required fields exist in $row before using them
            if (isset($row['eventID'], $row['eventDate'], $row['name'], $row['eventDescription'])) {
                // Create and return the Event object
                return new Event(
                    (int)$row['eventID'],
                    $row['eventDate'],
                    $eventLocation,
                    $row['name'],
                    $row['eventDescription']
                );
            } else {
                // Log an error or handle missing fields gracefully
                error_log("Missing required fields in event data: " . json_encode($row));
            }
        }

        return null;
    }

    public function create(): bool
    {
        // Ensure that the address has been created in the database and has a valid ID
        if ($this->eventLocation->getId() === 0) {
            $this->eventLocation->create();
        }
    
        // Get the address ID from the Address object
        $locationID = $this->eventLocation->getId();
    
        // SQL Query to insert the event (without setting eventID)
        $sql = "INSERT INTO `event` (eventDate, eventLocation, `name`, eventDescription) 
        VALUES ('{$this->eventDate}', {$locationID}, '{$this->eventName}', '{$this->eventDescription}')";

        // Execute the query
        $result = run_query($sql);
    
        // If the query was successful, retrieve the auto-generated eventID
        if ($result) {
            // Retrieve the last inserted eventID
            $this->eventID = Database::getInstance()->get_last_inserted_id();
        }
    
        return $result;
    }
    


    public function read(): ?Event
{
    $sql = "SELECT * FROM `event` WHERE eventID = {$this->eventID}";
    $result = run_select_query($sql);

    if ($result) {
        $event = $result[0];  // Assuming one event will be returned

        // Assuming eventLocation is a string and parent_id and level need to be set manually
        $locationName = $event['eventLocation'];  // Assuming it's just a name
        $parentId = null;  // Set parent ID based on your logic (e.g., fetch from another table)
        $level = 'City';  // Set the level based on your logic or default value

        return new Event(
            $event['eventID'],
            $event['eventDate'],
            new Address($locationName, $parentId, $level),  // Properly passing 3 arguments
            $event['name'],
            $event['eventDescription']
        );
    }

    return null;
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
