<?php
/**
 * EventView Class
 * Handles all rendering of event-related content with support for staff management
 * and improved event details display
 */
class EventView {
    /**
     * Formats an address object into a human-readable string
     * Handles edge cases and invalid addresses gracefully
     */
    private function formatAddress(?Address $address): string {
        if (!$address || !($address instanceof Address)) {
            return 'Invalid Address';
        }
    
        $addressParts = [];
        $addressParts[] = $address->getName() ?? 'Unknown Address Name';
        $addressParts[] = "(" . ($address->getLevel() ?? 'Unknown Level') . ")";
        
        return implode(' ', $addressParts);
    }
    
    /**
     * Renders the page header with enhanced navigation and Bootstrap 5 integration
     */
    public function renderPageHeader(): void {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="Event Management System">
            <title>Event Management System</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <a class="navbar-brand" href="/">Event Manager</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="?action=list">All Events</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?action=upcoming">Upcoming Events</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?action=create">Create Event</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <?php
            // Display any notifications stored in the session
            if (isset($_SESSION['notification'])) {
                echo '<div class="container mt-3">';
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo htmlspecialchars($_SESSION['notification']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                echo '</div>';
                echo '</div>';
                unset($_SESSION['notification']);
            }
    }

    /**
     * Renders the page footer with Bootstrap 5 scripts
     */
    public function renderPageFooter(): void {
        ?>
            <footer class="container mt-5 mb-3 text-center">
                <hr>
                <p>&copy; <?php echo date('Y'); ?> Event Management System</p>
            </footer>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    /**
     * Renders the event creation/update form with Address integration
     */
    public function renderEventForm(?Event $event = null): void {
        $isUpdate = $event !== null;
        $title = $isUpdate ? 'Update Event' : 'Create New Event';
        $address = $isUpdate ? $event->getEventLocation() : null;
        
        echo '<div class="container mt-5">';
        echo '<h2 class="text-center mb-4">' . htmlspecialchars($title) . '</h2>';
        
        echo '<div class="row">';
        echo '<div class="col-lg-8 mx-auto">';
        echo '<form method="POST" action="?action=' . ($isUpdate ? 'update' : 'create') . '">';
        
        if ($isUpdate) {
            echo '<input type="hidden" name="eventID" value="' . htmlspecialchars($event->getEventID()) . '">';
        }
        
        // Event Details Section
        echo '<div class="card mb-4">';
        echo '<div class="card-header"><h4 class="mb-0">Event Details</h4></div>';
        echo '<div class="card-body">';
        
        // Event Name
        echo '<div class="mb-3">';
        echo '<label class="form-label">Event Name</label>';
        echo '<input type="text" class="form-control" name="eventName" required ';
        echo 'value="' . ($isUpdate ? htmlspecialchars($event->getEventName()) : '') . '">';
        echo '</div>';
        
        // Event Date
        echo '<div class="mb-3">';
        echo '<label class="form-label">Event Date</label>';
        echo '<input type="date" class="form-control" name="eventDate" required ';
        echo 'value="' . ($isUpdate ? htmlspecialchars($event->getEventDate()) : '') . '">';
        echo '</div>';
        
        // Event Description
        echo '<div class="mb-3">';
        echo '<label class="form-label">Description</label>';
        echo '<textarea class="form-control" name="eventDescription" rows="3" required>';
        echo $isUpdate ? htmlspecialchars($event->getEventDescription()) : '';
        echo '</textarea>';
        echo '</div>';
        
        // Staff Requirements
        echo '<div class="row">';
        echo '<div class="col-md-4">';
        echo '<label class="form-label">Required Cooks</label>';
        echo '<input type="number" class="form-control" name="reqCooks" min="0" required ';
        echo 'value="' . ($isUpdate ? htmlspecialchars($event->getReqCooks()) : '0') . '">';
        echo '</div>';
        
        echo '<div class="col-md-4">';
        echo '<label class="form-label">Required Delivery Staff</label>';
        echo '<input type="number" class="form-control" name="reqForDelivery" min="0" required ';
        echo 'value="' . ($isUpdate ? htmlspecialchars($event->getReqForDelivery()) : '0') . '">';
        echo '</div>';
        
        echo '<div class="col-md-4">';
        echo '<label class="form-label">Required Coordinators</label>';
        echo '<input type="number" class="form-control" name="reqCoordinators" min="0" required ';
        echo 'value="' . ($isUpdate ? htmlspecialchars($event->getReqCoordinators()) : '0') . '">';
        echo '</div>';
        echo '</div>';
        
        echo '</div>'; // Close card-body
        echo '</div>'; // Close card
        
        // Address Section
        echo '<div class="card mb-4">';
        echo '<div class="card-header"><h4 class="mb-0">Location Details</h4></div>';
        echo '<div class="card-body">';
        
        echo '<div class="mb-3">';
        echo '<label class="form-label">Location Name</label>';
        echo '<input type="text" class="form-control" name="addressName" required ';
        echo 'value="' . ($address ? htmlspecialchars($address->getName()) : '') . '">';
        echo '</div>';
        
        echo '<div class="mb-3">';
        echo '<label class="form-label">Location Level</label>';
        echo '<input type="text" class="form-control" name="addressLevel" required ';
        echo 'value="' . ($address ? htmlspecialchars($address->getLevel()) : '') . '">';
        echo '</div>';
        
        echo '<div class="mb-3">';
        echo '<label class="form-label">Parent Location ID</label>';
        echo '<input type="number" class="form-control" name="parentId" min="0" ';
        echo 'value="' . ($address && $address->getParentId() ? htmlspecialchars($address->getParentId()) : '0') . '">';
        echo '</div>';
        
        echo '</div>'; // Close card-body
        echo '</div>'; // Close card
        
        // Form buttons
        echo '<div class="text-center">';
        echo '<button type="submit" class="btn btn-primary me-2">' . ($isUpdate ? 'Update' : 'Create') . ' Event</button>';
        echo '<a href="?action=list" class="btn btn-secondary">Cancel</a>';
        echo '</div>';
        
        echo '</form>';
        echo '</div>'; // Close col
        echo '</div>'; // Close row
        echo '</div>'; // Close container
    }

    /**
     * Renders a list of events with improved card layout and staff requirement indicators
     */
    public function renderEventList(array $events): void {
        echo '<div class="container mt-5">';
        echo '<h2 class="text-center mb-4">Events</h2>';
        
        if (empty($events)) {
            echo '<div class="alert alert-info">No events found.</div>';
            return;
        }

        echo '<div class="row">';
        foreach ($events as $event) {
            echo '<div class="col-md-4 mb-4">';
            echo '<div class="card h-100">';
            echo '<div class="card-body">';
            echo '<h3 class="card-title h5">' . htmlspecialchars($event->getEventName()) . '</h3>';
            echo '<p class="card-text"><strong>Date:</strong> ' . htmlspecialchars($event->getEventDate()) . '</p>';
            echo '<p class="card-text"><strong>Location:</strong> ' . 
                 htmlspecialchars($this->formatAddress($event->getEventLocation())) . '</p>';
            
            // Staff requirements summary
            if ($event->getReqCooks() > 0 || $event->getReqForDelivery() > 0 || $event->getReqCoordinators() > 0) {
                echo '<div class="mt-2 mb-3">';
                echo '<small class="text-muted">Staff Needed:</small><br>';
                if ($event->getReqCooks() > 0) {
                    echo '<span class="badge bg-info me-1">Cooks: ' . $event->getReqCooks() . '</span>';
                }
                if ($event->getReqForDelivery() > 0) {
                    echo '<span class="badge bg-info me-1">Delivery: ' . $event->getReqForDelivery() . '</span>';
                }
                if ($event->getReqCoordinators() > 0) {
                    echo '<span class="badge bg-info">Coordinators: ' . $event->getReqCoordinators() . '</span>';
                }
                echo '</div>';
            }
            
            echo '<div class="mt-3">';
            echo '<a href="?action=details&id=' . urlencode($event->getEventID()) . '" class="btn btn-primary me-2">View Details</a>';
            echo '<a href="?action=update&id=' . urlencode($event->getEventID()) . '" class="btn btn-secondary">Edit</a>';
            echo '</div>';
            
            echo '</div>'; // Close card-body
            echo '</div>'; // Close card
            echo '</div>'; // Close col
        }
        echo '</div>'; // Close row
        echo '</div>'; // Close container
    }

    /**
     * Renders detailed information for a single event with staff assignment functionality
     */
    public function renderEventDetails(Event $event): void {
        $address = $event->getEventLocation();
        
        if (!$address || !($address instanceof Address)) {
            $this->renderError("Invalid Address information for this event.");
            return;
        }
        
        echo '<div class="container mt-5">';
        echo '<div class="row">';
        echo '<div class="col-lg-8 mx-auto">';
        
        echo '<h2 class="text-center mb-4">' . htmlspecialchars($event->getEventName()) . '</h2>';
        
        // Event Details Card
        echo '<div class="card mb-4">';
        echo '<div class="card-header"><h4 class="mb-0">Event Details</h4></div>';
        echo '<div class="card-body">';
        
        // Basic event information
        $this->renderDetailRow('Date', $event->getEventDate());
        $this->renderDetailRow('Location', $this->formatAddress($address));
        $this->renderDetailRow('Description', nl2br(htmlspecialchars($event->getEventDescription())));
        
        echo '</div>'; // Close card-body
        echo '</div>'; // Close card
        
        // Staff Requirements Card
        echo '<div class="card mb-4">';
        echo '<div class="card-header"><h4 class="mb-0">Staff Requirements</h4></div>';
        echo '<div class="card-body">';
        
        // Staff assignment forms
        $this->renderStaffAssignment($event, 'cook', 'Cooks', $event->getReqCooks());
        $this->renderStaffAssignment($event, 'delivery', 'Delivery Staff', $event->getReqForDelivery());
        $this->renderStaffAssignment($event, 'coordinator', 'Coordinators', $event->getReqCoordinators());
        
        echo '</div>'; // Close card-body
        echo '</div>'; // Close card
        
        // Action buttons
        echo '<div class="text-center mt-4">';
        echo '<a href="?action=update&id=' . urlencode($event->getEventID()) . '" class="btn btn-primary me-2">Edit Event</a>';
        echo '<a href="?action=list" class="btn btn-secondary me-2">Back to List</a>';
        
        // Delete button with confirmation
        echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete Event</button>';
        echo '</div>';
        
        // Delete confirmation modal
        $this->renderDeleteModal($event);
        
        echo '</div>'; // Close col
        echo '</div>'; // Close row
        echo '</div>'; // Close container
    }
    
    /**
     * Helper method to render a detail row in consistent format
     */
    private function renderDetailRow(string $label, string $value): void {
        echo '<div class="row mb-3">';
        echo '<div class="col-md-4"><strong>' . htmlspecialchars($label) . ':</strong></div>';
        echo '<div class="col-md-8">' . $value . '</div>';
        echo '</div>';
    }
    
    /**
     * Helper method to render staff assignment section
     */
    private function renderStaffAssignment(Event $event, string $type, string $label, int $required): void {
        if ($required <= 0) {
            return;
        }
        
        echo '<div class="mb-4">';
        echo '<h5>' . htmlspecialchars($label) . ' Needed: ' . $required . '</h5>';
        // Continuing from the renderStaffAssignment method...
        echo '<form method="POST" action="?action=assign" class="mt-2">';
        echo '<input type="hidden" name="eventID" value="' . $event->getEventID() . '">';
        echo '<input type="hidden" name="staffType" value="' . htmlspecialchars($type) . '">';
        
        echo '<div class="d-grid gap-2">';
        echo '<button type="submit" class="btn btn-outline-primary">';
        echo 'Assign ' . htmlspecialchars($label);
        echo '</button>';
        echo '</div>';
        
        echo '</form>';
        echo '</div>';
    }

    /**
     * Renders a confirmation modal for event deletion
     * This provides a safety check before permanent deletion
     */
    private function renderDeleteModal(Event $event): void {
        echo '<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">';
        echo '<div class="modal-dialog">';
        echo '<div class="modal-content">';
        
        echo '<div class="modal-header">';
        echo '<h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        echo '</div>';
        
        echo '<div class="modal-body">';
        echo '<p>Are you sure you want to delete the event "' . htmlspecialchars($event->getEventName()) . '"?</p>';
        echo '<p class="text-danger">This action cannot be undone.</p>';
        echo '</div>';
        
        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>';
        
        // Delete form with POST method for security
        echo '<form method="POST" action="?action=delete" class="d-inline">';
        echo '<input type="hidden" name="eventID" value="' . $event->getEventID() . '">';
        echo '<button type="submit" class="btn btn-danger">Delete Event</button>';
        echo '</form>';
        
        echo '</div>'; // Close modal-footer
        echo '</div>'; // Close modal-content
        echo '</div>'; // Close modal-dialog
        echo '</div>'; // Close modal
    }

    /**
     * Renders an error message with consistent styling and structure
     * Includes an option to return to the events list
     */
    public function renderError(string $message): void {
        echo '<div class="container mt-5">';
        echo '<div class="row">';
        echo '<div class="col-lg-8 mx-auto">';
        
        echo '<div class="alert alert-danger" role="alert">';
        echo '<h4 class="alert-heading">Error Occurred</h4>';
        echo '<p class="mb-0">' . htmlspecialchars($message) . '</p>';
        echo '</div>';
        
        echo '<div class="text-center">';
        echo '<a href="?action=list" class="btn btn-secondary">Return to Events List</a>';
        echo '</div>';
        
        echo '</div>'; // Close col
        echo '</div>'; // Close row
        echo '</div>'; // Close container
    }

    /**
     * Renders a success message with consistent styling
     * Used for confirming actions like updates and assignments
     */
    public function renderSuccess(string $message): void {
        echo '<div class="container mt-5">';
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo '<strong>Success!</strong> ' . htmlspecialchars($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        echo '</div>';
    }
}


