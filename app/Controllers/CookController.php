<?php

require_once __DIR__ . "/../Models/Cooking.php";
require_once __DIR__ . "/../Views/CookingView.php";

class CookingController
// {
//     private CookingView $view;

//     public function __construct(CookingView $view)
//     {
//         session_start();

//         $cookID = $_SESSION['cookID'] ?? null;

//         if (!$cookID) {
//             throw new Exception("Cook is not logged in.");
//         }

//         $this->view = $view;
//     }

//     public function handleRequest()
//     {
//         $action = $_POST['action'] ?? null;

//         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//             try {
//                 switch ($action) {
//                     case 'take_meals':
//                         $this->handleTakeMeals();
//                         break;
//                     case 'complete_meals':
//                         $this->handleCompleteMeals();
//                         break;
//                 }

//                 // Redirect to prevent form resubmission
//                 header("Location: " . $_SERVER['REQUEST_URI']);
//                 exit;
//             } catch (Exception $e) {
//                 $this->view->renderError($e->getMessage());
//             }
//         }

//         $this->renderPage();
//     }

//     private function handleTakeMeals()
//     {
//         $mealID = (int) ($_POST['mealID'] ?? 0);
//         $count = (int) ($_POST['count'] ?? 0);

//         if ($mealID <= 0 || $count <= 0) {
//             throw new Exception("Invalid meal ID or count provided.");
//         }

//         $cookID = $_SESSION['cookID'];
//         $cooking = new Cooking($cookID, $mealID);

//         if (!$cooking->takeMeals($count)) {
//             throw new Exception("Failed to take meals. Ensure enough meals are available.");
//         }
//     }

//     private function handleCompleteMeals()
//     {
//         $mealID = (int) ($_POST['mealID'] ?? 0);
//         $count = (int) ($_POST['count'] ?? 0);

//         if ($mealID <= 0 || $count <= 0) {
//             throw new Exception("Invalid meal ID or count provided.");
//         }

//         $cookID = $_SESSION['cookID'];
//         $cooking = new Cooking($cookID, $mealID);

//         if (!$cooking->completeMeals($count)) {
//             throw new Exception("Failed to complete meals. Ensure the count does not exceed meals taken.");
//         }
//     }

//     // private function renderPage()
//     // {
//     //     $cookID = $_SESSION['cookID'];
//     //     $meals = Cooking::getMealsByCook($cookID);
//     //     $this->view->renderPageHeader();
//     //     $this->view->renderMealsList($meals);
//     //     $this->view->renderTakeMealsForm();
//     //     $this->view->renderCompleteMealsForm();
//     //     $this->view->renderPageFooter();
//     // }

