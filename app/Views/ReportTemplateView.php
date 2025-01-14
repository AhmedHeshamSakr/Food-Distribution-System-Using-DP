<?php

// // Include Bootstrap CSS and JavaScript
// echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">';
// echo '<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>';
// echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>';
// echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>';

// abstract class ReportTemplate
// {
//     final public function generateReport(): void
//     {
//         echo '<div class="container mt-4">';
//         $this->printHeader();
//         $this->printTimestamp();
//         $this->printTitle();
//         $this->printBody();
//         $this->printSeparator();
//         $this->printSummary();
//         $this->printFooter();
//         echo '</div>';
//     }

//     final protected function printHeader(): void
//     {
//         echo '<div class="text-center"><h1>Report Generation</h1><hr></div>';
//     }

//     final protected function printFooter(): void
//     {
//         echo '<div class="text-center"><h4>End of Report</h4><hr></div>';
//     }

//     final protected function printSeparator(): void
//     {
//         echo '<hr>';
//     }

//     final protected function printTimestamp(): void
//     {
//         echo '<p>Report Generated On: <strong>' . date('Y-m-d H:i:s') . '</strong></p>';
//     }

//     protected function printSummary(): void
//     {
//         echo '<p>Summary: <em>This report contains detailed information.</em></p>';
//     }

//     abstract protected function printTitle(): void;

//     abstract protected function printBody(): void;
// }

// class DeliveryReport extends ReportTemplate
// {
//     private array $deliveryData;

//     public function __construct(array $deliveryData)
//     {
//         $this->deliveryData = $deliveryData;
//     }

//     protected function printTitle(): void
//     {
//         echo '<h3>Delivery Details Report</h3><hr>';
//     }

//     protected function printBody(): void
//     {
//         foreach ($this->deliveryData as $delivery) {
//             echo '<div class="card mb-3">';
//             echo '<div class="card-body">';
//             echo '<p>Delivery ID: <strong>' . $delivery['deliveryID'] . '</strong></p>';
//             echo '<p>Date: <strong>' . $delivery['deliveryDate'] . '</strong></p>';
//             echo '<p>Start Location: <strong>' . $delivery['startLocation'] . '</strong></p>';
//             echo '<p>End Location: <strong>' . $delivery['endLocation'] . '</strong></p>';
//             echo '<p>Status: <strong>' . $delivery['status'] . '</strong></p>';
//             echo '</div></div>';
//         }
//     }

//     protected function printSummary(): void
//     {
//         echo '<p>Summary: This report contains <strong>' . count($this->deliveryData) . '</strong> deliveries.</p>';
//     }
// }

// class ReporterReport extends ReportTemplate
// {
//     private array $reportData;

//     public function __construct(array $reportData)
//     {
//         $this->reportData = $reportData;
//     }

//     protected function printTitle(): void
//     {
//         echo '<h3>Reporter Reports</h3><hr>';
//     }

//     protected function printBody(): void
//     {
//         foreach ($this->reportData as $report) {
//             echo '<div class="card mb-3">';
//             echo '<div class="card-body">';
//             echo '<p>Report ID: <strong>' . $report['reportID'] . '</strong></p>';
//             echo '<p>Person In Name: <strong>' . $report['personInName'] . '</strong></p>';
//             echo '<p>Person In Address: <strong>' . $report['personInAddress'] . '</strong></p>';
//             echo '<p>Person In Phone: <strong>' . $report['personInPhone'] . '</strong></p>';
//             echo '<p>Status: <strong>' . $report['status'] . '</strong></p>';
//             echo '<p>Description: <strong>' . $report['description'] . '</strong></p>';
//             echo '</div></div>';
//         }
//     }

//     protected function printSummary(): void
//     {
//         echo '<p>Summary: This report contains <strong>' . count($this->reportData) . '</strong> reporter entries.</p>';
//     }
// }

// class DonationReport extends ReportTemplate
// {
//     private array $donationData;

//     public function __construct(array $donationData)
//     {
//         $this->donationData = $donationData;
//     }

//     protected function printTitle(): void
//     {
//         echo '<h3>Donation Report</h3><hr>';
//     }

//     protected function printBody(): void
//     {
//         foreach ($this->donationData as $donation) {
//             echo '<div class="card mb-3">';
//             echo '<div class="card-body">';
//             echo '<p>Donation ID: <strong>' . $donation['donationID'] . '</strong></p>';
//             echo '<p>Date: <strong>' . $donation['donationDate'] . '</strong></p>';
//             echo '<p>Amount: <strong>' . $donation['donationAmount'] . '</strong></p>';
//             echo '<p>Payment Method: <strong>' . $donation['paymentMethod'] . '</strong></p>';
//             echo '</div></div>';
//         }
//     }

//     protected function printSummary(): void
//     {
//         $totalAmount = array_sum(array_column($this->donationData, 'donationAmount'));
//         echo '<p>Summary: This report contains <strong>' . count($this->donationData) . '</strong> donations totaling <strong>$' . $totalAmount . '</strong>.</p>';
//     }
// }

// class EventReport extends ReportTemplate
// {
//     private array $eventData;

//     public function __construct(array $eventData)
//     {
//         $this->eventData = $eventData;
//     }

//     protected function printTitle(): void
//     {
//         echo '<h3>Event Report</h3><hr>';
//     }

//     protected function printBody(): void
//     {
//         foreach ($this->eventData as $event) {
//             echo '<div class="card mb-3">';
//             echo '<div class="card-body">';
//             echo '<p>Event ID: <strong>' . $event['eventID'] . '</strong></p>';
//             echo '<p>Date: <strong>' . $event['eventDate'] . '</strong></p>';
//             echo '<p>Name: <strong>' . $event['eventName'] . '</strong></p>';
//             echo '<p>Description: <strong>' . $event['eventDescription'] . '</strong></p>';
//             echo '<p>Location: <strong>' . $event['eventLocation'] . '</strong></p>';
//             echo '</div></div>';
//         }
//     }

//     protected function printSummary(): void
//     {
//         echo '<p>Summary: This report contains details for <strong>' . count($this->eventData) . '</strong> events.</p>';
//     }
// }
