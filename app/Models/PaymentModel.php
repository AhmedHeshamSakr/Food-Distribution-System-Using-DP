<?php
require_once '../../config/DB.php';
// Define the Strategy Interface for Payment
interface IPayment {
    public function pay(float $amount): array;
}

// Concrete Strategy 1: PayPal Payment
class PayPayPal implements IPayment {
    private $orderId;
    private $currency;

    public function __construct(string $currency = 'USD', ?string $orderId = null) {
        $this->currency = $currency;
        $this->orderId = $orderId;
    }

    public function pay(float $amount): array {
        // PayPal specific response format
        return [
            'status' => 1,
            'payment_method' => 'PayPal',
            'amount' => $amount,
            'currency' => $this->currency,
            'order_id' => $this->orderId,
            'message' => 'Payment processed successfully via PayPal'
        ];
    }
}

// Concrete Strategy 2: Credit/Debit Card via PayPal
class PayCard implements IPayment {
    private $currency;
    private $orderId;

    public function __construct(string $currency = 'USD', ?string $orderId = null) {
        $this->currency = $currency;
        $this->orderId = $orderId;
    }

    public function pay(float $amount): array {
        // Card payment through PayPal response format
        return [
            'status' => 1,
            'payment_method' => 'Card',
            'amount' => $amount,
            'currency' => $this->currency,
            'order_id' => $this->orderId,
            'message' => 'Payment processed successfully via Card'
        ];
    }
}

// Payment Model that integrates with PayPal API
class PaymentModel {
    private $db;
    private $currency;
    private $itemNumber;
    private $paymentContext;

    public function __construct($db) {
        $this->db = $db;
        $this->currency = 'USD';
        $this->itemNumber = uniqid();
        // Default to PayPal payment method
        $this->paymentContext = new PaymentContext(new PayPayPal($this->currency));
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getItemNumber() {
        return $this->itemNumber;
    }

    public function setPaymentMethod(string $method, ?string $orderId = null) {
        $paymentStrategy = match($method) {
            'paypal' => new PayPayPal($this->currency, $orderId),
            'card' => new PayCard($this->currency, $orderId),
            default => throw new InvalidArgumentException('Invalid payment method')
        };
        
        $this->paymentContext->setPaymentMethod($paymentStrategy);
    }

    public function validateAndProcessPayment($orderId, $donationAmount, $paymentMethod = 'paypal') {
        try {
            // Set the payment method with order ID
            $this->setPaymentMethod($paymentMethod, $orderId);
            
            // Process the payment
            $result = $this->paymentContext->executePayment($donationAmount);
            
            // Store transaction in database if needed
            // $this->storeTransaction($result);
            
            return [
                'status' => $result['status'],
                'ref_id' => $result['order_id'],
                'message' => $result['message']
            ];
        } catch (Exception $e) {
            return [
                'status' => 0,
                'ref_id' => null,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    private function storeTransaction($paymentResult) {
        $conn = $this->db->getConnection();
        $sql = "INSERT INTO transactions (order_id, amount, currency, payment_method, status) VALUES (?, ?, ?, ?, ?)";

    }
}

// Context Class
class PaymentContext {
    private $paymentMethod;

    public function __construct(IPayment $paymentMethod) {
        $this->paymentMethod = $paymentMethod;
    }

    public function executePayment($amount) {
        return $this->paymentMethod->pay($amount);
    }

    public function setPaymentMethod(IPayment $paymentMethod) {
        $this->paymentMethod = $paymentMethod;
    }
}


class Transaction{
    private $transactionID;
    private $amount;
    private $userID;
    private $date;


    public function __construct( $amount, $userID, $date){

        $this->amount = $amount;
        $this->userID = $userID;
        $this->date = $date;

    }

    public function storeTransaction(): bool {
        try {
            $conn= database::getInstance()->getConnection();
            $date = $this->date;
            $amount = $this->amount;
            $userID = $this->userID;

            $query = "INSERT INTO transactions (`date`, amount, userID) 
                VALUES ('{$date}', '{$amount}', '{$userID}')";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            // Fetch the transaction ID and set it
            $this->transactionID = $stmt->insert_id;

            $stmt->close();
            return true;
        } catch (Exception $e) {
            // Log the error or handle it as needed
            error_log("Transaction failed: " . $e->getMessage());
            return false;
        }
    }

    public function getTransactionID() {
        return $this->transactionID;
    }
    public function getAmount() {
        return $this->amount;
    }
    public function getUserID() {
        return $this->userID;
    }
    public function getDate() {
        return $this->date;
    }
    
    public function updateTransaction(array $fieldsToUpdate): bool
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
        $query = "UPDATE transactions SET $setQueryStr WHERE transactionID = '{$this->transactionID}'";

        // Run the query and return the result
        return run_query($query);
    }

    public function setAmount($amount) {
        $this->amount = $amount;
        $fieldsToUpdate = ['amount' => $amount];
        return $this->updateTransaction($fieldsToUpdate);
    }

    public function setDate($date) {
        $this->date = $date;
        $fieldsToUpdate = ['date' => $date];
        return $this->updateTransaction($fieldsToUpdate);
    }

    public function setUserID($userID) {
        $this->userID = $userID;
        $fieldsToUpdate = ['userID' => $userID];
        return $this->updateTransaction($fieldsToUpdate);
    }
    
    public function deleteTransaction(): bool
    {
        $query = "DELETE FROM transactions WHERE transactionID = '{$this->transactionID}'";
        return run_query($query);
    }

    
}