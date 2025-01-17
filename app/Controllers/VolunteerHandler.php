<?php
// Create a new file: volunteer_handler.php

require_once 'EventController.php';

class VolunteerHandler {
    public function processRequest(): void {
        header('Content-Type: application/json');
        
        try {
            // Verify user is logged in
            if (!isset($_SESSION['email'])) {
                throw new Exception('You must be logged in to volunteer');
            }
            
            // Validate required parameters
            if (!isset($_POST['eventID']) || !isset($_POST['staffType']) || !isset($_POST['action'])) {
                throw new Exception('Missing required parameters');
            }
            
            $eventID = (int)$_POST['eventID'];
            $staffType = $_POST['staffType'];
            $action = $_POST['action'];
            $userID = $_SESSION['userID']; // Assuming you store userID in session
            
            // Get the event
            $event = Event::fetchById($eventID);
            if (!$event) {
                throw new Exception('Event not found');
            }
            
            $success = false;
            $remainingSpots = 0;
            
            // Handle the volunteer/unvolunteer action
            if ($action === 'volunteer') {
                $success = $this->addVolunteer($userID, $eventID, $staffType);
                $message = 'Successfully volunteered!';
            } else {
                $success = $this->removeVolunteer($userID, $eventID, $staffType);
                $message = 'Successfully removed from volunteer position';
            }
            
            // Update remaining spots count based on staff type
            switch ($staffType) {
                case 'cook':
                    $remainingSpots = $event->getReqCooks();
                    break;
                case 'delivery':
                    $remainingSpots = $event->getReqForDelivery();
                    break;
                case 'coordinator':
                    $remainingSpots = $event->getReqCoordinators();
                    break;
            }
            
            echo json_encode([
                'success' => $success,
                'message' => $message,
                'remainingSpots' => $remainingSpots
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function addVolunteer(int $userID, int $eventID, string $staffType): bool {
        $db = Database::getInstance()->getConnection();
        
        // Determine which table to insert into based on staff type
        switch ($staffType) {
            case 'cook':
                $table = 'Cook';
                break;
            case 'delivery':
                $table = 'DeliveryGuyEvents';
                break;
            case 'coordinator':
                $table = 'coordinating';
                break;
            default:
                throw new Exception('Invalid staff type');
        }
        
        $query = "INSERT INTO {$table} (userID, eventID) VALUES (?, ?)";
        $stmt = $db->prepare($query);
        return $stmt->execute([$userID, $eventID]);
    }
    
    private function removeVolunteer(int $userID, int $eventID, string $staffType): bool {
        $db = Database::getInstance()->getConnection();
        
        // Determine which table to delete from based on staff type
        switch ($staffType) {
            case 'cook':
                $table = 'Cook';
                break;
            case 'delivery':
                $table = 'DeliveryGuyEvents';
                break;
            case 'coordinator':
                $table = 'coordinating';
                break;
            default:
                throw new Exception('Invalid staff type');
        }
        
        $query = "DELETE FROM {$table} WHERE userID = ? AND eventID = ?";
        $stmt = $db->prepare($query);
        return $stmt->execute([$userID, $eventID]);
    }
}

// Initialize and process the request
$handler = new VolunteerHandler();
$handler->processRequest();