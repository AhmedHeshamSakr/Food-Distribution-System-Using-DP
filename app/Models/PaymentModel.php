<?php
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