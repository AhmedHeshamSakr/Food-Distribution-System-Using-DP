<?php

require_once 'Event.php';
require_once 'ReportData.php';
require_once 'Reporter.php';
require_once 'Badges.php';
require_once 'Volunteer.php';

interface ICommand {
    public function execute();
}

class CreateEventCommand implements ICommand {
    private EventReceiver $eventReceiver;
    private ?array $eventData = null;

    public function __construct(EventReceiver $eventReceiver) {
        $this->eventReceiver = $eventReceiver;
    }

    public function setEventData(array $eventData): void {
        $this->eventData = $eventData;
    }

    public function execute(): void {
        if ($this->eventData === null) {
            throw new Exception("Event data must be set before executing this command.");
        }

        $this->eventReceiver->createEvent(
            $this->eventData['eventDate'],
            $this->eventData['eventLocation'],
            $this->eventData['eventName'],
            $this->eventData['eventDescription'],
            $this->eventData['reqCooks'],
            $this->eventData['reqForDelivery'],
            $this->eventData['reqCoordinators']
        );
    }
}

class ViewAllReportsCommand implements ICommand {
    private ReportReceiver $reportReceiver;

    public function __construct(ReportReceiver $reportReceiver) {
        $this->reportReceiver = $reportReceiver;
    }

    public function execute(): void {
        $reports = $this->reportReceiver->viewAllReports();
        print_r($reports); // Display reports or handle them as needed
    }
}

class UpdateReportStatusCommand implements ICommand {
    private ReportReceiver $reportReceiver;
    private ?int $reportID = null;
    private ?string $newStatus = null;

    public function __construct(ReportReceiver $reportReceiver) {
        $this->reportReceiver = $reportReceiver;
    }

    public function setReportDetails(int $reportID, string $newStatus): void {
        $this->reportID = $reportID;
        $this->newStatus = $newStatus;
    }

    public function execute(): void {
        if ($this->reportID === null || $this->newStatus === null) {
            throw new Exception("Report ID and new status must be set before executing this command.");
        }

        $this->reportReceiver->updateReportStatus($this->reportID, $this->newStatus);
    }
}

class GiveBadgeCommand implements ICommand {
    private BadgeReceiver $badgeReceiver;
    private ?Volunteer $volunteer = null;
    private ?Badges $badge = null;

    public function __construct(BadgeReceiver $badgeReceiver) {
        $this->badgeReceiver = $badgeReceiver;
    }

    public function setBadgeDetails(Volunteer $volunteer, Badges $badge): void {
        $this->volunteer = $volunteer;
        $this->badge = $badge;
    }

    public function execute(): void {
        if ($this->volunteer === null || $this->badge === null) {
            throw new Exception("Volunteer and badge must be set before executing this command.");
        }

        $this->badgeReceiver->giveBadge($this->volunteer, $this->badge);
    }
}

class RevokeBadgeCommand implements ICommand {
    private BadgeReceiver $badgeReceiver;
    private ?Volunteer $volunteer = null;

    public function __construct(BadgeReceiver $badgeReceiver) {
        $this->badgeReceiver = $badgeReceiver;
    }

    public function setVolunteer(Volunteer $volunteer): void {
        $this->volunteer = $volunteer;
    }

    public function execute(): void {
        if ($this->volunteer === null) {
            throw new Exception("Volunteer must be set before executing this command.");
        }

        $this->badgeReceiver->revokeBadge($this->volunteer);
    }
}

class RecognizeReportCommand implements ICommand {
    private ReportReceiver $reportReceiver;
    private ?int $reportID = null;

    public function __construct(ReportReceiver $reportReceiver) {
        $this->reportReceiver = $reportReceiver;
    }

    public function setReportID(int $reportID): void {
        $this->reportID = $reportID;
    }

    public function execute(): void {
        if ($this->reportID === null) {
            throw new Exception("Report ID must be set before executing this command.");
        }

        $this->reportReceiver->recognizeReport($this->reportID);
    }
}

class ReportReceiver{
    protected ReportingData $reportingData;

    public function __construct(ReportingData $reportingData)
    {
        $this->reportingData = $reportingData;
    }

    public function recognizeReport($reportID) {
        $reportDetails = $this->reportingData->fetchReportDetails($reportID);
    
        if ($reportDetails) {
            return $this->reportingData->updateReportField($reportID, 'recognized', 1);
        }
    
        return false;
    }

  
    public function updateReportStatus($reportID, $newStatus) {
        $validStatuses = ['Pending', 'Acknowledged', 'In Progress', 'Completed'];
        if (!in_array($newStatus, $validStatuses)) {
            return false;
        }

        $reportDetails = $this->reportingData->fetchReportDetails($reportID);

        if ($reportDetails) {
            return $this->reportingData->updateReportField($reportID, 'status', $newStatus);
        }

        return false;
    }

    public function viewAllReports() {
        return $this->reportingData->getAllActiveReports();
    }
}






############################################################################################################

class BadgeReceiver{
    protected Badges $badge;

    public function __construct(Badges $badge)
    {
        $this->badge = $badge;
    }

    public function giveBadge(Volunteer $volunteer, Badges $badge) {
        $volunteer->setBadge($badge);
        return true;
    }

    public function revokeBadge(Volunteer $volunteer) {
        $volunteer->setBadge(NULL);
        return true;
    }
}


class EventReceiver{
    protected Event $event;

    public function __construct( Event $event)
    {
        $this->event = $event;
    
    }
    public function createEvent(string $eventDate, Address $eventLocation, string $eventName, string $eventDescription, int $reqCooks, int $reqForDelivery, int $reqCoordinators): bool
    {
        // Validate if the address ID is properly set (make sure the address is created first)
        if (!$eventLocation->getId()) {
            throw new Exception("Address ID must be set before creating an event.");
        }
    
        // Create a new Event object with the provided data
        $event = new Event(
            null, // Assuming eventID is auto-incremented, we pass null or leave it out
            $eventDate,
            $eventLocation,  // pass the address object
            $eventName,
            $eventDescription,
            $reqCooks,
            $reqForDelivery,
            $reqCoordinators
        );
    
        // Call the create method on the Event object to save it to the database
        return $event->create();
    }

    public function deleteEvent(int $eventID): bool
    {
        // Create an Event object with the given ID and delete it
        $event = new Event($eventID);
        return $event->delete();
    }


    public function updateEvent(int $eventID, string $eventDate, Address $eventLocation, string $eventName, string $eventDescription, int $reqCooks, int $reqForDelivery, int $reqCoordinators): bool
    {
        // Ensure the address ID is valid
        if (!$eventLocation->getId()) {
            throw new Exception("Invalid Address ID. Update failed.");
        }
    
        // Create an Event object with updated data
        $event = new Event(
            $eventID,
            $eventDate,
            $eventLocation,  // pass the address object
            $eventName,
            $eventDescription,
            $reqCooks,
            $reqForDelivery,
            $reqCoordinators
        );
    
        // Call the update method on the Event object to save it to the database
        return $event->update();
    }

    public function fetchAllEvents(): array
    {
        return Event::fetchAll();
    }


}


// class VerificationReciver{



// }
// class PadgeReciver{



// }

//Invoker(remote controller)
class ControlPanel {

    private ICommand $command;

    public function setCommand(ICommand $command): void {
        $this->command = $command;
    }

    public function executeCommand(): void {
        $this->command->execute();
    }

}
