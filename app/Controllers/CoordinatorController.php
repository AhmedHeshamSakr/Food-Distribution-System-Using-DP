<?php
require_once 'Coordinator.php';

class CoordinatorController
{
    private Coordinator $coordinator;

    public function __construct(Person $user)
    {
        $this->coordinator = new Coordinator($user);
    }

    // Assign coordinator to an event
    public function assignToEvent(int $eventID): void
    {
        if ($this->coordinator->assignCoordinatorToEvent($eventID)) {
            echo json_encode([
                'status' => 'success',
                'message' => "Coordinator successfully assigned to event ID $eventID."
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "Failed to assign coordinator to event ID $eventID."
            ]);
        }
    }

    // Get all events assigned to the coordinator
    public function getAssignedEvents(): void
    {
        $events = $this->coordinator->getAssignedEvents();

        if (!empty($events)) {
            echo json_encode([
                'status' => 'success',
                'data' => $events
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No events assigned to this coordinator.'
            ]);
        }
    }

    // Get details of a specific event
    public function getEventDetails(int $eventID): void
    {
        $event = $this->coordinator->getEventDetails($eventID);

        if ($event) {
            echo json_encode([
                'status' => 'success',
                'data' => $event
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "No event found with ID $eventID."
            ]);
        }
    }

    // Update coordinator user type (if necessary)
    public function updateUserType(int $userTypeID): void
    {
        if ($this->coordinator->setUserTypeID($userTypeID)) {
            echo json_encode([
                'status' => 'success',
                'message' => "User type updated to $userTypeID."
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update user type.'
            ]);
        }
    }
}
