<?php

#!!!!!!!!!!!!!!!!!!!!!!!!!! FILE ONLY LEFT FOR REFERENCE, PAYMENTMODEL WILL BE USED INSTEAD

// // Define the Strategy Interface for Payment
// interface IPayment {
//     public function pay(float $amount): string;
// }

// // Concrete Strategy 1: Credit Card Payment
// class PayCreditCard implements IPayment {
//     private string $cardNumber;
//     private DateTime $expiryDate;
//     private int $CVV;

//     public function __construct(string $cardNumber) {
//         $this->cardNumber = $cardNumber;
//         // $this->expiryDate = $expiryDate;
//         // $this->CVV = $CVV;
//     }

//     public function pay(float $amount): string {
//         // Example logic for processing credit card payment
//         return "Paid $amount using Credit Card ending with " . substr($this->cardNumber, -4);
//     }
// }

// // Concrete Strategy 2: Visa Payment
// class PayVisa implements IPayment {
//     private string $visaNumber;
//     private DateTime $visaExpiry;

//     public function __construct(string $visaNumber) {
//         $this->visaNumber = $visaNumber;
//         // $this->visaExpiry = $visaExpiry;
//     }

//     public function pay(float $amount): string {
//         // Example logic for processing Visa payment
//         return "Paid $amount using Visa card ending with " . substr($this->visaNumber, -4);
//     }
// }

// // Concrete Strategy 3: Fawry Payment
// class PayFawry implements IPayment {
//     private string $fawryAccountNumber;
//     private string $fawryPin;

//     public function __construct(string $fawryAccountNumber) {
//         $this->fawryAccountNumber = $fawryAccountNumber;
//         // $this->fawryPin = $fawryPin;
//     }

//     public function pay(float $amount): string {
//         // Example logic for processing Fawry payment
//         return "Paid $amount using Fawry account " . $this->fawryAccountNumber;
//     }
// }

// // Context Class to handle payment with a chosen strategy

// class PaymentContext {
//     private $paymentMethod;

//     // Constructor to accept an IPayment instance
//     public function __construct(IPayment $paymentMethod) {
//         $this->paymentMethod = $paymentMethod;
//     }

//     // Method to execute payment using the provided strategy
//     public function executePayment($amount) {
//         return $this->paymentMethod->pay($amount);
//     }

//     // Optional method to switch the payment strategy if needed
//     public function setPaymentMethod(IPayment $paymentMethod) {
//         $this->paymentMethod = $paymentMethod;
//     }
// }

