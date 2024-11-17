<?php

class EventView
{
    public function renderPageHeader(): void
    {
        echo "<!DOCTYPE html><html><head><title>Events</title></head><body>";
        echo "<h1>All Events</h1>";
    }

    public function renderEventList(array $events): void
    {
        if (empty($events)) {
            echo "<p>No events available at the moment.</p>";
            return;
        }

        echo "<table border='1' cellspacing='0' cellpadding='10'>";
        echo "<tr>
                <th>Event ID</th>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Event Location</th>
                <th>Description</th>
                <th>Required Cooks</th>
                <th>Required Delivery Personnel</th>
                <th>Required Coordinators</th>
              </tr>";

        foreach ($events as $event) {
            $location = $event->getEventLocation();
            echo "<tr>
                    <td>{$event->getEventID()}</td>
                    <td>{$event->getEventName()}</td>
                    <td>{$event->getEventDate()}</td>
                    <td>{$location->getAddressLine()}</td>
                    <td>{$event->getEventDescription()}</td>
                    <td>{$event->getReqCooks()}</td>
                    <td>{$event->getReqForDelivery()}</td>
                    <td>{$event->getReqCoordinators()}</td>
                  </tr>";
        }
        echo "</table>";
    }

    public function renderPageFooter(): void
    {
        echo "</body></html>";
    }
}