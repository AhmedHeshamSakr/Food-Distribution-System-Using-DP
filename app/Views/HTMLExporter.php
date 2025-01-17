<?php
require_once '../Models/Exporter.php';

/**
 * HtmlExporter class implements the ExporterInterface to render report data as HTML
 * Uses Bootstrap 5 for styling and responsive design
 */
class HtmlExporter implements ExporterInterface {
    /**
     * Exports report data as formatted HTML with Bootstrap styling
     * 
     * @param array $reportData Array containing report information with keys:
     *                         header, timestamp, title, body, summary, footer
     * @throws InvalidArgumentException If required data fields are missing
     * @return void Outputs HTML directly
     */
    public function export(array $reportData) {
        // Validate required fields
        $requiredFields = ['header', 'timestamp', 'title', 'body', 'summary', 'footer'];
        foreach ($requiredFields as $field) {
            if (!isset($reportData[$field])) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }

        try {
            // Start output buffering to handle potential errors gracefully
            ob_start();
            
            // Render the HTML structure
            $this->renderHeader();
            $this->renderBody($reportData);
            $this->renderFooter();
            
            // Output the buffered content
            $content = ob_get_clean();
            echo $content;
            
        } catch (Exception $e) {
            // Clean any existing output
            ob_end_clean();
            throw new RuntimeException("Failed to generate HTML report: " . $e->getMessage());
        }
    }

    /**
     * Renders the HTML header with necessary meta tags and CSS/JS dependencies
     */
    private function renderHeader() {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Report Dashboard</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        </head>
        <body>
        <?php
    }

    /**
     * Renders the main content of the report
     * @param array $reportData The data to be displayed
     */
    private function renderBody(array $reportData) {
        ?>
        <div class="container mt-4">
            <div class="card shadow">
                <!-- Header Section -->
                <div class="card-header bg-primary text-white">
                    <h1 class="text-center mb-0"><?php echo htmlspecialchars($reportData['header']); ?></h1>
                </div>

                <!-- Main Content Section -->
                <div class="card-body">
                    <div class="text-muted mb-3">
                        <i class="bi bi-clock"></i> <?php echo htmlspecialchars($reportData['timestamp']); ?>
                    </div>
                    
                    <h3 class="mb-4"><?php echo htmlspecialchars($reportData['title']); ?></h3>
                    
                    <!-- Report Items Grid -->
                    <div class="row g-4">
                        <?php $this->renderReportItems($reportData['body']); ?>
                    </div>

                    <!-- Summary Section -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <p class="lead mb-0"><?php echo htmlspecialchars($reportData['summary']); ?></p>
                    </div>
                </div>

                <!-- Footer Section -->
                <div class="card-footer text-center bg-light">
                    <h4 class="text-muted mb-0"><?php echo htmlspecialchars($reportData['footer']); ?></h4>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Renders individual report items in a grid layout
     * @param array $items Array of report items to display
     */
    private function renderReportItems(array $items) {
        foreach ($items as $item) {
            ?>
            <div class="col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <?php
                        foreach ($item as $key => $value) {
                            if ($key === 'Actions') {
                                echo $value; // Allow HTML for actions
                            } else {
                                echo '<p class="mb-2"><strong>' . htmlspecialchars($key) . ':</strong> ' . 
                                     htmlspecialchars($value) . '</p>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Renders the HTML footer
     */
    private function renderFooter() {
        ?>
        </body>
        </html>
        <?php
    }
}