<?php

require_once __DIR__ . "/../../config/DB.php";

class Address
{
    private int $id;
    private string $name;
    private ?int $parent_id = NULL;
    private string $level; // Change level to string

    // Constructor to initialize properties
    public function __construct(string $name, ?int $parent_id, string $level)
    {
        $this->name = $name;
        $this->parent_id = $parent_id;
        $this->level = $level;
    }

    // Create a new address record in the database
    public function create(): bool
    {
        // Check if the address already exists in the database
        $queryCheck = "SELECT id FROM address WHERE name = '{$this->name}' AND parent_id " .
            ($this->parent_id === null ? "IS NULL" : "= '{$this->parent_id}'") .
            " AND level = '{$this->level}' LIMIT 1";
        $existingAddress = run_select_query($queryCheck);

        if ($existingAddress && count($existingAddress) > 0) {
            echo "Duplicate entry: This address already exists in the database.<br>";
            return false; // Do not insert if the address already exists
        }

        // Proceed to insert the new address if no duplicates were found
        if ($this->parent_id === NULL) {
            $query = "INSERT INTO address (name, level) VALUES ('{$this->name}', '{$this->level}')";
        } else {
            $query = "INSERT INTO address (name, parent_id, level) VALUES ('{$this->name}', '{$this->parent_id}', '{$this->level}')";
        }

        return run_query($query);
    }

    // Getters and Setters for each attribute
    public static function getIdByName(string $countryName): ?int
    {
        $query = "SELECT id FROM address WHERE name = '{$countryName}'";
        $result = run_select_query($query);

        if ($result && count($result) > 0) {
            return (int) $result[0]['id'];
        }

        return null; // Return null if no matching country is found
    }

    public function getId(): ?int
    {
        // Query to fetch the ID of the current instance based on its properties
        $query = "SELECT id FROM address WHERE name = '{$this->name}' AND parent_id " .
            ($this->parent_id === null ? "IS NULL" : "= '{$this->parent_id}'") .
            " AND level = '{$this->level}' LIMIT 1";
        $result = run_select_query($query); // Assume this function executes the query and returns an associative array

        // If a result is found, return the ID
        if ($result && count($result) > 0) {
            $this->id = (int) $result[0]['id']; // Update the instance ID
            return $this->id;
        }

        // Return null if no matching record is found
        return null;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function setParentId(int $parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function delete(): bool
    {
        // Start a database transaction
        run_query("START TRANSACTION");

        try {
            // Recursively delete all descendants first
            $this->deleteDescendants($this->getId());

            // Delete the current address (parent) after all descendants are deleted
            $queryDeleteParent = "DELETE FROM address WHERE id = '{$this->getId()}'";
            $deleteSuccess = run_query($queryDeleteParent);

            if (!$deleteSuccess) {
                throw new Exception("Failed to delete parent address with ID {$this->getId()}");
            }

            // Commit the transaction
            run_query("COMMIT");
            echo "Address deleted successfully.<br>";
            return true;

        } catch (Exception $e) {
            // Rollback transaction if any error occurs
            run_query("ROLLBACK");
            echo "Error during deletion: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    private function deleteDescendants(int $parentId): void
    {
        echo "Checking for children of parent ID: {$parentId}<br>";
        $querySelectChildren = "SELECT id FROM address WHERE parent_id = '{$parentId}'";
        $children = run_select_query($querySelectChildren);

        if ($children) {
            foreach ($children as $child) {
                // Recursively delete descendants of each child
                $this->deleteDescendants($child['id']);

                // Delete the child itself
                $queryDeleteChild = "DELETE FROM address WHERE id = '{$child['id']}'";
                run_query($queryDeleteChild);
                echo "Deleted child ID: {$child['id']}<br>";
            }
        } else {
            echo "No children found for parent ID: {$parentId}<br>";
        }
    }


    // Update an address record in the database
    public function update(): bool
    {
        $query = "UPDATE address SET name = '{$this->name}', parent_id = '{$this->parent_id}', level = '{$this->level}' WHERE id = '{$this->id}'";
        return run_query($query);
    }

    // Read an address record from the database
    public static function read(?int $id): ?Address
    {
        $query = "SELECT * FROM address WHERE id = '{$id}'";
        $result = run_select_query($query);

        if ($result && count($result) > 0) {
            $data = $result[0];
            return new Address($data['name'], $data['parent_id'], $data['level']);
        }

        return null;
    }
}