<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../Models/Donor.php";
require_once __DIR__ . "/../Views/DonorView.php";



class DonorController
{
    
    private $donor;
    private $donorView;

    public function __construct(Donor $donor, DonorView $donorView)
    {
        $this->donor = $donor;
        $this->donorView = $donorView;
    }

    /**
     * Handle the request to display the donation history.
     */
    public function displayDonationHistory(): void
    {
        $this->donorView->renderDonationHistory();
    }

    /**
     * Handle the request to show the add donation form.
     */
    public function showAddDonationForm(): void
    {
        $this->donorView->renderAddDonationForm();
    }

    /**
     * Process the add donation request.
     */
    public function processAddDonation(array $postData): void
    {
        try {
            $amount = (float)$postData['amount'];
            $paymentMethod = $postData['paymentMethod'];
            $paymentDetails = json_decode($postData['paymentDetails'], true);

            if ($this->donor->addDonation($amount, $paymentMethod, $paymentDetails)) {
                echo "<p>Donation added successfully!</p>";
            } else {
                echo "<p>Failed to add donation. Please try again.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Error: {$e->getMessage()}</p>";
        }
    }

    /**
     * Handle the request to show the update personal information form.
     */
    public function showUpdatePersonalInfoForm(): void
    {
        $this->donorView->renderUpdatePersonalInfoForm();
    }

    /**
     * Process the update personal information request.
     */
    public function processUpdatePersonalInfo(array $postData): void
    {
        try {
            $firstName = $postData['firstName'];
            $lastName = $postData['lastName'];
            $email = $postData['email'];
            $phoneNo = $postData['phoneNo'];

            if ($this->donor->updatePersonalInfo($firstName, $lastName, $email, $phoneNo)) {
                echo "<p>Personal information updated successfully!</p>";
            } else {
                echo "<p>Failed to update personal information. Please try again.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Error: {$e->getMessage()}</p>";
        }
    }

    /**
     * Handle the request to show the update donation form.
     */
    public function showUpdateDonationForm(): void
    {
        $this->donorView->renderUpdateDonationForm();
    }

    /**
     * Process the update donation request.
     */
    public function processUpdateDonation(array $postData): void
    {
        try {
            $donationID = (int)$postData['donationID'];
            $amount = (float)$postData['amount'];
            $paymentMethod = $postData['paymentMethod'];

            if ($this->donor->updateDonation($donationID, $amount, $paymentMethod)) {
                echo "<p>Donation updated successfully!</p>";
            } else {
                echo "<p>Failed to update donation. Please try again.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Error: {$e->getMessage()}</p>";
        }
    }
}

// Example: Fetching user data from session (ensure session_start() is called earlier)
session_start();

if (isset($_SESSION['userID'], $_SESSION['firstName'], $_SESSION['lastName'], $_SESSION['email'], $_SESSION['phoneNo'], $_SESSION['login'])) {
    $userID = $_SESSION['userID'];
    $firstName = $_SESSION['firstName'];
    $lastName = $_SESSION['lastName'];
    $email = $_SESSION['email'];
    $phoneNo = $_SESSION['phoneNo'];
    $login = $_SESSION['login'];
} else {
    die("User session data is missing. Please log in.");
}

// Instantiate Donor and DonorView
$donor = new Donor($userID, $firstName, $lastName, $email, $phoneNo, $login);
$donorView = new DonorView($donor);

// Instantiate the controller
$controller = new DonorController($donor, $donorView);
// Example action handling
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'donationHistory':
            $controller->displayDonationHistory();
            break;
        case 'addDonationForm':
            $controller->showAddDonationForm();
            break;
        case 'updatePersonalInfoForm':
            $controller->showUpdatePersonalInfoForm();
            break;
        case 'updateDonationForm':
            $controller->showUpdateDonationForm();
            break;
        default:
            echo "<p>Invalid action specified.</p>";
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'addDonation':
            $controller->processAddDonation($_POST);
            break;
        case 'updatePersonalInfo':
            $controller->processUpdatePersonalInfo($_POST);
            break;
        case 'updateDonation':
            $controller->processUpdateDonation($_POST);
            break;
        default:
            echo "<p>Invalid action specified.</p>";
            break;
    }
} else {
    echo "<p>No valid request received.</p>";
}


