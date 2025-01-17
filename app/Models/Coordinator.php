<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'Volunteer.php';
require_once __DIR__ . "/../../config/DB.php"; // Assuming DB.php handles your database connection

class Coordinator extends VolunteerRoles
{
    private string $coordinatorID;
    private int $userTypeID= Person::COORDINATOR_FLAG;
    private DateTime $eventDate;
    private string $eventID;
    private Person $user;

    // Constructor accepts a User object
    public function __construct(Person $user)
    {
        parent::__construct($user);
        $this->chooseRole();
        $this->user = $user;
    }

    // Set Coordinator role
    public function chooseRole(): bool {
        
        // Get the current userTypeID, then apply the Cook flag using the setter
        $currentType = $this->ref->getUserTypeID();
        //echo 'coordinator: current coordinator type is'. $currentType . '</br>';
        $this->setUserTypeID($currentType | Person::COORDINATOR_FLAG); // Access the constant in User
        //echo 'coordinator: current coordinator type is'. $this->getUserTypeID() . '</br>';
        return true;
    }

    // Getter for Coordinator ID
    public function getCoordinatorID(): string{return $this->coordinatorID;}
    // Setter for Coordinator ID
    public function setCoordinatorID(string $coordinatorID): void{$this->coordinatorID = $coordinatorID;}
    // Getter for Event Date
    public function getEventDate(): DateTime{ return $this->eventDate;}
    // Setter for Event Date
    public function setEventDate(DateTime $eventDate): void{$this->eventDate = $eventDate;}
    // Getter for Event ID
    public function getEventID(): string{return $this->eventID;}
    // Setter for Event ID
    public function setEventID(string $eventID): void{$this->eventID = $eventID;}

    // Method to assign coordinator to an event
    public function assignCoordinatorToEvent(int $eventID): bool {
        $db = Database::getInstance()->getConnection();
        $userID = $this->user->getUserID();

        // Step 1: Check if the user exists in the volunteer table
        $volunteerCheckQuery = "SELECT userID FROM Volunteer WHERE userID = ?";
        $stmt = $db->prepare($volunteerCheckQuery);
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 0) {
            echo "Error: User ID $userID is not a volunteer.\n";
            $stmt->close();
            return false;
        }
        $stmt->close();

        // Step 2: Check if the event exists in the event table
        $eventCheckQuery = "SELECT eventID FROM event WHERE eventID = ?";
        $stmt = $db->prepare($eventCheckQuery);
        $stmt->bind_param('i', $eventID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            echo "Error: Event ID $eventID does not exist.\n";
            $stmt->close();
            return false;
        }
        $stmt->close();

        // Step 3: Assign the coordinator to the event
        $assignQuery = "INSERT INTO coordinating (userID, eventID) VALUES (?, ?)";
        $stmt = $db->prepare($assignQuery);
        $stmt->bind_param('ii', $userID, $eventID);

        if ($stmt->execute()) {
            echo "Coordinator assigned to Event ID: $eventID\n";
            $stmt->close();
            return true;
        } else {
            echo "Error assigning coordinator to event: " . $stmt->error . "\n";
            $stmt->close();
            return false;
        }
    }

    // Method to retrieve events assigned to the coordinator
    public function getAssignedEvents(): array
    {
        $db = Database::getInstance()->getConnection();
        $userID = $this->user->getUserID();

        $query = "
            SELECT e.eventID, e.name, e.eventDate, e.eventDescription 
            FROM event e 
            JOIN coordinating c ON e.eventID = c.eventID 
            WHERE c.userID = ?
        ";

        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }

        return $events;
    }

    // Get details of a specific event assigned to the coordinator
    public function getUserTypeID(): int
    {
        return $this->userTypeID;
    }
    public function setUserTypeID(int $userTypeID): bool
    {
        $this->userTypeID = $userTypeID;
        $fieldsToUpdate = [
            'userTypeID' => $this->userTypeID
        ];
        //echo 'the new user type id is '.$this->userTypeID;
        $gottenvalue = $this->getUserTypeID();
        //echo 'the gotten value (COORDINATORRRRRRRRRRRRRRRRRRRRRRRRRR) is '.$gottenvalue;
        return $this->updatePerson($fieldsToUpdate); 
    }
    
}