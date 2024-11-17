<?php

class EventAdminView
{


    public function renderError($errorMessage)
{
    echo <<<HTML
    <div class="alert alert-danger" role="alert">
        <strong>Error:</strong> {$errorMessage}
    </div>
HTML;
}
    /**
     * Render the page header with Bootstrap styling.
     */
    public function renderPageHeader()
    {
        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .form-section {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Event Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
HTML;
    }

    /**
     * Render the page footer with Bootstrap scripts.
     */
    public function renderPageFooter()
    {
        echo <<<HTML
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
    }

    /**
     * Render the list of events in a table.
     */
    /**
 * Render the list of events in a table.
 */
public function renderEventList(array $events)
{
    echo <<<HTML
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title">Event List</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
HTML;

    foreach ($events as $event) {
        // Access properties using object syntax
        echo <<<HTML
            <tr>
                <td>{$event->id}</td>
                <td>{$event->name}</td>
                <td>{$event->date}</td>
                <td>{$event->location}</td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="delete_event">
                        <input type="hidden" name="eventID" value="{$event->id}">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
HTML;
    }

    echo <<<HTML
                </tbody>
            </table>
        </div>
    </div>
HTML;
}


    /**
     * Render the form to create a new event.
     */
    public function renderCreateEventForm()
    {
        echo <<<HTML
<div class="card form-section">
    <div class="card-header bg-success text-white">
        <h5 class="card-title">Create Event</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="create_event">
            <div class="mb-3">
                <label for="eventName" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="eventName" name="eventName" placeholder="Enter event name" required>
            </div>
            <div class="mb-3">
                <label for="eventDate" class="form-label">Event Date</label>
                <input type="date" class="form-control" id="eventDate" name="eventDate" required>
            </div>
            <div class="mb-3">
                <label for="locationName" class="form-label">Location Name</label>
                <input type="text" class="form-control" id="locationName" name="locationName" placeholder="Enter location name" required>
            </div>
            <div class="mb-3">
                <label for="locationParentId" class="form-label">Parent Location ID (Optional)</label>
                <input type="number" class="form-control" id="locationParentId" name="locationParentId" placeholder="Enter parent location ID">
            </div>
            <div class="mb-3">
                <label for="locationLevel" class="form-label">Location Level</label>
                <select class="form-select" id="locationLevel" name="locationLevel" required>
                    <option value="" disabled selected>Select a level</option>
                    <option value="Country">Country</option>
                    <option value="State">State</option>
                    <option value="City">City</option>
                    <option value="Neighborhood">Neighborhood</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="eventDescription" class="form-label">Event Description</label>
                <textarea class="form-control" id="eventDescription" name="eventDescription" rows="3" placeholder="Provide a brief description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="reqCooks" class="form-label">Required Cooks</label>
                <input type="number" class="form-control" id="reqCooks" name="reqCooks" placeholder="Enter number of cooks required" required>
            </div>
            <div class="mb-3">
                <label for="reqForDelivery" class="form-label">Required Delivery Staff</label>
                <input type="number" class="form-control" id="reqForDelivery" name="reqForDelivery" placeholder="Enter number of delivery staff required" required>
            </div>
            <div class="mb-3">
                <label for="reqCoordinators" class="form-label">Required Coordinators</label>
                <input type="number" class="form-control" id="reqCoordinators" name="reqCoordinators" placeholder="Enter number of coordinators required" required>
            </div>
            <button type="submit" class="btn btn-success">Create Event</button>
        </form>
    </div>
</div>
HTML;
    }
}
