<?php

require_once '#c-Meals.php';

class Cooking
{
    private int $cookID;
    private int $mealID;
    private int $mealsTaken;
    private int $mealsCompleted;

    // Constructor
    public function __construct(int $cookID, int $mealID, int $mealsTaken = 0, int $mealsCompleted = 0)
    {
        $this->cookID = $cookID;
        $this->mealID = $mealID;
        $this->mealsTaken = $mealsTaken;
        $this->mealsCompleted = $mealsCompleted;
    }

    // Getters
    public function getCookID(): int
    {
        return $this->cookID;
    }

    public function getMealID(): int
    {
        return $this->mealID;
    }

    public function getMealsTaken(): int
    {
        return $this->mealsTaken;
    }

    public function getMealsCompleted(): int
    {
        return $this->mealsCompleted;
    }

    // Setters
    public function setCookID(int $cookID): void
    {
        $this->cookID = $cookID;
    }

    public function setMealID(int $mealID): void
    {
        $this->mealID = $mealID;
    }

    public function setMealsTaken(int $mealsTaken): void
    {
        $this->mealsTaken = $mealsTaken;
    }

    public function setMealsCompleted(int $mealsCompleted): void
    {
        $this->mealsCompleted = $mealsCompleted;
    }

    // Cook takes a certain number of meals to prepare
    public function takeMeals(int $count): bool
    {
        $meal = Meal::ReadMeal($this->mealID);
        if ($meal && $meal->reduceRemainingMeals($count)) {
            $query = "INSERT INTO Cooking (cookID, mealID, mealsTaken, mealsCompleted) 
                      VALUES ('{$this->cookID}', '{$this->mealID}', '$count', 0)
                      ON DUPLICATE KEY UPDATE mealsTaken = mealsTaken + $count";

            return run_query($query);
        }
        return false;
    }

    // Mark meals as completed
    public function completeMeals(int $count): bool
    {
        // Ensure that the number of meals completed doesn't exceed the meals taken
        if ($this->mealsTaken < $this->mealsCompleted + $count) {
            return false; // Prevent over-completion
        }

        $query = "UPDATE Cooking 
                  SET mealsCompleted = mealsCompleted + '$count' 
                  WHERE cookID = '{$this->cookID}' AND mealID = '{$this->mealID}' 
                  AND (mealsTaken - mealsCompleted) >= '$count'";
        if (run_query($query)) {
            $this->mealsCompleted += $count;
            return true;
        }
        return false;
    }

    // Get all meals assigned to a specific cook
    public static function getMealsByCook(int $cookID): array
    {
        $query = "SELECT * FROM Cooking WHERE cookID = '$cookID'";
        return run_select_query($query) ?: [];
    }

    // Get the number of meals needed for a specific meal
    public static function getMealsNeeded(int $mealID): int
    {
        $meal = Meal::ReadMeal($mealID);
        return $meal ? $meal->getRemainingMeals() : 0;
    }

    // Fetch cooks assigned to a specific meal
    public static function getCooksByMeal(int $mealID): array
    {
        $query = "SELECT * FROM Cooking WHERE mealID = '$mealID'";
        return run_select_query($query) ?: [];
    }
}