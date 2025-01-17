<?php

class VolunteerView {
    /**
     * Renders the standard page header with navigation
     */
    public function renderPageHeader(): void {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Volunteer Portal</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
                <div class="container">
                    <a class="navbar-brand" href="?action=upcoming">Volunteer Portal</a>
                    <div class="navbar-nav">
                        <a class="nav-link" href="?action=upcoming">Upcoming Events</a>
                    </div>
                </div>
            </nav>
            <div class="container">
        <?php
    }

    /**
     * Renders the page footer with necessary scripts
     */
    public function renderPageFooter(): void {
        ?>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
        <?php
    }

    /**
     * Renders a list of upcoming events
     */
    public function renderUpcomingEvents(array $events): void {
        ?>
        <h2 class="mb-4">Upcoming Events</h2>
        <?php if (empty($events)): ?>
            <div class="alert alert-info">No upcoming events found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event->getEventName()) ?></td>
                                <td><?= htmlspecialchars($event->getEventDate()) ?></td>
                                <td><?= htmlspecialchars($event->getEventLocation()->getName()) ?></td>
                                <td>
                                    <a href="?action=details&id=<?= $event->getEventID() ?>" class="btn btn-sm btn-primary">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <?php
    }


// Inside the renderEventDetails method, replace the staff requirements section with:

public function renderEventDetails(Event $event): void {
    ?>
    <div class="card">
        <div class="card-header">
            <h2><?= htmlspecialchars($event->getEventName()) ?></h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <!-- Event Information section remains the same -->
                    <h4>Event Information</h4>
                    <dl class="row">
                        <dt class="col-sm-3">Date</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($event->getEventDate()) ?></dd>

                        <dt class="col-sm-3">Location</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($event->getEventLocation()->getName()) ?></dd>

                        <dt class="col-sm-3">Description</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($event->getEventDescription()) ?></dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h4>Staff Requirements</h4>
                    <div class="card mb-3">
                        <div class="card-body">
                            <!-- Cook Section -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Cooks Needed: <span id="cooksCount"><?= $event->getReqCooks() ?></span></h5>
                                    <div class="form-check">
                                        <input class="form-check-input staff-checkbox" type="checkbox" 
                                               id="cookCheckbox" 
                                               data-staff-type="cook"
                                               data-event-id="<?= $event->getEventID() ?>"
                                               <?= $event->getReqCooks() <= 0 ? 'disabled' : '' ?>>
                                        <label class="form-check-label" for="cookCheckbox">
                                            Volunteer as Cook
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Section -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Delivery Staff Needed: <span id="deliveryCount"><?= $event->getReqForDelivery() ?></span></h5>
                                    <div class="form-check">
                                        <input class="form-check-input staff-checkbox" type="checkbox" 
                                               id="deliveryCheckbox" 
                                               data-staff-type="delivery"
                                               data-event-id="<?= $event->getEventID() ?>"
                                               <?= $event->getReqForDelivery() <= 0 ? 'disabled' : '' ?>>
                                        <label class="form-check-label" for="deliveryCheckbox">
                                            Volunteer for Delivery
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Coordinator Section -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Coordinators Needed: <span id="coordinatorCount"><?= $event->getReqCoordinators() ?></span></h5>
                                    <div class="form-check">
                                        <input class="form-check-input staff-checkbox" type="checkbox" 
                                               id="coordinatorCheckbox" 
                                               data-staff-type="coordinator"
                                               data-event-id="<?= $event->getEventID() ?>"
                                               <?= $event->getReqCoordinators() <= 0 ? 'disabled' : '' ?>>
                                        <label class="form-check-label" for="coordinatorCheckbox">
                                            Volunteer as Coordinator
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  <script>
document.addEventListener('DOMContentLoaded', function() {
    const staffCheckboxes = document.querySelectorAll('.staff-checkbox');
    
    staffCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', async function(e) {
            if (!this.checked) {
                return;
            }

            try {
                // Get the form data from the checkbox's data attributes
                const eventId = this.dataset.eventId;
                const staffType = this.dataset.staffType;
                
                // Instead of FormData, let's use URLSearchParams for a standard form submission
                const formData = new URLSearchParams();
                formData.append('eventID', eventId);
                formData.append('staffType', staffType);
                
                const response = await fetch('?action=volunteer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData.toString()
                });

                // Add debugging output
                console.log('Request sent:', {
                    eventId: eventId,
                    staffType: staffType
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Response received:', data);
                
                if (data.success) {
                    // Update the count in the UI
                    const countElement = document.getElementById(`${staffType}Count`);
                    if (countElement) {
                        const currentCount = parseInt(countElement.textContent);
                        countElement.textContent = Math.max(0, currentCount - 1);
                        
                        if (currentCount <= 1) {
                            this.disabled = true;
                        }
                    }
                    alert('Successfully registered as ' + staffType);
                } else {
                    this.checked = false;
                    alert(data.message || 'Registration failed');
                }
            } catch (error) {
                console.error('Error details:', error);
                this.checked = false;
                alert('Unable to process request. Please try again.');
            }
        });
    });
});
</script>
    <?php
}
// Helper function to show notifications
public function showNotification(string $type, string $message): void {
    // You can replace this with a more sophisticated notification system
    if ($type === 'error') {
        echo '<script>alert("Error: ' . $message . '");</script>';
    } else {
        echo '<script>alert("' . $message . '");</script>';
    }
}



    public function renderError(string $message): void {
        ?>
        <div class="alert alert-danger">
            <strong>Error:</strong> <?= htmlspecialchars($message) ?>
        </div>
        <?php
    }
}
