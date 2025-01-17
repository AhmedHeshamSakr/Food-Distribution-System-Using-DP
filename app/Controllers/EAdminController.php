<?php
// EAdminController.php - Updated version

require_once __DIR__ . "/../Models/#a-Eadmin.php";
require_once __DIR__ . "/../Models/Address.php";
require_once __DIR__ . "/../Views/EAdminView.php";
require_once __DIR__ . "/../Models/ControlePanelDPs.php";

class EventAdminController
{
    private EventAdminView $view;
    private EventAdmin $eventAdmin;
    private ControlPanel $controlPanel;
    private FacadReciver $facadReciver;

    public function __construct(EventAdminView $view)
    {
        session_start();

        $email = $_SESSION['email'] ?? null;

        if (!$email) {
            throw new Exception("Admin user is not logged in.");
        }

        $query = "SELECT * FROM person WHERE email = ? LIMIT 1";
        $stmt = Database::getInstance()->getConnection()->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
            $this->eventAdmin = new EventAdmin(
                $userData['firstName'],
                $userData['lastName'],
                $userData['email'],
                $userData['phoneNo']
            );
        } else {
            throw new Exception("Admin user not found in the database.");
        }

        $this->view = $view;
        
        // Initialize the Control Panel components
        $this->facadReciver = new FacadReciver(
            new Event(),
            new ReportingData('', '', '', ''),
            new Badges(),
            new Reporter()
        );
        $this->controlPanel = new ControlPanel();
    }

    public function handleRequest()
    {
        $action = $_POST['action'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                switch ($action) {
                    case 'create_event':
                        $this->handleCreateEvent();
                        break;
                    case 'delete_event':
                        $this->handleDeleteEvent();
                        break;
                }

                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } catch (Exception $e) {
                $this->view->renderError($e->getMessage());
            }
        }

        $this->renderPage();
    }

    private function handleCreateEvent()
    {
        $eventData = $_POST;

        if (empty($eventData['eventDate']) || empty($eventData['eventName']) || empty($eventData['eventDescription'])) {
            throw new Exception("Missing required event fields.");
        }

        $address = new Address(
            $eventData['locationName'] ?? '',
            $eventData['locationParentId'] ?? null,
            $eventData['locationLevel'] ?? ''
        );

        if ($address->create()) {
            // Create the command
            $createEventCommand = new CreateEventCommand($this->facadReciver);
            
            // Set the event data
            $createEventCommand->setEventData([
                'eventDate' => $eventData['eventDate'],
                'eventLocation' => $address,
                'eventName' => $eventData['eventName'],
                'eventDescription' => $eventData['eventDescription'],
                'reqCooks' => (int)($eventData['reqCooks'] ?? 0),
                'reqForDelivery' => (int)($eventData['reqForDelivery'] ?? 0),
                'reqCoordinators' => (int)($eventData['reqCoordinators'] ?? 0)
            ]);

            // Set and execute the command
            $this->controlPanel->setCommand($createEventCommand);
            $this->controlPanel->executeCommand();
        } else {
            throw new Exception("Failed to create address for the event.");
        }
    }

    private function handleDeleteEvent()
    {
        $eventID = (int) ($_POST['eventID'] ?? 0);

        if ($eventID <= 0) {
            throw new Exception("Invalid event ID provided for deletion.");
        }

        if (!$this->eventAdmin->deleteEvent($eventID)) {
            throw new Exception("Failed to delete the event.");
        }
    }

    private function renderPage()
    {
        $this->view->renderPageHeader();
        $events = $this->eventAdmin->getAllEvents();
        $this->view->renderEventList($events);
        $this->view->renderCreateEventForm();
        $this->view->renderPageFooter();
    }
}