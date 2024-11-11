<?php

class Badges
{
    private int $badgeID;
    private string $badgeName;
    private string $badgeType;
    private string $expiryDate;

    public function __construct(int $badgeID, string $badgeName, string $badgeType, string $expiryDate)
    {
        $this->badgeID = $badgeID;
        $this->badgeName = $badgeName;
        $this->badgeType = $badgeType;
        $this->expiryDate = $expiryDate;
    }

    // Getters
    public function getBadgeID(): int
    {
        return $this->badgeID;
    }

    public function getBadgeName(): string
    {
        return $this->badgeName;
    }

    public function getBadgeType(): string
    {
        return $this->badgeType;
    }

    public function getExpiryDate(): string
    {
        return $this->expiryDate;
    }


    // Setters
    public function setBadgeName(string $badgeName): void
    {
        $this->badgeName = $badgeName;
    }

    public function setBadgeType(string $badgeType): void
    {
        $this->badgeType = $badgeType;
    }

    public function setExpiryDate(string $expiryDate): void
    {
        $this->expiryDate = $expiryDate;
    }
}
?>
