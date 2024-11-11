<?php

require_once 'Delivery.php';
require_once 'DeliveryGuy.php';
require_once '../../config/DB.php';

class Delivering
{
    private DeliveryGuy $deliveryGuy; // Reference to the DeliveryGuy object
    private Delivery $delivery; // Reference to the Delivery object
    private string $deliveryTime; // Store as string for time representation (HH:MM:SS)

    // Constructor to initialize the Delivering object
    public function __construct(DeliveryGuy $deliveryGuy, Delivery $delivery, string $deliveryTime)
    {
        $this->deliveryGuy = $deliveryGuy;
        $this->delivery = $delivery;
        $this->deliveryTime = $deliveryTime;
    }

    // Getter and Setter for DeliveryGuy
    public function getDeliveryGuy(): DeliveryGuy
    {
        return $this->deliveryGuy;
    }

    public function setDeliveryGuy(DeliveryGuy $deliveryGuy): void
    {
        $this->deliveryGuy = $deliveryGuy;
    }

    // Getter and Setter for Delivery
    public function getDelivery(): Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(Delivery $delivery): void
    {
        $this->delivery = $delivery;
    }

    // Getter and Setter for DeliveryTime
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(string $deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    // Insert a new delivery record into the Delivering table
    public function insertDelivering(): bool
    {
        $query = "INSERT INTO Delivering (deliveryGuyID, deliveryID, deliveryTime) 
                  VALUES ('{$this->deliveryGuy->getUserID()}', '{$this->delivery->getDeliveryID()}', '{$this->deliveryTime}')";
        
        return run_query($query); // Assumes run_query() handles the query execution
    }

    // Update an existing delivery record in the Delivering table
    public function updateDelivering(): bool
    {
        $query = "UPDATE Delivering 
                  SET deliveryTime = '{$this->deliveryTime}' 
                  WHERE deliveryGuyID = '{$this->deliveryGuy->getUserID()}' AND deliveryID = '{$this->delivery->getDeliveryID()}'";
        
        return run_query($query); // Assumes run_query() handles the query execution
    }

    // Delete a delivery record from the Delivering table
    public function deleteDelivering(): bool
    {
        $query = "DELETE FROM Delivering 
                  WHERE deliveryGuyID = '{$this->deliveryGuy->getUserID()}' AND deliveryID = '{$this->delivery->getDeliveryID()}'";
        
        return run_query($query); // Assumes run_query() handles the query execution
    }

    // Static function to read a Delivering record by deliveryGuy and delivery
   
}    
