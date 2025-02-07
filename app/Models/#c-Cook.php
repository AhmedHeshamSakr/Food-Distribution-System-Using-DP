<?php
require_once 'Volunteer.php';
require_once '#c-Meals.php';
require_once '#c-Cooking.php';

class Cook extends VolunteerRoles
{

    private int $userTypeID= Person::COOK_FLAG; // 1
    public function __construct(Person $user)
    {
        // Initialize the VolunteerRoles with a User reference
        parent::__construct($user);
        // Assign the cook role by default
        //$this->chooseRole();
    }

    // Override chooseRole to assign the Cook flag
    // public function chooseRole(): bool
    // {
    //     $this->roleType |= self::COOK_FLAG; // Set Cook role flag
    //     return true;
    // }


    public function assignCookToEvent(int $eventID): bool {
        $db = Database::getInstance()->getConnection();
        $userID = $this->ref->getUserID();
    
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
    
        // Step 3: Assign the cook to the event
        $assignQuery = "INSERT INTO Cook (userID, eventID) VALUES (?, ?)";
        $stmt = $db->prepare($assignQuery);
        $stmt->bind_param('ii', $userID, $eventID);
    
        if ($stmt->execute()) {
            echo "Cook assigned to Event ID: $eventID\n";
            $stmt->close();
            return true;
        } else {
            echo "Error assigning cook to event: " . $stmt->error . "\n";
            $stmt->close();
            return false;
        }
    }
    
    public function getAssignedEvents(): array {
        $db = Database::getInstance()->getConnection();
        $userID = $this->ref->getUserID();
    
        $query = "
            SELECT e.eventID, e.name, e.eventDate, e.eventDescription 
            FROM event e 
            JOIN Cook c ON e.eventID = c.eventID 
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
    
    public function chooseRole(): bool {
        
        // Get the current userTypeID, then apply the Cook flag using the setter
        $currentType = $this->ref->getUserTypeID(); //0 from user
        //echo 'cook: current cook type is'. $currentType . '</br>';
        $this->setUserTypeID($currentType | Person::COOK_FLAG); // Access the constant in User
        //echo 'cook: current cook type is'. $this->getUserTypeID() . '</br>'; // shlold be 1
        return true;
    }

    // Take meals to cook
    public function takeMeals(int $mealID, int $count): bool
    {
        // Check if meals are available to take
        $mealsNeeded = Cooking::getMealsNeeded($mealID);
        if ($mealsNeeded < $count) {
            echo "Not enough meals available to take.\n";
            return false;  // Can't take more meals than are needed
        }

        // Create a Cooking instance to handle meal assignment
        $cooking = new Cooking($this->ref->getUserID(), $mealID);

        // Try to take the specified number of meals
        if ($cooking->takeMeals($count)) {
            echo "$count meals assigned to cook.\n";
            return true;
        } else {
            echo "Failed to assign meals to cook.\n";
            return false;
        }
    }

    // Complete meals that were taken
    public function completeMeals(int $mealID, int $count): bool
    {
        // Ensure cook has taken meals before completing them
        $cooking = new Cooking($this->ref->getUserID(), $mealID);
        $mealsTaken = $cooking->getMealsTaken();

        if ($mealsTaken < $count) {
            echo "Cook has not taken enough meals to complete this many.\n";
            return false;  // Can't complete more meals than the cook has taken
        }

        // Mark meals as completed
        if ($cooking->completeMeals($count)) {
            echo "$count meals completed by cook.\n";
            return true;
        } else {
            echo "Failed to complete meals.\n";
            return false;
        }
    }

    // Get all meals assigned to this cook
    public function getMealsAssigned(): array
    {
        return Cooking::getMealsByCook($this->ref->getUserID());
    }

    // Get the list of all roles assigned to this cook
    public function getRoles(): array
    {
        return $this->getAllRoles();
    }
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
        //echo 'the new user type id is '.$this->userTypeID . '</br>';

        $gottenvalue = $this->getUserTypeID();
        //echo 'the gotten value (COOOOOOOOOOOOK) is '.$gottenvalue . '</br>';
        return $this->updatePerson($fieldsToUpdate); 
    }
}


