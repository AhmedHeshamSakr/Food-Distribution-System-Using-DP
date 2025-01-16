<?php

require_once __DIR__ . "/../Views/VolunteerView.php";
require_once __DIR__ . "/../Models/Volunteer.php";

require_once 'Database.php';


class VolunteerController {
    private $volunteer;
    private $db;
    private $view;

    // Constants for role flags (matching those in VolunteerRoles class)
    private const COOK_FLAG = 1;
    private const DELIVERY_FLAG = 2;
    private const COORDINATOR_FLAG = 4;

    public function __construct(Volunteer $volunteer = null) {
        // Initialize database connection
        $this->db = Database::getInstance()->getConnection();
        
        // Store the volunteer instance if provided
        $this->volunteer = $volunteer;
        
        // Initialize the view
        $this->view = new VolunteerView();
    }

    /**
     * Main method to display the volunteer dashboard
     * Handles the initial page load and displays available events
     */
    public function showDashboard() {
        // Render the volunteer dashboard view
        echo $this->view->render();
    }

    /**
     * Fetches all upcoming events with their volunteer requirements
     * This method is called via AJAX to get the event data
     */
    public function getUpcomingEvents() {
        try {
            // Query to get events with current volunteer counts
            $query = "SELECT 
                e.*,
                (SELECT COUNT(*) FROM cooking WHERE eventID = e.eventID) as current_cooks,
                (SELECT COUNT(*) FROM delivery WHERE eventID = e.eventID) as current_delivery,
                (SELECT COUNT(*) FROM coordinating WHERE eventID = e.eventID) as current_coordinators,
                e.required_cooks,
                e.required_delivery,
                e.required_coordinators
                FROM events e
                WHERE e.date >= CURDATE()
                ORDER BY e.date ASC";

            $result = mysqli_query($this->db, $query);
            $events = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Format events for JSON response
            return json_encode([
                'success' => true,
                'events' => $events
            ]);
        } catch (Exception $e) {
            return json_encode([
                'success' => false,
                'message' => 'Error fetching events'
            ]);
        }
    }

    /**
     * Handles role assignment for a volunteer
     * Updates multiple tables based on the selected role
     */
    public function assignRole() {
        // Get POST data
        $eventId = $_POST['eventId'] ?? null;
        $role = $_POST['role'] ?? null;
        $selected = $_POST['selected'] ?? false;

        if (!$eventId || !$role || !$this->volunteer) {
            return json_encode([
                'success' => false,
                'message' => 'Invalid request parameters'
            ]);
        }

        try {
            $this->db->begin_transaction();

            // First, handle the base volunteering record
            if ($selected) {
                // Insert into volunteering table
                $volunteeringQuery = "INSERT INTO volunteering (userID, eventID) 
                                    VALUES (?, ?)";
                $stmt = $this->db->prepare($volunteeringQuery);
                $userID = $this->volunteer->getUserID();
                $stmt->bind_param("ii", $userID, $eventId);
                $stmt->execute();
            }

            // Handle role-specific tables
            switch ($role) {
                case 'cook':
                    $success = $this->handleCookRole($eventId, $selected);
                    break;
                case 'deliveryGuy':
                    $success = $this->handleDeliveryRole($eventId, $selected);
                    break;
                case 'coordinator':
                    $success = $this->handleCoordinatorRole($eventId, $selected);
                    break;
                default:
                    throw new Exception('Invalid role specified');
            }

            // Update the volunteer's role flags
            $this->updateVolunteerRoles($role, $selected);

            $this->db->commit();
            return json_encode(['success' => true]);

        } catch (Exception $e) {
            $this->db->rollback();
            return json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handles the assignment of the cook role
     */
    private function handleCookRole($eventId, $selected): bool {
        if ($selected) {
            // Get the next available meal ID for this event
            $mealQuery = "SELECT mealID FROM meals WHERE eventID = ? AND cookID IS NULL LIMIT 1";
            $stmt = $this->db->prepare($mealQuery);
            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $result = $stmt->get_result();
            $meal = $result->fetch_assoc();

            if (!$meal) {
                throw new Exception('No available meals to cook');
            }

            // Insert into cooking table
            $cookQuery = "INSERT INTO cooking (cookID, mealID, mealsTaken, mealsCompleted) 
                         VALUES (?, ?, 0, 0)";
            $stmt = $this->db->prepare($cookQuery);
            $cookID = $this->volunteer->getUserID();
            $mealID = $meal['mealID'];
            $stmt->bind_param("ii", $cookID, $mealID);
            return $stmt->execute();
        } else {
            // Remove from cooking table
            $query = "DELETE FROM cooking WHERE cookID = ? AND mealID IN 
                     (SELECT mealID FROM meals WHERE eventID = ?)";
            $stmt = $this->db->prepare($query);
            $userID = $this->volunteer->getUserID();
            $stmt->bind_param("ii", $userID, $eventId);
            return $stmt->execute();
        }
    }

    /**
     * Handles the assignment of the delivery role
     */
    private function handleDeliveryRole($eventId, $selected): bool {
        if ($selected) {
            // Get an available vehicle
            $vehicleQuery = "SELECT vehicleID FROM vehicles WHERE isAvailable = 1 LIMIT 1";
            $result = mysqli_query($this->db, $vehicleQuery);
            $vehicle = mysqli_fetch_assoc($result);

            if (!$vehicle) {
                throw new Exception('No available vehicles');
            }

            // Insert into delivery table
            $query = "INSERT INTO delivery (userID, vehicleID) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            $userID = $this->volunteer->getUserID();
            $vehicleID = $vehicle['vehicleID'];
            $stmt->bind_param("ii", $userID, $vehicleID);
            return $stmt->execute();
        } else {
            // Remove from delivery table
            $query = "DELETE FROM delivery WHERE userID = ?";
            $stmt = $this->db->prepare($query);
            $userID = $this->volunteer->getUserID();
            $stmt->bind_param("i", $userID);
            return $stmt->execute();
        }
    }

    /**
     * Handles the assignment of the coordinator role
     */
    private function handleCoordinatorRole($eventId, $selected): bool {
        if ($selected) {
            // Insert into coordinating table
            $query = "INSERT INTO coordinating (userID, eventID) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            $userID = $this->volunteer->getUserID();
            $stmt->bind_param("ii", $userID, $eventId);
            return $stmt->execute();
        } else {
            // Remove from coordinating table
            $query = "DELETE FROM coordinating WHERE userID = ? AND eventID = ?";
            $stmt = $this->db->prepare($query);
            $userID = $this->volunteer->getUserID();
            $stmt->bind_param("ii", $userID, $eventId);
            return $stmt->execute();
        }
    }

    /**
     * Updates the volunteer's role flags in the user type ID
     */
    private function updateVolunteerRoles($role, $selected): bool {
        $roleFlag = $this->getRoleFlag($role);
        $currentTypeID = $this->volunteer->getUserTypeID();
        
        if ($selected) {
            // Add the role flag
            $newTypeID = $currentTypeID | $roleFlag;
        } else {
            // Remove the role flag
            $newTypeID = $currentTypeID & ~$roleFlag;
        }

        return $this->volunteer->setUserTypeID($newTypeID);
    }

    /**
     * Gets the corresponding flag for a role
     */
    private function getRoleFlag($role): int {
        switch ($role) {
            case 'cook':
                return self::COOK_FLAG;
            case 'deliveryGuy':
                return self::DELIVERY_FLAG;
            case 'coordinator':
                return self::COORDINATOR_FLAG;
            default:
                throw new Exception('Invalid role specified');
        }
    }
}