<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../Models/Event.php";
require_once __DIR__ . "/../Views/EventView.php";
require_once __DIR__ . "/../Views/VolunteerView.php";
require_once __DIR__ . "/../Models/Volunteer.php";
require_once __DIR__ . "/../Models/ControlePanelDPs.php";

/**
 * EventController Class
 * Handles all event-related operations and implements the Observer pattern
 * to receive notifications from Event objects about requirement fulfillment
 */
class EventController implements Observer {
    private EventView $view;
    private VolunteerView $volunteerView;
    private bool $isVolunteerContext;
    private ControlPanel $controlPanel;
    private EventReceiver $eventReceiver;

    private function checkAuthentication(): void {
        if (!isset($_SESSION['email'])) {
            header("Location: login.php");
            exit();
        }
    }
    
    /**
     * Constructor now initializes both views and determines the context
     * based on the user's role or access path
     */
    public function __construct(bool $isVolunteerContext = false) {
        $this->view = new EventView();
        $this->volunteerView = new VolunteerView();
        $this->isVolunteerContext = $isVolunteerContext;
        $this->eventReceiver = new EventReceiver(new Event());

        $this->controlPanel = new ControlPanel();
    }

    /**
     * Enhanced request handler that routes requests based on context
     * and provides appropriate view rendering
     */
    public function handleRequest(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        try {
            $this->checkAuthentication();
            ob_start();
            $action = $_GET['action'] ?? 'list';
            // Render appropriate header based on context
            if ($this->isVolunteerContext) {
                $this->volunteerView->renderPageHeader();
            } else {
                $this->view->renderPageHeader();
            }
            
            switch ($action) {
                // Original admin actions
                case 'list':
                    $this->handleList();
                    break;
                    
                case 'upcoming':
                    // Now handles both admin and volunteer contexts
                    $this->handleUpcomingEvents();
                    break;
                    
                case 'details':
                    // Enhanced to handle both contexts
                    $this->handleEventDetails();
                    break;

                case 'create':
                    $this->validateAdminAccess();
                    $this->handleCreateEvent();
                    break;

                case 'delete':
                    $this->validateAdminAccess();
                    $this->handleDeleteEvent();
                    break;

                case 'update':
                    $this->validateAdminAccess();
                    $this->handleUpdateEvent();
                    break;
                    
                case 'assign':
                    $this->validateAdminAccess();
                    $this->handleAssignStaff();
                    break;

                // New volunteer-specific actions
                case 'volunteer':
                    $this->handleAjaxVolunteerSignup();
                    break;
                    
                default:
                    throw new Exception("Invalid action specified");
            }
            
            // Render appropriate footer based on context
            if ($this->isVolunteerContext) {
                $this->volunteerView->renderPageFooter();
            } else {
                $this->view->renderPageFooter();
            }
            
            ob_end_flush();
            
        } catch (Exception $e) {
            ob_end_clean();
            if ($this->isVolunteerContext) {
                $this->volunteerView->renderPageHeader();
                $this->volunteerView->renderError($e->getMessage());
                $this->volunteerView->renderPageFooter();
            } else {
                $this->view->renderPageHeader();
                $this->view->renderError($e->getMessage());
                $this->view->renderPageFooter();
            }
        }
    }

    /**
     * Enhanced upcoming events handler that considers the context
     * and renders appropriate view
     */
    private function handleUpcomingEvents(): void {
        try {
            $events = Event::fetchUpcomingEvents();
            foreach ($events as $event) {
                $event->addObserver($this);
            }
            
            if ($this->isVolunteerContext) {
                $this->volunteerView->renderUpcomingEvents($events);
            } else {
                $this->view->renderEventList($events);
            }
        } catch (Exception $e) {
            throw new Exception("Error fetching upcoming events: " . $e->getMessage());
        }
    }

    /**
     * Enhanced event details handler that renders appropriate view
     * based on context
     */
    private function handleEventDetails(): void {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new Exception("Invalid event ID provided");
        }
        
