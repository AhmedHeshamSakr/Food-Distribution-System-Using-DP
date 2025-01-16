<?php

require_once 'Volunteer.php';
require_once 'Vehicle.php';
require_once 'Delivery.php';
require_once 'Delivering.php';

class DeliveryGuy extends VolunteerRoles
{
    //private User $user; //howa howa el ref fo2 khalas

    private Vehicle $vehicleType;
    private int $userTypeID= Person::DELIVERY_FLAG; // 2
    private array $deliveryList = [];
    
    public function __construct(
        Person $user,    
        Vehicle $vehicleType
    ) {
        parent::__construct($user);   
        $this->vehicleType = $vehicleType;
        $this->deliveryList = [];
        //$this->chooseRole();
        $this->insertDeliveryGuy();
        

    }

    
    public function getUserID(): int {
        return $this->ref->getUserID();
    }

    // Adds delivery management functionality
    public function insertDeliveryGuy(): bool {
        $userid = $this->ref->getUserID();
        $vehicleID = $this->vehicleType->getVehicleID();
        $query = "INSERT INTO deliveryguy (userID, vehicleID) 
                  VALUES ('{$userid}', '{$vehicleID}')";
        return run_query($query);
    }

    public function updateDeliveryGuy(array $fieldsToUpdate): bool {
        // Assuming we pass the correct vehicleID, but let's ensure it exists in the vehicle table
        if (isset($fieldsToUpdate['vehicleID'])) {
            $vehicleID = $fieldsToUpdate['vehicleID'];
            $vehicleCheckQuery = "SELECT 1 FROM vehicle WHERE vehicleID = '$vehicleID'";
            $result = mysqli_query(Database::getInstance()->getConnection(), $vehicleCheckQuery);
            
            if (mysqli_num_rows($result) == 0) {
                // Handle error if vehicleID does not exist in the vehicle table
                throw new Exception("Invalid vehicleID: $vehicleID does not exist.");
            }
        }
    
        // Continue with the update logic
        $setQuery = [];
        foreach ($fieldsToUpdate as $field => $value) {
            if ($field === 'vehicleType') {
                $field = 'vehicleID'; // Correct column name
            }
            $escapedValue = mysqli_real_escape_string(Database::getInstance()->getConnection(), $value);
            $setQuery[] = "$field = '$escapedValue'";
        }
        $setQueryStr = implode(', ', $setQuery);
        $query = "UPDATE deliveryguy SET $setQueryStr WHERE userID = '{$this->getUserID()}'";
        return run_query($query);
    }
    

    public function deleteDeliveryGuy(): bool {
        $query = "DELETE FROM deliveryguy WHERE userID = '{$this->getUserID()}'";
        return run_query($query);
    }

    public function addToDeliveryList(Delivery $delivery): bool {
        $deliveryID = $delivery->getDeliveryID();
        $deliveryGuyID = $this->getUserID();
        $query = "INSERT INTO Delivering (deliveryGuyID, deliveryID) VALUES ('{$deliveryGuyID}', '{$deliveryID}')";
        if (run_query($query)) {
            $this->deliveryList[] = $delivery;
            return true;
        }
        return false;
    }

    public function removeFromDeliveryList(Delivery $delivery): bool {
        $deliveryID = $delivery->getDeliveryID();
        $deliveryGuyID = $this->getUserID();
        $query = "DELETE FROM Delivering WHERE deliveryGuyID = '{$deliveryGuyID}' AND deliveryID = '{$deliveryID}'";
        if (run_query($query)) {
            foreach ($this->deliveryList as $key => $del) {
                if ($del->getDeliveryID() === $deliveryID) {
                    unset($this->deliveryList[$key]);
                    return true;
                }
            }
        }
        return false;
    }

    public function getDeliveryList(): array {
        // Query to select delivery details joined with Delivering to get the correct delivery guy
        $query = "SELECT * FROM Delivery 
                  INNER JOIN Delivering ON Delivery.deliveryID = Delivering.deliveryID 
                  WHERE Delivering.deliveryGuyID = '{$this->getUserID()}'";
        
        $result = run_select_query($query);
    
        if ($result === false) {
            return [];
        }
    
        $deliveries = [];
        foreach ($result as $row) {
            // Wrap the existing delivery data into Delivery objects
            $deliveries[] = new Delivery(
                $row['deliveryDate'],
                $row['startLocation'],
                $row['endLocation'],
                $row['deliveryGuy'],  
                $row['status'],
                $row['deliveryDetails']
            );
        }
    
        return $deliveries;
    }
    
    //function to retrieve history
    public function assignDelivery(Delivery $delivery, string $deliveryTime): bool {
        // Create a new Delivering instance and insert the record in the database
        $delivering = new Delivering($this, $delivery, $deliveryTime);
        if ($delivering->insertDelivering()) {
            $this->deliveryList[] = $delivery;
            return true;
        }
        return false;
    }

    public function unassignDelivery(Delivery $delivery): bool {
        // Create a new Delivering instance for deletion purposes
        $delivering = new Delivering($this, $delivery, ""); // Time is irrelevant for deletion
        if ($delivering->deleteDelivering()) {
            foreach ($this->deliveryList as $key => $del) {
                if ($del->getDeliveryID() === $delivery->getDeliveryID()) {
                    unset($this->deliveryList[$key]);
                    return true;
                }
            }
        }
        return false;
    }
    public function getUserTypeID(): int
    {
        return $this->userTypeID;
    }

    public function chooseRole(): bool {
        
        // Get the current userTypeID, then apply the Cook flag using the setter
        $currentType = $this->ref->getUserTypeID(); // 1 from cook
        //echo 'delivery: the following is the current type'. $currentType . '</br>';
        $this->setUserTypeID($currentType | Person::DELIVERY_FLAG); // Access the constant in User
        //echo 'delivery: the following the the current delivery type'. $this->getUserTypeID() . '</br>'; // should be 3
        return true;
    }


    public function setUserTypeID(int $userTypeID): bool
    {
        $this->userTypeID = $userTypeID;
        $fieldsToUpdate = [
            'userTypeID' => $this->userTypeID
        ];
        //echo 'delivery: the new user type id is '.$this->userTypeID . '</br>';

        $gottenvalue = $this->getUserTypeID();
        //echo 'the gotten value is '.$gottenvalue . '</br>';
        return $this->updatePerson($fieldsToUpdate); 
    }
    

}