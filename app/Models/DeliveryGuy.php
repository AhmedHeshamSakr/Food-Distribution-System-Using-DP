<?php

require_once 'Volunteer.php';
require_once 'Vehicle.php';
require_once 'Delivery.php';

class DeliveryGuy extends Volunteer
{
    private Vehicle $vehicleType;
    private array $deliveryList = [];
    
    public function __construct(
        Vehicle $vehicleType,
        int $userTypeID, 
        string $firstName, 
        string $lastName, 
        string $email, 
        string $phoneNo, 
        iLogin $login, 
        Address $address, 
        string $nationalID,
        Badge $badge)
    {
        parent::__construct( $userTypeID, 
         $firstName, 
         $lastName, 
         $email, 
         $phoneNo, 
         $login, 
         $address, 
         $nationalID,
         $badge );
        $this->vehicleType = $vehicleType;
        $this->deliveryList = [];
        $this->insertDeliveryGuy($vehicleType);

    }
    public function insertDeliveryGuy(Vehicle $vehicleType): bool
    {
        $userid = $this->getUserID();
        $vehicleID = $this->vehicleType->getVehicleID();
        $query = "INSERT INTO deliveryguy (userID, vehicleID) 
                VALUES ('{$userid}', '{$vehicleID}')";

        // Run the query and return whether it was successful
        return run_query($query);

    }

    public function updateDeliveryGuy(array $fieldsToUpdate): bool
    {
        // Create an array to hold the SET part of the SQL query
        $setQuery = [];
    
        // Loop through the fieldsToUpdate array and create the SET portion of the query
        foreach ($fieldsToUpdate as $field => $value) {
            // Escape the value to prevent SQL injection
            $escapedValue = mysqli_real_escape_string(Database::getInstance()->getConnection(), $value);
            $setQuery[] = "$field = '$escapedValue'";
        }
    
        // Join the setQuery array into a string with commas
        $setQueryStr = implode(', ', $setQuery);
    
        // Construct the full SQL query
        $query = "UPDATE deliveryguy SET $setQueryStr WHERE userID = '{$this->getUserID()}'";
    
        // Run the query and return the result
        return run_query($query);
    }

    public function deleteDeliveryGuy(): bool
    {
        $query = "DELETE FROM deliveryguy WHERE userID = '{$this->getUserID()}'";
        return run_query($query);
    }

    public function getVehicleType(): Vehicle
    {
        return $this->vehicleType;
    }

    // Setter for vehicleType
    public function setVehicleType(Vehicle $vehicleType): void
    {
        $this->vehicleType = $vehicleType;

        // Optionally update in database if needed (you could also add a method to update vehicle information in DB)
        $fieldsToUpdate = [
            'vehicleID' => $this->vehicleType->getVehicleID()
        ];

        $this->updateDeliveryGuy($fieldsToUpdate);
    }

    public function addToDeliveryList(Delivery $delivery): bool
{
    // Get the deliveryID and deliveryGuyID
    $deliveryID = $delivery->getDeliveryID();
    $deliveryGuyID = $this->getUserID();

    // Create the SQL query to insert the new record into the Delivering table
    $query = "INSERT INTO Delivering (deliveryGuyID, deliveryID) 
              VALUES ('{$deliveryGuyID}', '{$deliveryID}')";

    // Execute the query to insert into the database
    $querySuccess = run_query($query);

    // If the query was successful, append the delivery to the deliveryList
    if ($querySuccess) {
        $this->deliveryList[] = $delivery;
        return true;  // Successfully added the delivery
    }

    return false;  // Failed to insert into the database
}


    // Remove a delivery from the delivery list
    public function removeFromDeliveryList(Delivery $delivery): bool
{
    // Get the deliveryID and deliveryGuyID
    $deliveryID = $delivery->getDeliveryID();
    $deliveryGuyID = $this->getUserID();

    // Create the SQL query to delete the record from the Delivering table
    $query = "DELETE FROM Delivering WHERE deliveryGuyID = '{$deliveryGuyID}' AND deliveryID = '{$deliveryID}'";

    // Execute the query to delete from the database
    $querySuccess = run_query($query);

    // If the query was successful, remove the delivery from the deliveryList
    if ($querySuccess) {
        // Remove the delivery from the deliveryList
        foreach ($this->deliveryList as $key => $del) {
            if ($del->getDeliveryID() === $deliveryID) {
                unset($this->deliveryList[$key]);  // Remove the delivery from the list
                return true;  // Successfully removed the delivery
            }
        }
    }

    return false;  // Failed to delete from the database or delivery not found in the list
}


    // Get all deliveries assigned to this delivery guy
    public function getDeliveryList(): array
{
    $query = "SELECT * FROM Delivery
              INNER JOIN Delivering ON Delivery.deliveryID = Delivering.deliveryID
              WHERE Delivering.deliveryGuyID = '{$this->getUserID()}'";
    
    // Use run_select_query for SELECT queries
    $result = run_select_query($query);
    
    // Check if result is valid (not false)
    if ($result === false) {
        // Handle the case where the query fails (optional)
        return [];
    }

    // Process the result set into an array of Delivery objects
    $deliveries = [];
    foreach ($result as $row) {
        $deliveries[] = new Delivery(
            $row['deliveryID'],
            $row['deliveryDate'],
            $row['startLocation'],
            $row['endLocation'],
            $row['deliveryGuy']
        );
    }
    
    return $deliveries;
}





}