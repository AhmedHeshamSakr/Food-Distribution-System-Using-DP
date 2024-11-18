<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../Models/Event.php";
require_once __DIR__ . "/../Views/EventView.php";

class EventController
{
    private EventView $view;

    public function __construct(EventView $view)
    {
        // Initialize the view
        $this->view = $view;
    }

    /**
     * Handle the request and render the list of all events.
     */
    public function handleRequest()
    {
        // Fetch all events using the fetchAll() method
        $allEvents = Event::fetchAll();

        // Render the page header
        $this->view->renderPageHeader();

        // Render the list of all events
        $this->view->renderEventList($allEvents);

        // Render the page footer
        $this->view->renderPageFooter();
    }
}