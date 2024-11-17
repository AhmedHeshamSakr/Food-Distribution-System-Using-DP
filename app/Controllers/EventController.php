<?php

require_once __DIR__ . "/../models/Event.php";

class EventController
{
    // Fetch and return all events
    public function getAllEvents(): array
    {
        $query = "SELECT * FROM Event";
        $results = run_select_query($query);
        $events = [];

        foreach ($results as $row) {
            $eventLocation = Address::read($row['eventLocation']);
            $events[] = new Event(
                $row['eventID'],
                $row['eventDate'],
                $eventLocation,
                $row['name'],
                $row['eventDescription']
            );
        }

        return $events;
    }

    // Method to display the event view
    public function displayEvents(): void
    {
        $events = $this->getAllEvents();
        include __DIR__ . "/../Views/EventView.php";
    }
}
