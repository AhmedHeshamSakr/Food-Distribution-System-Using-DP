<?php

require_once '../../config/DB.php';

class Delivery
{
    private int $deliveryID;
    private string $deliveryDate;
    private string $startLocation;
    private string $endLocation;
    private int $deliveryGuyID;
    private string $status;
    private ?string $deliveryDetails;

    public function __construct(string $deliveryDate, string $startLocation, string $endLocation, int $deliveryGuyID, string $status = 'pending', ?string $deliveryDetails = null)
    {
       
        $this->deliveryDate = $deliveryDate;
        $this->startLocation = $startLocation;
        $this->endLocation = $endLocation;
        $this->deliveryGuyID = $deliveryGuyID;
        $this->status = $status;
        $this->deliveryDetails = $deliveryDetails; // Initialize deliveryDetails
        $this -> insertDelivery($deliveryDate, $startLocation, $endLocation, $deliveryGuyID, $status, $deliveryDetails);
    }
    

    // Getter and Setter Methods
    public function getDeliveryID(): int
    {
        return $this->deliveryID;
    }



    public function getDeliveryDate(): string
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(string $deliveryDate): void
    {
        $this->deliveryDate = $deliveryDate;
        $this->updateDelivery(['deliveryDate' => $deliveryDate]); // Automatically update
    }

    public function getStartLocation(): string
    {
        return $this->startLocation;
    }

    public function setStartLocation(string $startLocation): void
    {
        $this->startLocation = $startLocation;
        $this->updateDelivery(['startLocation' => $startLocation]); // Automatically update
    }

    public function getEndLocation(): string
    {
        return $this->endLocation;
    }

    public function setEndLocation(string $endLocation): void
    {
        $this->endLocation = $endLocation;
        $this->updateDelivery(['endLocation' => $endLocation]); // Automatically update
    }

    public function getDeliveryGuy(): int
    {
        return $this->deliveryGuyID;
    }

    // public function setDeliveryGuy(DeliveryGuy $deliveryGuy): void
    // {
    //     $this->deliveryGuy = $deliveryGuy;
    //     $this->updateDelivery(['deliveryGuy' => $deliveryGuy]); // Automatically update
    // }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $validStatuses = ['pending', 'delivering', 'delivered'];
        if (in_array($status, $validStatuses)) {
            $this->status = $status;
            $this->updateDelivery(['status' => $status]); // Automatically update status
        }
    }

    public function getDeliveryDetails(): ?string
    {
        return $this->deliveryDetails;
    }

    public function setDeliveryDetails(?string $deliveryDetails): void
    {
        $this->deliveryDetails = $deliveryDetails;
        $this->updateDelivery(['deliveryDetails' => $deliveryDetails]); // Automatically update
    }

    // Insert a new delivery
    public function insertDelivery(string $deliveryDate, string $startLocation, string $endLocation, int $deliveryGuyID, string $status = 'pending', ?string $deliveryDetails = null): bool
    {
        // Sanitize inputs to prevent SQL injection
        $deliveryDate = mysqli_real_escape_string(Database::getInstance()->getConnection(), $deliveryDate);
        $startLocation = mysqli_real_escape_string(Database::getInstance()->getConnection(), $startLocation);
        $endLocation = mysqli_real_escape_string(Database::getInstance()->getConnection(), $endLocation);
        $deliveryGuy = mysqli_real_escape_string(Database::getInstance()->getConnection(), $deliveryGuyID);
        $status = mysqli_real_escape_string(Database::getInstance()->getConnection(), $status);
        $deliveryDetails = mysqli_real_escape_string(Database::getInstance()->getConnection(), $deliveryDetails);

        // SQL query to insert the delivery into the database
        $query = "INSERT INTO Delivery (deliveryDate, startLocation, endLocation, deliveryGuy, status, deliveryDetails) 
                  VALUES ('{$deliveryDate}', '{$startLocation}', '{$endLocation}', '{$deliveryGuy}', '{$status}', '{$deliveryDetails}')";

        // Run the query and return whether it was successful
        $result = run_query($query);

        if ($result) {
            // Set the deliveryID to the last inserted ID
            $this->deliveryID = mysqli_insert_id(Database::getInstance()->getConnection());
            return true;
        }
        return false;
    }

    // Update delivery with dynamic fields
    public function updateDelivery(array $fieldsToUpdate): bool
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
        $query = "UPDATE Delivery SET $setQueryStr WHERE deliveryID = '{$this->deliveryID}'";

        // Run the query and return the result
        return run_query($query);
    }

    // Delete the delivery
    public function deleteDelivery(): bool
    {
        $query = "DELETE FROM Delivery WHERE deliveryID = '{$this->deliveryID}'";
        return run_query($query);
    }

    
}
?>
