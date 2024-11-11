<?php


require_once __DIR__ . "/../../config/DB.php";
require_once 'User.php';

class Donor extends User
{
    // Enum constants for payment methods
    private const PAYMENT_METHODS = [
        'Cash' => 'Cash',
        'CreditCard' => 'Credit Card',
        'BankTransfer' => 'Bank Transfer',
        'OnlinePayment' => 'Online Payment',
    ];

    public function __construct(int $userID, string $firstName, string $lastName, string $email, string $phoneNo, iLogin $login)
    {
        // Initialize the User class with userTypeID for 'donor'
        parent::__construct(self::USER_TYPE_ID_MAP['donor'], $firstName, $lastName, $email, $phoneNo, $login);
    }

    // Method to add a donation record
    public function addDonation(float $amount, string $paymentMethod): bool
    {
        // Validate payment method
        if (!in_array($paymentMethod, self::PAYMENT_METHODS)) {
            throw new InvalidArgumentException("Invalid payment method.");
        }

        // Insert the donation into the Donation table
        $query = "INSERT INTO Donation (donationDate, donationAmount, paymentMethod)
                  VALUES (CURDATE(), {$amount}, '{$paymentMethod}')";
        
        // Run the insert query and get the new donation ID
        $donationID = run_query($query, true); // Assuming run_query returns the new donation ID

        if ($donationID) {
            // Insert into the Donating table to record the many-to-many relationship
            $donatingQuery = "INSERT INTO Donating (userID, donationID) VALUES ({$this->getuserID()}, {$donationID})";
            return run_query($donatingQuery, true);
        }

        return false;
    }

    // Method to fetch donation history for the donor
    public function fetchDonationHistory(): array
    {
        $query = "SELECT d.donationID, d.donationDate, d.donationAmount, d.paymentMethod 
                  FROM Donation d
                  JOIN Donating don ON d.donationID = don.donationID
                  WHERE don.userID = {$this->getuserID()}";
        return run_select_query($query) ?: [];
    }

    // Method to update the donor's personal information
    public function updatePersonalInfo(string $firstName, string $lastName, string $email, string $phoneNo): bool
    {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setPhoneNo($phoneNo);

        // Update the Person table
        $query = "UPDATE Person 
                  SET firstName = '{$this->getFirstName()}', 
                      lastName = '{$this->getLastName()}', 
                      email = '{$this->getEmail()}', 
                      phoneNo = '{$this->getPhoneNo()}' 
                  WHERE userID = {$this->getuserID()}";
        return run_query($query, true);
    }

    // Method to update donation amount and payment method for a specific donation
    public function updateDonation(int $donationID, float $amount, string $paymentMethod): bool
    {
        // Validate payment method
        if (!in_array($paymentMethod, self::PAYMENT_METHODS)) {
            throw new InvalidArgumentException("Invalid payment method.");
        }

        // Update the donation record
        $query = "UPDATE Donation 
                  SET donationAmount = {$amount}, paymentMethod = '{$paymentMethod}' 
                  WHERE donationID = {$donationID}";
        return run_query($query, true);
    }

}