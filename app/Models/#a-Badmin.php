<?php

require_once 'Person.php';
require_once 'Badges.php';
require_once 'Volunteer.php';

class BadgeAdmin extends Person
{
    private int $userTypeID = Person::B_ADMIN_FLAG;

    public function __construct(string $firstName, string $lastName, string $email, string $phoneNo)
    {   
        parent::__construct($firstName, $lastName, $email, $phoneNo, self::B_ADMIN_FLAG);
    }

    public function createBadge(string $badgeLvl): bool
    {
        $badge = new Badges($badgeLvl);
        return $badge->insertBadge();
    }

    public function updateBadge(int $badgeID, string $newBadgeLvl): bool
    {
        $badge = new Badges($newBadgeLvl);
        $badge->setBadgeID($badgeID);
        return $badge->updateBadge();
    }

    public function deleteBadge(int $badgeID): bool
    {
        $badge = new Badges('');
        $badge->setBadgeID($badgeID);
        return $badge->deleteBadge();
    }

    public function getBadge(int $badgeID): ?array
    {
        $badge = new Badges('');
        return $badge->getBadgeByID($badgeID);
    }

    public function getAllBadges(): array
    {
        $badge = new Badges('');
        return $badge->getAllBadges();
    }

    public function assignBadgeToUser(int $userID, int $badgeID): bool
    {
        // Fetch the Volunteer object by userID
        $volunteer = Volunteer::fetchById($userID);
        if (!$volunteer) {
            return false;
        }

        // Create a Badges object with the given badgeID
        $badge = new Badges('');
        $badge->setBadgeID($badgeID);

        // Use the Volunteer's setBadge method to assign the badge
        return $volunteer->setBadge($badge);
    }

    public function revokeBadgeFromUser(int $userID): bool
    {
        // Fetch the Volunteer object by userID
        $volunteer = Volunteer::fetchById($userID);
        if (!$volunteer) {
            return false;
        }

        // Set the badge to null or a default badge ID using setBadge
        // Assuming a default badge ID of 0
        $defaultBadge = new Badges('');
        $defaultBadge->setBadgeID(0);
        return $volunteer->setBadge($defaultBadge);
    }

    public function getUserBadge(int $userID): ?Badges
    {
        // Fetch the Volunteer object by userID
        $volunteer = Volunteer::fetchById($userID);
        if (!$volunteer) {
            return null;
        }

        // Return the badge property of the Volunteer object
        return $volunteer->getBadge();
    }

    public function getUserTypeID(): int
    {
        return $this->userTypeID;
    }
}
?>