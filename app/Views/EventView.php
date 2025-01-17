<?php

class EventView {
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
            <title>Event Management System</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
                <div class="container">
                    <a class="navbar-brand" href="?action=list&view=admin">Event Manager</a>
                    <div class="navbar-nav">
                        <a class="nav-link" href="?action=list">All Events</a>
                        <a class="nav-link" href="?action=upcoming">Upcoming Events</a>
                        <a class="nav-link" href="?action=create">Create Event</a>
                    </div>
                </div>
            </nav>
            <div class="container">
                <?php
                if (isset($_SESSION['notification'])) {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['notification']) . '</div>';
                    unset($_SESSION['notification']);
                }
                ?>
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
     * Renders a list of events in a table format
     */
    public function renderEventList(array $events): void {
        ?>
        <h2 class="mb-4">Events</h2>
        <?php if (empty($events)): ?>
            <div class="alert alert-info">No events found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Required Staff</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <a href="?action=details&id=<?= $event->getEventID() ?>">
                                        <?= htmlspecialchars($event->getEventName()) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($event->getEventDate()) ?></td>
                                <td><?= htmlspecialchars($event->getEventLocation()->getName()) ?></td>
                                <td>
                                    <small>
                                        Cooks: <?= $event->getReqCooks() ?> |
                                        Delivery: <?= $event->getReqForDelivery() ?> |
                                        Coordinators: <?= $event->getReqCoordinators() ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?action=update&id=<?= $event->getEventID() ?>" 
                                           class="btn btn-sm btn-primary">Edit</a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger"
                                                onclick="confirmDelete(<?= $event->getEventID() ?>)">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <script>
            function confirmDelete(eventId) {
                if (confirm('Are you sure you want to delete this event?')) {
                    window.location.href = `?action=delete&id=${eventId}`;
                }
            }
            </script>
        <?php endif;
    }

    /**
     * Renders detailed information about a specific event
     */
    public function renderEventDetails(Event $event): void {
        ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2><?= htmlspecialchars($event->getEventName()) ?></h2>
                <div class="btn-group">
                    <a href="?action=update&id=<?= $event->getEventID() ?>" 
                       class="btn btn-primary">Edit Event</a>
                    <button type="button" 
                            class="btn btn-danger"
                            onclick="confirmDelete(<?= $event->getEventID() ?>)">Delete Event</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
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
                                <h5>Cooks Needed: <?= $event->getReqCooks() ?></h5>
                                <form action="?action=assign" method="post" class="mb-2">
                                    <input type="hidden" name="eventID" value="<?= $event->getEventID() ?>">
                                    <input type="hidden" name="staffType" value="cook">
                                    <button type="submit" class="btn btn-sm btn-success"
                                            <?= $event->getReqCooks() <= 0 ? 'disabled' : '' ?>>
                                        Assign Cook
                                    </button>
                                </form>

                                <h5>Delivery Staff Needed: <?= $event->getReqForDelivery() ?></h5>
                                <form action="?action=assign" method="post" class="mb-2">
                                    <input type="hidden" name="eventID" value="<?= $event->getEventID() ?>">
                                    <input type="hidden" name="staffType" value="delivery">
                                    <button type="submit" class="btn btn-sm btn-success"
                                            <?= $event->getReqForDelivery() <= 0 ? 'disabled' : '' ?>>
                                        Assign Delivery Staff
                                    </button>
                                </form>

                                <h5>Coordinators Needed: <?= $event->getReqCoordinators() ?></h5>
                                <form action="?action=assign" method="post" class="mb-2">
                                    <input type="hidden" name="eventID" value="<?= $event->getEventID() ?>">
                                    <input type="hidden" name="staffType" value="coordinator">
                                    <button type="submit" class="btn btn-sm btn-success"
                                            <?= $event->getReqCoordinators() <= 0 ? 'disabled' : '' ?>>
                                        Assign Coordinator
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function confirmDelete(eventId) {
            if (confirm('Are you sure you want to delete this event?')) {
                window.location.href = `?action=delete&id=${eventId}`;
            }
        }
        </script>
        <?php
    }

    /**
     * Renders error messages in a standardized format
     */
    public function renderError(string $message): void {
        ?>
        <div class="alert alert-danger">
            <strong>Error:</strong> <?= htmlspecialchars($message) ?>
        </div>
        <?php
    }

    /**
     * Renders the event creation/edit form
     */
    public function renderEventForm(?Event $event = null): void {
        $isEdit = $event !== null;
        ?>
        <div class="card">
            <div class="card-header">
                <h2><?= $isEdit ? 'Edit Event' : 'Create New Event' ?></h2>
            </div>
            <div class="card-body">
                <form action="?action=<?= $isEdit ? 'update' : 'create' ?>" method="post">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="eventID" value="<?= $event->getEventID() ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="eventName" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="eventName" name="eventName"
                               value="<?= $isEdit ? htmlspecialchars($event->getEventName()) : '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="eventDate" class="form-label">Event Date</label>
                        <input type="date" class="form-control" id="eventDate" name="eventDate"
                               value="<?= $isEdit ? htmlspecialchars($event->getEventDate()) : '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" name="eventDescription" rows="3"
                                  required><?= $isEdit ? htmlspecialchars($event->getEventDescription()) : '' ?></textarea>
                    </div>

                    <!-- Address Fields -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Location Details</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($isEdit): ?>
                                <input type="hidden" name="addressId" 
                                       value="<?= $event->getEventLocation()->getId() ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="addressName" class="form-label">Location Name</label>
                                <input type="text" class="form-control" id="addressName" name="addressName"
                                       value="<?= $isEdit ? htmlspecialchars($event->getEventLocation()->getName()) : '' ?>" 
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="addressLevel" class="form-label">Location Level</label>
                                <select class="form-control" id="addressLevel" name="addressLevel" required>
                                    <?php
                                    $levels = ['Country', 'State', 'City', 'Neighborhood'];
                                    foreach ($levels as $level) {
                                        $selected = $isEdit && $event->getEventLocation()->getLevel() === $level ? 'selected' : '';
                                        echo "<option value=\"$level\" $selected>$level</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="parentId" class="form-label">Parent Location ID (optional)</label>
                                <input type="number" class="form-control" id="parentId" name="parentId"
                                       value="<?= $isEdit ? $event->getEventLocation()->getParentId() : '' ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Staff Requirements -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Staff Requirements</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="reqCooks" class="form-label">Required Cooks</label>
                                    <input type="number" class="form-control" id="reqCooks" name="reqCooks"
                                           value="<?= $isEdit ? $event->getReqCooks() : '0' ?>" min="0" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="reqForDelivery" class="form-label">Required Delivery Staff</label>
                                    <input type="number" class="form-control" id="reqForDelivery" name="reqForDelivery"
                                           value="<?= $isEdit ? $event->getReqForDelivery() : '0' ?>" min="0" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="reqCoordinators" class="form-label">Required Coordinators</label>
                                    <input type="number" class="form-control" id="reqCoordinators" name="reqCoordinators"
                                           value="<?= $isEdit ? $event->getReqCoordinators() : '0' ?>" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <?= $isEdit ? 'Update Event' : 'Create Event' ?>
                        </button>
                        <a href="?action=list" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
}