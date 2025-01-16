<?php

require_once __DIR__ . "/../../config/DB.php";

class Badges
{
    private int $badgeID = 0; // Default to 0 to handle uninitialized state
    private string $badgeLvl;
    private mysqli $connection;

    public function __construct(string $badgeLvl)
    {
        $this->badgeLvl = $badgeLvl;
        $this->connection = Database::getInstance()->getConnection(); // Get DB connection

        // Check for duplicates and insert if not exists
        $duplicateId = $this->findDuplicateBadge();
        if ($duplicateId !== null) {
            $this->badgeID = $duplicateId;
        } else {
            $this->insertBadge();
        }
    }

    // Getters
    public function getBadgeID(): int
    {
        return $this->badgeID;
    }

    public function getBadgeLvl(): string
    {
        return $this->badgeLvl;
    }

    // Setters
    public function setBadgeLvl(string $badgeLvl): void
    {
        $this->badgeLvl = $badgeLvl;
    }

    // CREATE: Insert a new badge
    public function insertBadge(): bool
    {
        // Check for duplicates
        $duplicateId = $this->findDuplicateBadge();
        if ($duplicateId !== null) {
            $this->badgeID = $duplicateId;
            return false; // Badge already exists
        }

        $query = "INSERT INTO Badge (badgeLvl) VALUES (?)";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Error preparing query: " . $this->connection->error);
        }

        $stmt->bind_param('s', $this->badgeLvl);
        if ($stmt->execute()) {
            // Assign the last inserted ID to $this->badgeID
            $this->badgeID = $this->connection->insert_id;
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // Find duplicate badge
    private function findDuplicateBadge(): ?int
    {
        $query = "SELECT badgeID FROM Badge WHERE badgeLvl = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Error preparing query: " . $this->connection->error);
        }

        $stmt->bind_param('s', $this->badgeLvl);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['badgeID'];
        }

        return null;
    }

    // READ: Get a badge by its ID
    public function getBadgeByID(int $badgeID): ?array
    {
        $query = "SELECT * FROM Badge WHERE badgeID = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Error preparing query: " . $this->connection->error);
        }

        $stmt->bind_param('i', $badgeID);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc(); // Return associative array of badge data
        }

        return null; // No badge found
    }

    // READ: Get all badges
    public function getAllBadges(): array
    {
        $query = "SELECT * FROM Badge";
        $result = $this->connection->query($query);

        if ($result && $result->num_rows > 0) {
            $badges = [];
            while ($row = $result->fetch_assoc()) {
                $badges[] = $row;
            }
            return $badges; // Return array of badges
        }

        return []; // No badges found
    }

    // UPDATE: Update an existing badge
    public function updateBadge(): bool
    {
        if ($this->badgeID === 0) {
            throw new Exception("Badge ID must be set before updating.");
        }

        $query = "UPDATE Badge SET badgeLvl = ? WHERE badgeID = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Error preparing query: " . $this->connection->error);
        }

        $stmt->bind_param('si', $this->badgeLvl, $this->badgeID);
        return $stmt->execute();
    }

    // DELETE: Delete a badge by its ID
    public function deleteBadge(): bool
    {
        if ($this->badgeID === 0) {
            throw new Exception("Badge ID must be set before deleting.");
        }

        $query = "DELETE FROM Badge WHERE badgeID = ?";
        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            die("Error preparing query: " . $this->connection->error);
        }

        $stmt->bind_param('i', $this->badgeID);
        return $stmt->execute();
    }
}
?>
