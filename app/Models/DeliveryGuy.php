<?php

require_once 'Volunteer.php';
require_once 'Vehicle.php';
require_once 'Delivery.php';

class DeliveryGuy extends VolunteerRoles
{
    //private User $user; //howa howa el ref fo2 khalas

    private Vehicle $vehicleType;
    private array $deliveryList = [];
    
    public function __construct(
        User $user,    
        Vehicle $vehicleType
    ) {
        parent::__construct($user);   
        $this->vehicleType = $vehicleType;
        $this->deliveryList = [];
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
        $setQuery = [];
        foreach ($fieldsToUpdate as $field => $value) {
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
        $query = "SELECT * FROM Delivery INNER JOIN Delivering ON Delivery.deliveryID = Delivering.deliveryID WHERE Delivering.deliveryGuyID = '{$this->getUserID()}'";
        $result = run_select_query($query);
        if ($result === false) {
            return [];
        }
        $deliveries = [];
        foreach ($result as $row) {
            $deliveries[] = new Delivery($row['deliveryID'], $row['deliveryDate'], $row['startLocation'], $row['endLocation'], $row['deliveryGuy']);
        }
        return $deliveries;
    }
}
