<?php

require_once 'User.php';
require_once 'Payment.php'; // Ensure this includes IPayment and strategy classes

class Donor extends Person
{
    private const PAYMENT_METHODS = ['Fawry', 'Credit Card', 'Visa'];
    private int $userTypeID = Person::DONOR_FLAG;

    public function __construct(int $userID, string $firstName, string $lastName, string $email, string $phoneNo, iLogin $login)
    {
        parent::__construct(self::DONOR_FLAG, $firstName, $lastName, $email, $phoneNo, $login);
    }

    public function addDonation(float $amount, string $paymentMethod, array $paymentDetails): bool
    {
        if (!in_array($paymentMethod, self::PAYMENT_METHODS)) {
            throw new InvalidArgumentException("Invalid payment method.");
        }

        $paymentContext = new PaymentContext($this->getPaymentMethod($paymentMethod, $paymentDetails));
        $paymentResponse = $paymentContext->executePayment($amount);

        if (!$paymentResponse) {
            return false;
        }

        try {
            $conn = Database::getInstance()->getConnection();
            $amount = $conn->real_escape_string($amount);
            $paymentMethod = $conn->real_escape_string($paymentMethod);

            $query = "INSERT INTO Donation (donationDate, donationAmount, paymentMethod) VALUES (CURDATE(), '$amount', '$paymentMethod')";
            if ($conn->query($query) === true) {
                $donationID = $conn->insert_id;
                
                $userID = $this->getUserID();
                $donatingQuery = "INSERT INTO Donating (userID, donationID) VALUES ('$userID', '$donationID')";
                
                return $conn->query($donatingQuery) === true;
            } else {
                throw new Exception("Failed to execute query: " . $conn->error);
            }
        } catch (Exception $e) {
            error_log("Error adding donation: " . $e->getMessage());
            return false;
        }
    }

    public function fetchDonationHistory(): array
    {
        try {
            $conn = Database::getInstance()->getConnection();
            $userID = $this->getUserID();
            $userID = $conn->real_escape_string($userID);

            $query = "SELECT d.donationID, d.donationDate, d.donationAmount, d.paymentMethod 
                      FROM Donation d
                      JOIN Donating don ON d.donationID = don.donationID
                      WHERE don.userID = '$userID'";

            $result = $conn->query($query);
            if ($result) {
                return $result->fetch_all(MYSQLI_ASSOC);
            }
            return [];
        } catch (Exception $e) {
            error_log("Error fetching donation history: " . $e->getMessage());
            return [];
        }
    }

    public function updatePersonalInfo(string $firstName, string $lastName, string $email, string $phoneNo): bool
    {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setPhoneNo($phoneNo);

        try {
            $conn = Database::getInstance()->getConnection();
            $userID = $this->getUserID();

            $firstName = $conn->real_escape_string($firstName);
            $lastName = $conn->real_escape_string($lastName);
            $email = $conn->real_escape_string($email);
            $phoneNo = $conn->real_escape_string($phoneNo);

            $query = "UPDATE Person SET firstName = '$firstName', lastName = '$lastName', email = '$email', phoneNo = '$phoneNo' 
                      WHERE userID = '$userID'";
            return $conn->query($query) === true;
        } catch (Exception $e) {
            error_log("Error updating personal info: " . $e->getMessage());
            return false;
        }
    }

    public function updateDonation(int $donationID, float $amount, string $paymentMethod): bool
    {
        if (!in_array($paymentMethod, self::PAYMENT_METHODS)) {
            throw new InvalidArgumentException("Invalid payment method.");
        }

        try {
            $conn = Database::getInstance()->getConnection();
            $donationID = $conn->real_escape_string($donationID);
            $amount = $conn->real_escape_string($amount);
            $paymentMethod = $conn->real_escape_string($paymentMethod);

            $query = "UPDATE Donation SET donationAmount = '$amount', paymentMethod = '$paymentMethod' WHERE donationID = '$donationID'";
            return $conn->query($query) === true;
        } catch (Exception $e) {
            error_log("Error updating donation: " . $e->getMessage());
            return false;
        }
    }

    private function getPaymentMethod(string $paymentMethod, array $paymentDetails): IPayment
    {
        switch ($paymentMethod) {
            case 'Credit Card':
                return new PayCreditCard($paymentDetails['cardNumber'], new DateTime($paymentDetails['expiryDate']), $paymentDetails['cvv']);
            case 'Visa':
                return new PayVisa($paymentDetails['cardNumber'], new DateTime($paymentDetails['expiryDate']));
            case 'Fawry':
                return new PayFawry($paymentDetails['fawryNumber'], $paymentDetails['referenceID']);
            default:
                throw new InvalidArgumentException("Unsupported payment method.");
        }
    }
    public function getUserTypeID(): int
    {
        return $this->userTypeID;
    }
    
    
}
