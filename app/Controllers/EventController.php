<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../Models/Event.php";
require_once __DIR__ . "/../Views/EventView.php";

/**
 * EventController Class
 * Handles all event-related operations and implements the Observer pattern
 * to receive notifications from Event objects about requirement fulfillment
 */
class EventController implements Observer {
    private EventView $view;

    /**
     * Constructor initializes the view component
     */
    public function __construct() {
        $this->view = new EventView();
    }

    /**
     * Receives and handles notifications from observed Event objects
     * Stores notifications in the session for display to the user
     */
    public function update(string $message): void {
        $_SESSION['notification'] = $message;
    }

    /**
     * Main request handler that coordinates all event-related actions
     * Uses output buffering to ensure clean error handling
     */
    public function handleRequest(): void {
        try {
            ob_start();
            
            $action = $_GET['action'] ?? 'list';
            
            $this->view->renderPageHeader();
            
            switch ($action) {
                case 'list':
                    $this->handleList();
                    break;
                    
                case 'upcoming':
                    $this->handleUpcomingEvents();
                    break;
                    
                case 'details':
                    $this->handleEventDetails();
                    break;

                case 'create':
                    $this->handleCreateEvent();
                    break;

                case 'delete':
                    $this->handleDeleteEvent();
                    break;

                case 'update':
                    $this->handleUpdateEvent();
                    break;
                    
                case 'assign':
                    $this->handleAssignStaff();
                    break;
                    
                default:
                    throw new Exception("Invalid action specified");
            }
            
            $this->view->renderPageFooter();
            ob_end_flush();
            
        } catch (Exception $e) {
            ob_end_clean();
            $this->view->renderPageHeader();
            $this->view->renderError($e->getMessage());
            $this->view->renderPageFooter();
        }
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
     * Retrieves and displays upcoming events, registering the controller as an observer
     */
    private function handleUpcomingEvents(): void {
        try {
            $events = Event::fetchUpcomingEvents();
            foreach ($events as $event) {
                $event->addObserver($this);
            }
            $this->view->renderEventList($events);
        } catch (Exception $e) {
            throw new Exception("Error fetching upcoming events: " . $e->getMessage());
        }
    }

    /**
     * Displays detailed information for a specific event
     */
    private function handleEventDetails(): void {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new Exception("Invalid event ID provided");
        }
        
        try {
            $event = new Event();
            $event->setEventID((int)$_GET['id']);
            $event = $event->read();
            
            if (!$event) {
                throw new Exception("Event not found");
            }
            
            $event->addObserver($this);
            $this->view->renderEventDetails($event);
            
        } catch (Exception $e) {
            throw new Exception("Error fetching event details: " . $e->getMessage());
        }
    }

    /**
     * Handles the creation of new events, including address creation
     */
    /**
 * Handles creating a new event with proper address initialization
 */
private function handleCreateEvent(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
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
            
            // First create the Address object and validate it
            $address = new Address(
                $params['addressName'],
                !empty($params['parentId']) ? (int)$params['parentId'] : null,
                $params['addressLevel']
            );

            // Ensure the address level is set
            if (empty($address->getLevel())) {
                throw new Exception("Address level cannot be empty");
            }

            // Create the event with the validated address
            $event = new Event(
                null,
                $params['eventDate'],
                $address,
                $params['eventName'],
                $params['eventDescription'],
                (int)$params['reqCooks'],
                (int)$params['reqForDelivery'],
                (int)$params['reqCoordinators']
            );

            if (!$event->create()) {
                throw new Exception("Failed to create event");
            }

            $_SESSION['notification'] = "Event created successfully";
            header("Location: ?action=list");
            exit;

        } catch (Exception $e) {
            $this->view->renderError($e->getMessage());
        }
    } else {
        $this->view->renderEventForm();
    }
}

/**
 * Handles updating an event with proper address handling
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

            // Create and validate the Address object
            $address = new Address(
                $params['addressName'],
                !empty($params['parentId']) ? (int)$params['parentId'] : null,
                $params['addressLevel']
            );

            // Ensure the address level is set
            if (empty($address->getLevel())) {
                throw new Exception("Address level cannot be empty");
            }

            // If updating an existing address, set its ID
            if (isset($params['addressId'])) {
                $address->setId((int)$params['addressId']);
            }

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

            $_SESSION['notification'] = "Event updated successfully";
            header("Location: ?action=list");
            exit;

        } catch (Exception $e) {
            $this->view->renderError($e->getMessage());
        }
    } else {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new Exception("Invalid event ID provided");
        }

        try {
            $event = new Event();
            $event->setEventID((int)$_GET['id']);
            $event = $event->read();

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