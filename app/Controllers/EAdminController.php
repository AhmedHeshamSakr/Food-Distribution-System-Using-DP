<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../Models/#a-Eadmin.php";
require_once __DIR__ . "/../Models/Address.php";
require_once __DIR__ . "/../Views/EAdminView.php";

class EventAdminController
{
    private EventAdminView $view;
    private EventAdmin $eventAdmin;

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
                    // case 'update_event':
                    //     $this->handleUpdateEvent();
                    //     break;
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

        $address = new Address(
            $eventData['locationName'],
            $eventData['locationParentId'] ?? null,
            $eventData['locationLevel']
        );

        if ($address->create()) {
            unset($eventData['eventID']);
            $this->eventAdmin->createEvent(
                $eventData['eventDate'],
                $address,
                $eventData['eventName'],
                $eventData['eventDescription'],
                (int) $eventData['reqCooks'],
                (int) $eventData['reqForDelivery'],
                (int) $eventData['reqCoordinators']
            );
        }
    }

    // private function handleUpdateEvent()
    // {
    //     $eventData = $_POST;

    //     $address = Address::read($eventData['locationId']);
    //     if ($address) {
    //         $address->setName($eventData['locationName']);
    //         $address->setParentId($eventData['locationParentId'] ?? null);
    //         $address->setLevel($eventData['locationLevel']);

    //         if ($address->update()) {
    //             $this->eventAdmin->updateEvent(
    //                 (int) $eventData['eventID'],
    //                 $eventData['eventDate'],
    //                 $address,
    //                 $eventData['eventName'],
    //                 $eventData['eventDescription'],
    //                 (int) $eventData['reqCooks'],
    //                 (int) $eventData['reqForDelivery'],
    //                 (int) $eventData['reqCoordinators']
    //             );
    //         }
    //     }
    // }

    private function handleDeleteEvent()
    {
        $eventID = (int) $_POST['eventID'];
        $this->eventAdmin->deleteEvent($eventID);
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
