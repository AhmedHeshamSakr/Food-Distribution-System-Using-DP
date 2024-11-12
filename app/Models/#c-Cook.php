<?php
require_once 'Volunteer.php';
require_once '#c-Meals.php';
require_once '#c-Cooking.php';

class Cook extends VolunteerRoles
{
    public function __construct(User $user)
    {
        // Initialize the VolunteerRoles with a User reference
        parent::__construct($user);
        // Assign the cook role by default
        $this->chooseRole();
    }

    // Override chooseRole to assign the Cook flag
    public function chooseRole(): bool
    {
        $this->roleType |= self::COOK_FLAG; // Set Cook role flag
        return true;
    }

    // Take meals to cook
    public function takeMeals(int $mealID, int $count): bool
    {
        // Check if meals are available to take
        $mealsNeeded = Cooking::getMealsNeeded($mealID);
        if ($mealsNeeded < $count) {
            echo "Not enough meals available to take.\n";
            return false;  // Can't take more meals than are needed
        }

        // Create a Cooking instance to handle meal assignment
        $cooking = new Cooking($this->ref->getUserID(), $mealID);

        // Try to take the specified number of meals
        if ($cooking->takeMeals($count)) {
            echo "$count meals assigned to cook.\n";
            return true;
        } else {
            echo "Failed to assign meals to cook.\n";
            return false;
        }
    }

    // Complete meals that were taken
    public function completeMeals(int $mealID, int $count): bool
    {
        // Ensure cook has taken meals before completing them
        $cooking = new Cooking($this->ref->getUserID(), $mealID);
        $mealsTaken = $cooking->getMealsTaken();

        if ($mealsTaken < $count) {
            echo "Cook has not taken enough meals to complete this many.\n";
            return false;  // Can't complete more meals than the cook has taken
        }

        // Mark meals as completed
        if ($cooking->completeMeals($count)) {
            echo "$count meals completed by cook.\n";
            return true;
        } else {
            echo "Failed to complete meals.\n";
            return false;
        }
    }

    // Get all meals assigned to this cook
    public function getMealsAssigned(): array
    {
        return Cooking::getMealsByCook($this->ref->getUserID());
    }

    // Get the list of all roles assigned to this cook
    public function getRoles(): array
    {
        return $this->getAllRoles();
    }
}