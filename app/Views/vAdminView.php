<?php
class VAdminView {
    private $exporter;

    public function __construct(HtmlExporter $exporter) {
        $this->exporter = $exporter;
    }

    public function renderAdminPanel($reportData) {
        $this->exporter->export($reportData);
    }

    public function renderError($message) {
        $errorData = [
            'header' => 'Error',
            'body' => ['message' => $message],
            'footer' => ''
        ];
        $this->exporter->export($errorData);
    }
}