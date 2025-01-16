<?php

require_once '../../config/DB.php';
require_once 'Iterator.php';
require_once 'DeliveryState.php';

class Delivery
{
    private int     $deliveryID;
    private string  $deliveryDate;
    private string  $startLocation;
    private string  $endLocation;
    private int     $deliveryGuyID;
    private string   $status = 'pending';
    private ?string $deliveryDetails;
    private State $state;

    public function __construct(string $deliveryDate, string $startLocation, string $endLocation, int $deliveryGuyID, string $status = 'pending', ?string $deliveryDetails = null)
    {

        $this->state = match($status) {
            'pending' => new PendingState(),
            'delivering' => new DeliveringState(),
            'delivered' => new DeliveredState(),
            default => new PendingState()
        };

        $this->deliveryDate = $deliveryDate;
        $this->startLocation = $startLocation;
        $this->endLocation = $endLocation;
        $this->deliveryGuyID = $deliveryGuyID;
        $this->status = $status;
        $this->deliveryDetails = $deliveryDetails;
        // $this->insertDelivery($deliveryDate, $startLocation, $endLocation, $deliveryGuyID, $status, $deliveryDetails);
    }

     // Request method that delegates to state's handle method
     public function request(): void {
        $this->state->handle($this);
    }

    // Method to transition between states
    public function transitionTo(State $state): void {
        $this->state = $state;
    }

     // Get current state status for database
     public function getCurrentStatus(): string {
        return match(get_class($this->state)) {
            PendingState::class => 'pending',
            DeliveringState::class => 'delivering',
            DeliveredState::class => 'delivered',
            default => 'pending'
        };
    }

    // Getter for deliveryID
    public function getDeliveryID(): int
    {
        return $this->deliveryID;
    }

    // Getter for deliveryDate
    public function getDeliveryDate(): string
    {
        return $this->deliveryDate;
    }

    // Setter for deliveryDate with database update
    public function setDeliveryDate(string $deliveryDate): void
    {
        $this->deliveryDate = $deliveryDate;
        $this->updateDelivery(['deliveryDate' => $deliveryDate]);
    }

    // Getter for startLocation
    public function getStartLocation(): string
    {
        return $this->startLocation;
    }

    // Setter for startLocation with database update
    public function setStartLocation(string $startLocation): void
    {
        $this->startLocation = $startLocation;
        $this->updateDelivery(['startLocation' => $startLocation]);
    }

    // Getter for endLocation
    public function getEndLocation(): string
    {
        return $this->endLocation;
    }

    // Setter for endLocation with database update
    public function setEndLocation(string $endLocation): void
    {
        $this->endLocation = $endLocation;
        $this->updateDelivery(['endLocation' => $endLocation]);
    }

    // Getter for deliveryGuyID
    public function getDeliveryGuy(): int
    {
        return $this->deliveryGuyID;
    }

    // Getter for status
    public function getStatus(): string
    {
        return $this->status;
    }

    // Setter for status with database update
    public function setStatus(string $status): void
    {
        $validStatuses = ['pending', 'delivering', 'delivered'];
        if (in_array($status, $validStatuses)) {
            $this->status = $status;
            $this->updateDelivery(['status' => $status]);
        }
    }

    // Getter for deliveryDetails
    public function getDeliveryDetails(): ?string
    {
        return $this->deliveryDetails;
    }

    // Setter for deliveryDetails with database update
    public function setDeliveryDetails(?string $deliveryDetails): void
    {
        $this->deliveryDetails = $deliveryDetails;
        $this->updateDelivery(['deliveryDetails' => $deliveryDetails]);
    }

    // Insert a new delivery
    public function insertDelivery(Delivery $delivery){
        $connection = Database::getInstance()->getConnection();
        $query = "INSERT INTO delivery (deliveryDate, startLocation, endLocation, deliveryGuy, status, deliveryDetails) 
                  VALUES ('{$delivery->getDeliveryDate()}', '{$delivery->getStartLocation()}', '{$delivery->getEndLocation()}', '{$delivery->getDeliveryGuy()}', '{$delivery->getStatus()}', '{$delivery->getDeliveryDetails()}')";
        $result = run_query($query);
        if ($result) {
            $this->deliveryID = mysqli_insert_id($connection);
            return true;
        }
        return false;
    }
    // public function insertDelivery(string $deliveryDate, string $startLocation, string $endLocation, int $deliveryGuyID, string $status = 'pending', ?string $deliveryDetails = null): bool
    // {
    //     $connection = Database::getInstance()->getConnection();

    //     // Sanitize inputs
    //     $deliveryDate = mysqli_real_escape_string($connection, $deliveryDate);
    //     $startLocation = mysqli_real_escape_string($connection, $startLocation);
    //     $endLocation = mysqli_real_escape_string($connection, $endLocation);
    //     $deliveryGuyID = mysqli_real_escape_string($connection, $deliveryGuyID);
    //     $status = mysqli_real_escape_string($connection, $status);
    //     $deliveryDetails = mysqli_real_escape_string($connection, $deliveryDetails);

    //     $query = "INSERT INTO delivery (deliveryDate, startLocation, endLocation, deliveryGuy, status, deliveryDetails) 
    //               VALUES ('{$deliveryDate}', '{$startLocation}', '{$endLocation}', '{$deliveryGuyID}', '{$status}', '{$deliveryDetails}')";

    //     $result = run_query($query);

    //     if ($result) {
    //         $this->deliveryID = mysqli_insert_id($connection);
    //         return true;
    //     }
    //     return false;
    // }

    // Update delivery with dynamic fields
    public function updateDelivery(array $fieldsToUpdate): bool
    {
        $connection = Database::getInstance()->getConnection();
        $setQuery = [];

        foreach ($fieldsToUpdate as $field => $value) {
            $escapedValue = mysqli_real_escape_string($connection, $value);
            $setQuery[] = "$field = '$escapedValue'";
        }

        $setQueryStr = implode(', ', $setQuery);
        $query = "UPDATE delivery SET $setQueryStr WHERE deliveryID = '{$this->deliveryID}'";

        return run_query($query);
    }

    // Delete the delivery
    public function deleteDelivery(): bool
    {
        $query = "DELETE FROM delivery WHERE deliveryID = '{$this->deliveryID}'";
        return run_query($query);
    }



    public static function getAllDeliveries(): DeliveryList {
        $deliveryList = new DeliveryList();
        $connection = Database::getInstance()->getConnection();
        
        $query = "SELECT * FROM delivery";
        $result = mysqli_query($connection, $query);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $delivery = new Delivery(
                $row['deliveryDate'],
                $row['startLocation'],
                $row['endLocation'],
                $row['deliveryGuy'],
                $row['status'],
                $row['deliveryDetails']
            );
            $deliveryList->addDelivery($delivery);
        }
        
        return $deliveryList;
    }



}

?>