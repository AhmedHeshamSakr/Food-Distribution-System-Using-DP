<?php

require_once 'Person.php';
require_once 'Badges.php';
require_once 'UserBadge.php';



class BadgeAdmin extends Person
{
    private int $userTypeID = Person::B_ADMIN_FLAG;
    // Constructor to initialize BadgeAdmin with required details
    public function __construct(string $firstName, string $lastName, string $email, string $phoneNo)
    {   
        $userTypeID = Person::B_ADMIN_FLAG;
        $this->userTypeID = $userTypeID;
        parent::__construct($firstName, $lastName, $email, $phoneNo, $userTypeID);
    }

    // Badge Management Methods
    public function createBadge(string $badgeLvl): bool
    {
        $badge = new Badges(0, $badgeLvl); // Badge ID is 0 for new badges
        return $badge->insertBadge();
    }

    public function updateBadge(int $badgeID, string $newBadgeLvl): bool
    {
        $badge = new Badges($badgeID, $newBadgeLvl);
        return $badge->updateBadge();
    }

    public function deleteBadge(int $badgeID): bool
    {
        $badge = new Badges($badgeID);
        return $badge->deleteBadge();
    }

    public function getBadge(int $badgeID): ?array
    {
        $badge = new Badges('Silver Tier');
        return $badge->getBadgeByID($badgeID);
    }

    public function getAllBadges(): array
    {
        $badge = new Badges('Silver Tier');
        return $badge->getAllBadges();
    }

    // User-Badge Assignment Methods
    public function assignBadgeToUser(int $userID, int $badgeID, string $dateAwarded, string $expiryDate): bool
    {
        $userBadge = new UserBadge($userID, $badgeID, $dateAwarded, $expiryDate);
        return $userBadge->create();
    }

    public function updateBadgeAssignment(int $userID, int $badgeID, string $newDateAwarded, string $newExpiryDate): bool
    {
        $userBadge = new UserBadge($userID, $badgeID, $newDateAwarded, $newExpiryDate);
        return $userBadge->update();
    }

    public function revokeBadgeFromUser(int $userID, int $badgeID): bool
    {
        $userBadge = new UserBadge($userID, $badgeID, '', ''); // Empty dates as placeholders
        return $userBadge->delete();
    }

    public function getUserBadge(int $userID, int $badgeID): ?UserBadge
    {
        return UserBadge::read($userID, $badgeID);
    }

    public function getUserTypeID(): int
    {
        return $this->userTypeID;
    }
}


