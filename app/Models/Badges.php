<?php

class Badges
{
    private int $badgeID;
    private string $badgeName;
    private string $badgeLvl;
    private string $expiryDate;

    public function __construct(int $badgeID, string $badgeName, string $badgeLvl, string $expiryDate)
    {
        $this->badgeID = $badgeID;
        $this->badgeName = $badgeName;
        $this->badgeLvl = $badgeLvl;
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

    public function getBadgeLvl(): string
    {
        return $this->badgeLvl;
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

    public function setBadgeLvl(string $badgeLvl): void
    {
        $this->badgeLvl = $badgeLvl;
    }

    public function setExpiryDate(string $expiryDate): void
    {
        $this->expiryDate = $expiryDate;
    }
}
?>
