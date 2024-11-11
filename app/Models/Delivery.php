<?php

require_once 'Database.php';

class Delivery
{
    private int $deliveryID;
    private string $deliveryDate;
    private string $startLocation;
    private string $endLocation;
    private int $deliveryGuy;

    public function __construct(int $deliveryID, string $deliveryDate, string $startLocation, string $endLocation, int $deliveryGuy)
    {
        $this->deliveryID = $deliveryID;
        $this->deliveryDate = $deliveryDate;
        $this->startLocation = $startLocation;
        $this->endLocation = $endLocation;
        $this->deliveryGuy = $deliveryGuy;
        $this->insertDelivery($deliveryDate, $startLocation, $endLocation, $deliveryGuy);
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
        return $this->deliveryGuy;
    }

    public function setDeliveryGuy(int $deliveryGuy): void
    {
        $this->deliveryGuy = $deliveryGuy;
        $this->updateDelivery(['deliveryGuy' => $deliveryGuy]); // Automatically update
    }

    // Insert a new delivery
    public function insertDelivery(string $deliveryDate, string $startLocation, string $endLocation, int $deliveryGuy): bool
    {
        // Sanitize inputs to prevent SQL injection
        $deliveryDate = mysqli_real_escape_string(Database::getInstance()->getConnection(), $deliveryDate);
        $startLocation = mysqli_real_escape_string(Database::getInstance()->getConnection(), $startLocation);
        $endLocation = mysqli_real_escape_string(Database::getInstance()->getConnection(), $endLocation);
        $deliveryGuy = mysqli_real_escape_string(Database::getInstance()->getConnection(), $deliveryGuy);

        // SQL query to insert the delivery into the database
        $query = "INSERT INTO Delivery (deliveryDate, startLocation, endLocation, deliveryGuy) 
                  VALUES ('{$deliveryDate}', '{$startLocation}', '{$endLocation}', '{$deliveryGuy}')";

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
