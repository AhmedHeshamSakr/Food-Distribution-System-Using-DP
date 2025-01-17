<?php




interface IteratorInterface
{
    public function hasNext(): bool;
    public function next(): mixed;
    public function remove(): void; // Optional, depending on your needs
}

interface Lists
{
    public function createIterator(): IteratorInterface;
}

class DeliveryIterator implements IteratorInterface
{
    private $deliveries;
    private $position = 0;

    public function __construct(array $deliveries)
    {
        $this->deliveries = $deliveries;
    }

    public function hasNext(): bool
    {
        return isset($this->deliveries[$this->position]);
    }

    public function next(): mixed
    {
        return $this->deliveries[$this->position++];
    }

    public function remove(): void
    {
        if ($this->position > 0) {
            array_splice($this->deliveries, $this->position - 1, 1);
            $this->position--;
        }
    }
}

class DeliveryList implements Lists
{
    private $deliveries = [];

    public function addDelivery(Delivery $delivery): void
    {
        $this->deliveries[] = $delivery;
    }

    public function createIterator(): IteratorInterface
    {
        return new DeliveryIterator($this->deliveries);
    }

    public function count(): int
    {
        return count($this->deliveries);
    }
}


class EventIterator implements IteratorInterface
{
    private $events;
    private $position = 0;

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function hasNext(): bool
    {
        return isset($this->events[$this->position]);
    }

    public function next(): mixed
    {
        return $this->events[$this->position++];
    }

    public function remove(): void
    {
        if ($this->position > 0) {
            array_splice($this->events, $this->position - 1, 1);
            $this->position--;
        }
    }
}

class EventList implements Lists
{
    private $events = [];

    public function addEvent(Event $event): void
    {
        $this->events[] = $event;
    }

    public function createIterator(): IteratorInterface
    {
        return new EventIterator($this->events);
    }

    public function count(): int
    {
        return count($this->events);
    }
}

class ReportingDataIterator implements IteratorInterface
{
    private $reports;
    private $position = 0;

    public function __construct(array $reports)
    {
        $this->reports = $reports;
    }

    public function hasNext(): bool
    {
        return isset($this->reports[$this->position]);
    }

    public function next(): mixed
    {
        return $this->reports[$this->position++];
    }

    public function remove(): void
    {
        if ($this->position > 0) {
            array_splice($this->reports, $this->position - 1, 1);
            $this->position--;
        }
    }
}

class ReportingDataList implements Lists
{
    private $reports = [];

    public function addReport(ReportingData $report): void
    {
        $this->reports[] = $report;
    }

    public function createIterator(): IteratorInterface
    {
        return new ReportingDataIterator($this->reports);
    }

    public function count(): int
    {
        return count($this->reports);
    }
}

class VolunteerIterator implements IteratorInterface
{
    private $volunteers;
    private $position = 0;

    public function __construct(array $volunteers)
    {
        $this->volunteers = $volunteers;
    }

    public function hasNext(): bool
    {
        return isset($this->volunteers[$this->position]);
    }

    public function next(): mixed
    {
        return $this->volunteers[$this->position++];
    }

    public function remove(): void
    {
        if ($this->position > 0) {
            array_splice($this->volunteers, $this->position - 1, 1);
            $this->position--;
        }
    }
}

class VolunteerList implements Lists
{
    private $volunteers = [];

    public function addVolunteer(Volunteer $volunteer): void
    {
        $this->volunteers[] = $volunteer;
    }

    public function createIterator(): IteratorInterface
    {
        return new VolunteerIterator($this->volunteers);
    }

    public function count(): int
    {
        return count($this->volunteers);
    }
}

class BadgeList implements Lists
{
    private $badges = [];

    public function addBadge(Badge $badge): void
    {
        $this->badges[] = $badge;
    }

    public function createIterator(): IteratorInterface
    {
        return new BadgeIterator($this->badges);
    }

    public function count(): int
    {
        return count($this->badges);
    }
}

class BadgeIterator implements IteratorInterface
{
    private $badges;
    private $position = 0;

    public function __construct(array $badges)
    {
        $this->badges = $badges;
    }

    public function hasNext(): bool
    {
        return isset($this->badges[$this->position]);
    }

    public function next(): mixed
    {
        return $this->badges[$this->position++];
    }

    public function remove(): void
    {
        if ($this->position > 0) {
            array_splice($this->badges, $this->position - 1, 1);
            $this->position--;
        }
    }
}