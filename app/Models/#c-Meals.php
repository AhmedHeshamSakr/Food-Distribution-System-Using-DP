
<?php
class Meal
{
    private ?int $mealID; // Updated to allow null
    private bool $needOfDelivery;
    private int $nOFMeals;
    private int $remainingMeals;
    private string $mealDescription;

    // Constructor
    public function __construct(bool $needOfDelivery, int $nOFMeals, string $mealDescription, ?int $mealID = null)
    {
        $this->mealID = $mealID;
        $this->needOfDelivery = $needOfDelivery;
        $this->nOFMeals = $nOFMeals;
        $this->remainingMeals = $nOFMeals; // Initially, remaining meals equals total meals
        $this->mealDescription = $mealDescription;
    }

    // Getters
    public function getMealID(): ?int
    {
        return $this->mealID;
    }

    public function getNeedOfDelivery(): bool
    {
        return $this->needOfDelivery;
    }

    public function getNOFMeals(): int
    {
        return $this->nOFMeals;
    }

    public function getRemainingMeals(): int
    {
        return $this->remainingMeals;
    }

    public function getMealDescription(): string
    {
        return $this->mealDescription;
    }

    // Setters
    public function setMealID(int $mealID): void
    {
        $this->mealID = $mealID;
    }

    public function setNeedOfDelivery(bool $needOfDelivery): void
    {
        $this->needOfDelivery = $needOfDelivery;
    }

    public function setNOFMeals(int $nOFMeals): void
    {
        $this->nOFMeals = $nOFMeals;
        $this->remainingMeals = $nOFMeals; // Reset remaining meals if total meals are updated
    }

    public function setRemainingMeals(int $remainingMeals): void
    {
        $this->remainingMeals = $remainingMeals;
    }

    public function setMealDescription(string $mealDescription): void
    {
        $this->mealDescription = $mealDescription;
    }

    // Method to create a new meal in the database
    public function CreateMeal(): bool
    {
        $needOfDelivery = (int)$this->needOfDelivery;
        $mealDescription = mysqli_real_escape_string(Database::getInstance()->getConnection(), $this->mealDescription);

        $query = "INSERT INTO Meal (needOfDelivery, nOFMeals, remainingMeals, mealDescription)
                  VALUES ('$needOfDelivery', '{$this->nOFMeals}', '{$this->remainingMeals}', '$mealDescription')";

        $result = run_query($query);
        if ($result) {
            $this->mealID = mysqli_insert_id(Database::getInstance()->getConnection());
            return true;
        }
        return false;
    }

    // Method to read a meal by ID
    public static function ReadMeal(int $mealID): ?Meal
    {
        $query = "SELECT * FROM Meal WHERE mealID = '$mealID'";
        $result = run_select_query($query);

        if ($result && count($result) > 0) {
            $mealData = $result[0];
            return new Meal(
                (bool)$mealData['needOfDelivery'],
                $mealData['nOFMeals'],
                $mealData['mealDescription'],
                $mealData['mealID']
            );
        }
        return null;
    }

    // Method to update the meal in the database
    public function UpdateMeal(): bool
    {
        $mealDescription = mysqli_real_escape_string(Database::getInstance()->getConnection(), $this->mealDescription);
        $query = "UPDATE Meal 
                  SET needOfDelivery = '{$this->needOfDelivery}', 
                      nOFMeals = '{$this->nOFMeals}', 
                      remainingMeals = '{$this->remainingMeals}', 
                      mealDescription = '$mealDescription'
                  WHERE mealID = '{$this->mealID}'";
        return run_query($query);
    }

    // Method to reduce the number of remaining meals
    public function reduceRemainingMeals(int $count): bool
    {
        if ($this->remainingMeals < $count) {
            return false;
        }
        $this->remainingMeals -= $count;
        return $this->UpdateMeal();
    }

    // Method to delete a meal by ID
    public function DeleteMeal(): bool
    {
        $query = "DELETE FROM Meal WHERE mealID = '{$this->mealID}'";
        return run_query($query);
    }
}