        try {
            $event = Event::fetchById((int)$_GET['id']);
            if (!$event) {
                throw new Exception("Event not found");
            }
            
            $event->addObserver($this);
            
            if ($this->isVolunteerContext) {
                $this->volunteerView->renderEventDetails($event);
            } else {
                $this->view->renderEventDetails($event);
            }
        } catch (Exception $e) {
            throw new Exception("Error fetching event details: " . $e->getMessage());
        }
    }


    /**
     * Handles AJAX volunteer signup requests and returns JSON response
     */
    private function handleAjaxVolunteerSignup(): void {
    header('Content-Type: application/json');
    
    try {
        error_log("=== Starting volunteer signup ===");
        error_log("POST data received: " . print_r($_POST, true));
        error_log("Session state: " . print_r($_SESSION, true));
        
        // Check session
        if (!isset($_SESSION['email'])) {
            throw new Exception("Session expired - please log in again");
        }
        
        // Validate POST data
        if (!isset($_POST['eventID'])) {
            throw new Exception("Missing event ID");
        }
        if (!isset($_POST['staffType'])) {
            throw new Exception("Missing staff type");
        }
        
        $userId = $this->getUserDetails($_SESSION['email']);
        error_log("User ID retrieved: " . $userId);
        
        $event = Event::fetchById((int)$_POST['eventID']);
        error_log("Event fetched: " . ($event ? "yes" : "no"));
        
        // Continue with signup process...
        $this->handleVolunteerSignup();
        
        error_log("=== Volunteer signup completed successfully ===");
        
        echo json_encode([
            'success' => true,
            'message' => 'Successfully registered as volunteer'
        ]);
        
    } catch (Exception $e) {
        error_log("=== Volunteer signup failed ===");
        error_log("Error message: " . $e->getMessage());
        error_log("Error trace: " . $e->getTraceAsString());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

    public function getUserDetails(string $email): int {

        
        // Query your database to get user details
        $query = "SELECT userID FROM Person WHERE email = '$email'";
        $result= run_select_query($query);
        // Return array with user details
        return (int)$result[0]['userID'];
    }

    /**
     * Modified volunteer signup handler to work with both AJAX and regular requests
     */
    private function handleVolunteerSignup(): void {
        // Validate POST data
        if (!isset($_POST['eventID']) || !isset($_POST['staffType']) || !isset($_POST['userID'])) {
            throw new Exception("Missing required parameters");
        }
    
        // Validate user session
        if (!isset($_SESSION['email'])) {
            throw new Exception("User must be logged in");
        }
    
        try {
            // Get user ID using email
            $userEmail = $_SESSION['email'];
            $userId = $this->getUserDetails($userEmail);
            
            if (!$userId) {
                throw new Exception("Invalid user");
            }
    
            // Fetch event and validate it exists
            $event = Event::fetchById((int)$_POST['eventID']);
            if (!$event) {
                throw new Exception("Event not found");
            }
    
            // Get the volunteer record
            $volunteer = Volunteer::fetchById((int)$_POST['userID']);
            if (!$volunteer) {
                throw new Exception("Volunteer record not found");
            }
    
            // Handle role assignment based on staff type
            $staffType = $_POST['staffType'];
            $roleAssigned = false;
    
            switch ($staffType) {
                case 'cook':
                    if ($event->getReqCooks() > 0) {
                        $volunteer = new Cook($volunteer);
                        $event->assignCook();
                        $roleAssigned = true;
                    }
                    break;
    
                case 'delivery':
                    if ($event->getReqForDelivery() > 0) {
                        $vehicle = new Vehicle(0); // Default vehicle
                        $volunteer = new DeliveryGuy($volunteer, $vehicle);
                        $event->assignDelivery();
                        $roleAssigned = true;
                    }
                    break;
    
                case 'coordinator':
                    if ($event->getReqCoordinators() > 0) {
                        $volunteer = new Coordinator($volunteer);
                        $event->assignCoordinator();
                        $roleAssigned = true;
                    }
                    break;
    
                default:
                    throw new Exception("Invalid staff type");
            }
    
            if (!$roleAssigned) {
                throw new Exception("No positions available for this role");
            }
    
            // Save the role assignment
            $volunteer->chooseRole();
            
            // Update event
            if (!$event->update()) {
                throw new Exception("Failed to update event");
            }
    
        } catch (Exception $e) {
            // Log error for debugging
            error_log("Volunteer signup error: " . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Helper method to validate admin access
     * Throws exception if accessed in volunteer context
     */
    private function validateAdminAccess(): void {
        if ($this->isVolunteerContext) {
            throw new Exception("Access denied: Administrative action not allowed in volunteer context");
        }
    }
    /**
     * Receives and handles notifications from observed Event objects
     * Stores notifications in the session for display to the user
     */
    public function update(string $message): void {
        $_SESSION['notification'] = $message;
    }

   

    /**
     * Retrieves and displays all events, registering the controller as an observer
     */
    private function handleList(): void {
        try {
            $events = Event::fetchAll();
            foreach ($events as $event) {
                $event->addObserver($this);
            }
            $this->view->renderEventList($events);
        } catch (Exception $e) {
            throw new Exception("Error fetching events: " . $e->getMessage());
        }
    }

  

/**
 * Handles creating a new event with proper address initialization and validation
 */
private function handleCreateEvent(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // If not a POST request, render the form and exit
        $this->view->renderEventForm();
        return;
    }

    try {
        // Validate all input parameters first
        $params = $this->validateInputParams($_POST, [
            'eventName',
            'eventDate',
            'eventDescription',
            'addressName',
            'addressLevel',
            'parentId',
            'reqCooks',
            'reqForDelivery',
            'reqCoordinators'
        ]);

        // Validate address level before proceeding
        $validLevels = ['Country', 'State', 'City', 'Neighborhood'];
        if (!in_array($params['addressLevel'], $validLevels)) {
            throw new Exception("Invalid address level. Must be one of: " . implode(', ', $validLevels));
        }

        try {
            // Create and validate the Address object
            $address = new Address(
                $params['addressName'],
                !empty($params['parentId']) ? (int)$params['parentId'] : null,
                $params['addressLevel']
            );

            // Ensure address is created in database before proceeding
            if (!$address->create()) {
                throw new Exception("Failed to create address record");
            }

            // Verify the address was created and has an ID
            if ($address->getId() <= 0) {
                throw new Exception("Address creation failed - no ID returned");
            }

            // Create the command with validated data
            $createEventCommand = new CreateEventCommand($this->eventReceiver);
            
            // Set the event data for the command
            $createEventCommand->setEventData([
                'eventDate' => $params['eventDate'],
                'eventLocation' => $address,
                'eventName' => $params['eventName'],
                'eventDescription' => $params['eventDescription'],
                'reqCooks' => (int)$params['reqCooks'],
                'reqForDelivery' => (int)$params['reqForDelivery'],
                'reqCoordinators' => (int)$params['reqCoordinators']
            ]);

            // Set and execute the command using the control panel
            $this->controlPanel->setCommand($createEventCommand);
            $success = $this->controlPanel->executeCommand();

            if (!$success) {
                // If command execution fails, clean up the address
                $address->delete();
                throw new Exception("Failed to create event");
            }

            // Set success notification and redirect
            $_SESSION['notification'] = "Event created successfully";
            header("Location: ?action=list");
            exit;

        } catch (Exception $e) {
            // Clean up any partially created data
            if (isset($address) && $address->getId() > 0) {
                $address->delete();
            }
            throw $e;
        }
    } catch (Exception $e) {
        $this->view->renderError($e->getMessage());
    }
}

/**
 * Handles updating an event with proper address handling and validation
 */
private function handleUpdateEvent(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $params = $this->validateInputParams($_POST, [
                'eventID',
                'eventName', 
                'eventDate', 
                'eventDescription',
                'addressName',
                'addressLevel',
                'parentId',
                'reqCooks',
                'reqForDelivery',
                'reqCoordinators'
            ]);

            // Validate address level
            $validLevels = ['Country', 'State', 'City', 'Neighborhood'];
            if (!in_array($params['addressLevel'], $validLevels)) {
                throw new Exception("Invalid address level. Must be one of: " . implode(', ', $validLevels));
            }

            // Start a transaction if your database supports it
            // DB::beginTransaction();

            try {
                // If we have an existing address ID, load it first
                $address = null;
                if (!empty($params['addressId'])) {
                    $address = Address::read((int)$params['addressId']);
                    if (!$address) {
                        throw new Exception("Cannot find existing address");
                    }
                    
                    // Update existing address
                    $address->setName($params['addressName']);
                    $address->setParentId(!empty($params['parentId']) ? (int)$params['parentId'] : null);
                    $address->setLevel($params['addressLevel']);
                } else {
                    // Create new address
                    $address = new Address(
                        $params['addressName'],
                        !empty($params['parentId']) ? (int)$params['parentId'] : null,
                        $params['addressLevel']
                    );
                    if (!$address->create()) {
                        throw new Exception("Failed to create new address");
                    }
                }

                // Now create/update the event
                $event = new Event(
                    (int)$params['eventID'],
                    $params['eventDate'],
                    $address,
                    $params['eventName'],
                    $params['eventDescription'],
                    (int)$params['reqCooks'],
                    (int)$params['reqForDelivery'],
                    (int)$params['reqCoordinators']
                );

                if (!$event->update()) {
                    throw new Exception("Failed to update event");
                }

                // If using transactions: DB::commit();

                $_SESSION['notification'] = "Event updated successfully";
                header("Location: ?action=list");
                exit;

            } catch (Exception $e) {
                // If using transactions: DB::rollback();
                throw $e;
            }

        } catch (Exception $e) {
            $this->view->renderError($e->getMessage());
        }
    } else {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new Exception("Invalid event ID provided");
        }

        try {
            $event = Event::fetchById((int)$_GET['id']);
            if (!$event) {
                throw new Exception("Event not found");
            }

            $this->view->renderEventForm($event);

        } catch (Exception $e) {
            throw new Exception("Error preparing event update: " . $e->getMessage());
        }
    }
}
    /**
     * Handles event deletion with proper validation
     */
    private function handleDeleteEvent(): void {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new Exception("Invalid event ID provided");
        }
        
        try {
            $event = new Event();
            $event->setEventID((int)$_GET['id']);

            if (!$event->delete()) {
                throw new Exception("Failed to delete event");
            }

            $_SESSION['notification'] = "Event deleted successfully";
            header("Location: ?action=list");
            exit;

        } catch (Exception $e) {
            throw new Exception("Error deleting event: " . $e->getMessage());
        }
    }


    /**
     * Handles staff assignment to events (new functionality)
     */
    private function handleAssignStaff(): void {
        if (!isset($_POST['eventID']) || !isset($_POST['staffType'])) {
            throw new Exception("Missing required parameters for staff assignment");
        }

        try {
            $event = new Event();
            $event->setEventID((int)$_POST['eventID']);
            $event = $event->read();

            if (!$event) {
                throw new Exception("Event not found");
            }

            $event->addObserver($this);

            switch ($_POST['staffType']) {
                case 'cook':
                    $event->assignCook();
                    break;
                case 'delivery':
                    $event->assignDelivery();
                    break;
                case 'coordinator':
                    $event->assignCoordinator();
                    break;
                default:
                    throw new Exception("Invalid staff type");
            }

            if (!$event->update()) {
                throw new Exception("Failed to update event staff assignment");
            }

            $_SESSION['notification'] = "Staff assigned successfully";
            header("Location: ?action=details&id=" . $event->getEventID());
            exit;

        } catch (Exception $e) {
            throw new Exception("Error assigning staff: " . $e->getMessage());
        }
    }

    /**
     * Validates and sanitizes input parameters
     */
    private function validateInputParams(array $params, array $required = []): array {
        $sanitized = [];
        
        foreach ($required as $param) {
            if (!isset($params[$param]) || trim($params[$param]) === '') {
                throw new Exception("Missing required parameter: $param");
            }
        }
        
        foreach ($params as $key => $value) {
            $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $sanitized;
    }

   
}