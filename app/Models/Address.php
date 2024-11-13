<?php

require_once __DIR__ . "/../../config/DB.php";  

class Address
{
    private int $id;
    private string $name;
    private int $parent_id;
    private string $level; // Change level to string

    // Constructor to initialize properties
    public function __construct(int $id, string $name, int $parent_id, string $level)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parent_id = $parent_id;
        $this->level = $level;
    }

    // Create a new address record in the database
    public function create(): bool
    {
        $query = "INSERT INTO address (id, name, parent_id, level) VALUES ('{$this->id}', '{$this->name}', '{$this->parent_id}', '{$this->level}')";
        return run_query($query);
    }

    // Delete an address record from the database
    public function delete(): bool
    {
    // Recursively delete all descendants of the current address
    $this->deleteDescendants($this->id);

    // Now delete the parent address (current id)
    $queryDeleteParent = "DELETE FROM address WHERE id = '{$this->id}'";
    return run_query($queryDeleteParent);
    }

   //delete anything releted to this parent (aka anto l3yaly wla a3rfko )
    private function deleteDescendants(int $parentId): void
    {
        // Find all direct children of the given parentId
        $querySelectChildren = "SELECT id FROM address WHERE parent_id = '{$parentId}'";
        $children = run_select_query($querySelectChildren);

        if ($children) {
            foreach ($children as $child) {
                // Recursively delete descendants of each child
                $this->deleteDescendants($child['id']);

                // Delete the child itself
                $queryDeleteChild = "DELETE FROM address WHERE id = '{$child['id']}'";
                run_query($queryDeleteChild);
            }
        }
    }

    // Update an address record in the database
    public function update(): bool
    {
        $query = "UPDATE address SET name = '{$this->name}', parent_id = '{$this->parent_id}', level = '{$this->level}' WHERE id = '{$this->id}'";
        return run_query($query);
    }

    // Read an address record from the database
    public static function read(int $id): ?Address
    {
        $query = "SELECT * FROM address WHERE id = '{$id}'";
        $result = run_select_query($query);

        if ($result && count($result) > 0) {
            $data = $result[0];
            return new Address($data['id'], $data['name'], $data['parent_id'], $data['level']);
        }

        return null;
    }

    // Getters and Setters for each attribute
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getParentId(): int
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
}