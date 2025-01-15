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
        if (isset($_POST['paypal_order_check'])) {
            $orderId = $_POST['order_id'];
            $donationAmount = $_POST['donation_amount'];
            
            $result = $this->model->validateAndProcessPayment($orderId, $donationAmount);
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }
}