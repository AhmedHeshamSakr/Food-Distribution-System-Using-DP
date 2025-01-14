<?php 
abstract class ReportTemplate {
    private ExporterInterface $exporter;
    private array $reportData;

    public function __construct(ExporterInterface $exporter) {
        $this->exporter = $exporter;
        $this->reportData = array(
            'header' => '',
            'timestamp' => '',
            'title' => '',
            'body' => array(),
            'summary' => '',
            'footer' => ''
        );
    }

    final public function generateReport(): void {
        $this->collectData();
        $this->exporter->export($this->reportData);
    }

    private function collectData(): void {
        $this->reportData['header'] = $this->getHeader();
        $this->reportData['timestamp'] = $this->getTimestamp();
        $this->reportData['title'] = $this->getTitle();
        $this->reportData['body'] = $this->getBody();
        $this->reportData['summary'] = $this->getSummary();
        $this->reportData['footer'] = $this->getFooter();
    }

    final protected function getHeader(): string {
        return 'Report Generation';
    }

    final protected function getFooter(): string {
        return 'End of Report';
    }

    final protected function getTimestamp(): string {
        return 'Report Generated On: ' . date('Y-m-d H:i:s');
    }

    abstract protected function getTitle(): string;

    abstract protected function getBody(): array;

    protected function getSummary(): string {
        return 'This report contains detailed information.';
    }
}

class DeliveryReport extends ReportTemplate {
    private array $deliveryData;

    public function __construct(array $deliveryData, ExporterInterface $exporter) {
        parent::__construct($exporter);
        $this->deliveryData = $deliveryData;
    }

    protected function getTitle(): string {
        return 'Delivery Details Report';
    }

    protected function getBody(): array {
        $body = [];
        foreach ($this->deliveryData as $delivery) {
            $body[] = [
                'Delivery ID' => $delivery['deliveryID'],
                'Date' => $delivery['deliveryDate'],
                'Start Location' => $delivery['startLocation'],
                'End Location' => $delivery['endLocation'],
                'Status' => $delivery['status']
            ];
        }
        return $body;
    }

    protected function getSummary(): string {
        return 'This report contains ' . count($this->deliveryData) . ' deliveries.';
    }
}

class ReporterReport extends ReportTemplate {
    private array $reportData;

    public function __construct(array $reportData, ExporterInterface $exporter) {
        parent::__construct($exporter);
        $this->reportData = $reportData;
    }

    protected function getTitle(): string {
        return 'Reporter Reports';
    }

    protected function getBody(): array {
        $body = [];
        foreach ($this->reportData as $report) {
            $body[] = [
                'Report ID' => $report['reportID'],
                'Person In Name' => $report['personInName'],
                'Person In Address' => $report['personInAddress'],
                'Person In Phone' => $report['personInPhone'],
                'Status' => $report['status'],
                'Description' => $report['description']
            ];
        }
        return $body;
    }

    protected function getSummary(): string {
        return 'This report contains ' . count($this->reportData) . ' reporter entries.';
    }
}

class DonationReport extends ReportTemplate {
    private array $donationData;

    public function __construct(array $donationData, ExporterInterface $exporter) {
        parent::__construct($exporter);
        $this->donationData = $donationData;
    }

    protected function getTitle(): string {
        return 'Donation Report';
    }

    protected function getBody(): array {
        $body = [];
        foreach ($this->donationData as $donation) {
            $body[] = [
                'Donation ID' => $donation['donationID'],
                'Date' => $donation['donationDate'],
                'Amount' => $donation['donationAmount'],
                'Payment Method' => $donation['paymentMethod']
            ];
        }
        return $body;
    }

    protected function getSummary(): string {
        $totalAmount = array_sum(array_column($this->donationData, 'donationAmount'));
        return 'This report contains ' . count($this->donationData) . ' donations totaling $' . $totalAmount . '.';
    }
}

class EventReport extends ReportTemplate {
    private array $eventData;

    public function __construct(array $eventData, ExporterInterface $exporter) {
        parent::__construct($exporter);
        $this->eventData = $eventData;
    }

    protected function getTitle(): string {
        return 'Event Report';
    }

    protected function getBody(): array {
        $body = [];
        foreach ($this->eventData as $event) {
            $body[] = [
                'Event ID' => $event['eventID'],
                'Date' => $event['eventDate'],
                'Name' => $event['eventName'],
                'Description' => $event['eventDescription'],
                'Location' => $event['eventLocation']
            ];
        }
        return $body;
    }

    protected function getSummary(): string {
        return 'This report contains details for ' . count($this->eventData) . ' events.';
    }
}

