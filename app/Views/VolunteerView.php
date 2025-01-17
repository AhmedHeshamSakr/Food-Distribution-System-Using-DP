<?php

class VolunteerView {
    private $events;
    private $db;

    public function __construct() {
        // Initialize database connection
        $this->db = Database::getInstance()->getConnection();
        $this->loadEvents();
    }

    /**
     * Loads events from the database
     * Fetches event information along with counts of volunteers in each role
     */
    private function loadEvents() {
        $query = "
            SELECT e.*, 
                   COUNT(DISTINCT c.cookID) AS cook_count,
                   COUNT(DISTINCT d.userID) AS delivery_count,
                   COUNT(DISTINCT co.userID) AS coordinator_count
            FROM events e
            LEFT JOIN cooking c ON e.eventID = c.eventID
            LEFT JOIN delivery d ON e.eventID = d.eventID
            LEFT JOIN coordinating co ON e.eventID = co.eventID
            WHERE e.date >= CURDATE()
            GROUP BY e.eventID
        ";

        $result = mysqli_query($this->db, $query);
        $this->events = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /**
     * Renders the main volunteer page with event cards and modal
     * @return string The HTML content for the page
     */
    public function render() {
        return $this->getHeader() 
             . $this->getEventsContainer() 
             . $this->getEventModal() 
             . $this->getFooter();
    }

    /**
     * Generates the page header with necessary CSS and meta tags
     */
    private function getHeader() {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Volunteer Events Dashboard</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .event-card { transition: transform 0.2s; }
                .event-card:hover { transform: translateY(-5px); }
                .role-count { font-size: 1.2rem; font-weight: bold; }
                .required-count { color: #dc3545; }
            </style>
        </head>
        <body class="bg-light">
        HTML;
    }

    /**
     * Creates the container holding all event cards
     */
    private function getEventsContainer() {
        $html = <<<HTML
        <div class="container py-5">
            <h1 class="text-center mb-5">Upcoming Volunteer Events</h1>
            <div class="row g-4" id="events-container">
        HTML;

        foreach ($this->events as $event) {
            $html .= $this->generateEventCard($event);
        }

        $html .= '</div></div>';
        return $html;
    }

    /**
     * Generates an individual event card
     * @param array $event Event data
     */
    private function generateEventCard($event) {
        return <<<HTML
        <div class="col-md-6 col-lg-4">
            <div class="card event-card h-100 shadow-sm" data-event-id="{$event['eventID']}">
                <div class="card-body">
                    <h5 class="card-title">{$event['title']}</h5>
                    <p class="card-text">
                        <i class="bi bi-calendar"></i> {$event['date']}<br>
                        <i class="bi bi-geo-alt"></i> {$event['location']}
                    </p>
                    <button class="btn btn-primary w-100" onclick="volunteerView.showEventDetails({$event['eventID']})">
                        View Details
                    </button>
                </div>
            </div>
        </div>
        HTML;
    }

    /**
     * Creates the modal for displaying detailed event information
     */
    private function getEventModal() {
        return <<<HTML
        <div class="modal fade" id="eventModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Event Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="eventModalBody"></div>
                </div>
            </div>
        </div>
        HTML;
    }

    /**
     * Creates the page footer and JavaScript
     */
    private function getFooter() {
        return <<<HTML
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script>
        class VolunteerViewJS {
            constructor() {
                this.events = {$this->getEventsJson()};
                this.initializeEventListeners();
            }

            initializeEventListeners() {
                document.addEventListener('DOMContentLoaded', () => this.loadEvents());
            }

            showEventDetails(eventId) {
                const event = this.events.find(e => e.eventID === eventId);
                const modalBody = document.getElementById('eventModalBody');
                modalBody.innerHTML = this.createEventDetails(event);
                new bootstrap.Modal(document.getElementById('eventModal')).show();
            }

            handleRoleSelection(checkbox) {
                const role = checkbox.dataset.role;
                const eventId = checkbox.dataset.event;
                const isChecked = checkbox.checked;

                $.ajax({
                    url: 'volunteer-controller.php',
                    method: 'POST',
                    data: { action: 'updateRole', eventId, role, selected: isChecked },
                    success: response => this.handleRoleUpdateResponse(response, checkbox),
                    error: () => this.handleRoleUpdateError(checkbox)
                });
            }

            handleRoleUpdateResponse(response, checkbox) {
                if (response.success) {
                    this.updateRequiredCount(checkbox.dataset.role, checkbox.dataset.eventId, checkbox.checked);
                    this.showAlert('Success!', 'Role updated successfully', 'success');
                } else {
                    checkbox.checked = !checkbox.checked;
                    this.showAlert('Error', 'Failed to update role', 'danger');
                }
            }

            handleRoleUpdateError(checkbox) {
                checkbox.checked = !checkbox.checked;
                this.showAlert('Error', 'An error occurred', 'danger');
            }

            showAlert(title, message, type = 'info') {
                const alertHtml = `<div class="alert alert-\${type} alert-dismissible fade show" role="alert">
                                     <strong>\${title}</strong> \${message}
                                     <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                   </div>`;
                document.querySelector('.modal-body').insertAdjacentHTML('afterbegin', alertHtml);
            }

            updateRequiredCount(role, eventId, isChecked) {
                const element = document.getElementById(`${role}-required-${event}`);
                const currentCount = parseInt(element.textContent, 10);
                element.textContent = isChecked ? currentCount - 1 : currentCount + 1;
            }

            createEventDetails(event) {
                return <<<HTML
                <div class="container-fluid">
                    <h4>{$event['title']}</h4>
                    <p class="text-muted">
                        <strong>Date:</strong> {$event['date']}<br>
                        <strong>Location:</strong> {$event['location']}
                    </p>
                    <p>{$event['description']}</p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Role</th>
                                    <th>Required</th>
                                    <th>Current</th>
                                    <th>Volunteer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Cook</td>
                                    <td>
                                        <span class="required-count" id="cook-required-{$event['id']}">
                                            {$event['roles']['cook']['required']}
                                        </span>
                                    </td>
                                    <td>{$event['roles']['cook']['current']}</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                data-role="cook" 
                                                data-event="{$event['id']}"
                                                onchange="handleRoleSelection(this)">
                                        </div>
                                    </td>
                                </tr>
                                <!-- Add more roles here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                HTML;
        }

        const volunteerView = new VolunteerViewJS();
        </script>
        </body>
        </html>
        HTML;
    }

    /**
     * Converts event data to JSON for use in JavaScript
     */
    private function getEventsJson() {
        return json_encode($this->events);
    }
}
