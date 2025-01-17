<?php
require_once '../Models/PaymentModel.php';

class PaymentController {
    private $model;

    public function __construct($db) {
        $this->model = new PaymentModel($db);
    }

    public function getPaymentData() {
        return [
            'currency' => $this->model->getCurrency(),
            'itemNumber' => $this->model->getItemNumber()
        ];
    }

    public function validatePayment() {
        error_log('in validate 1');
        
        // Get amount from URL parameter 'donationAmount'
        $donationAmount = isset($_GET['donationAmount']) ? floatval($_GET['donationAmount']) : 0;
        error_log('Donation amount from URL: ' . $donationAmount);
        
        ######################### JUST FOR TESTING
        $userID = 41;
        #########################
        
        if($donationAmount > 0) {  // Only create transaction if amount exists
            $transaction = new Transaction($donationAmount, $userID, date("Y-m-d H:i:s"));
            error_log('About to store transaction with amount: ' . $donationAmount);
            $transaction->storeTransaction();
        }
    
        if (isset($_GET['order_id'])) {
            error_log('in if in validate');
            $orderId = $_GET['order_id'];
            
            $result = $this->model->validateAndProcessPayment($userID, $orderId, $donationAmount);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }
}