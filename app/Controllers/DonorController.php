<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../Models/Donor.php";
require_once __DIR__ . "/../Views/DonorView.php";

class DonorController
{
    private DonorView $view;
    private Donor $donor;

    public function __construct(DonorView $view)
{
    // session_start(); // Start the session to access the email
    $email = $_SESSION['email'] ?? null;

    if ($email) {
        // Fetch user data from the database using the email
        $query = "SELECT * FROM person WHERE email = '$email' LIMIT 1";
        $result = mysqli_query(Database::getInstance()->getConnection(), $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $userData = mysqli_fetch_assoc($result);

            // Initialize Donor with retrieved data
            $this->donor = new Donor(
                $userData['userTypeID'],
                $userData['firstName'],
                $userData['lastName'],
                $userData['email'],
                $userData['phoneNo']
            );

        } else {
            throw new Exception("User not found in the database.");
        }
    } else {
        throw new Exception("User is not logged in.");
    }

    $this->view = $view;
}

    /**
     * Handle the request and render the appropriate view.
     */
    public function handleRequest()
    {
        $action = $_POST['action'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'add_donation') {
                $this->handleAddDonation();
            } elseif ($action === 'update_info') {
                $this->handleUpdatePersonalInfo();
            }

            // Redirect to avoid form resubmission
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }

        $this->renderPage();
    }


    /**
     * Handle adding a donation.
     */
    private function handleAddDonation()
    {
        $amount = $_POST['amount'];
        $paymentMethod = $_POST['paymentMethod'];
        $paymentDetails = $_POST['paymentDetails']; // Handle specific details as necessary

        $this->donor->addDonation($amount, $paymentMethod, $paymentDetails);
    }

    /**
     * Handle updating personal info.
     */
    private function handleUpdatePersonalInfo()
    {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $phoneNo = $_POST['phoneNo'];

        $this->donor->updatePersonalInfo($firstName, $lastName, $email, $phoneNo);
    }

    /**
     * Render the page with all relevant content.
     */
    private function renderPage()
    {
        $donationHistory = $this->donor->fetchDonationHistory();

        // Render page header and required assets
        $this->view->renderPageHeader();

        // Render the view content (donation history, donation form, personal info form)

        $this->view->renderPersonalInfoForm();
        $this->view->renderDonationForm();
        $this->view->renderDonationHistory($donationHistory);
       

        // Render page footer with JS files
        $this->view->renderPageFooter();
    }
}