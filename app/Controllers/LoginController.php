<?php

require_once __DIR__ . '/../Models/Login.php';
require_once __DIR__ . '/../Views/LoginView.php';
require_once __DIR__ . '/../Models/Address.php';

class LoginController
{
    private $loginHandler;
    private $view;
    private $mode; // Tracks whether the view is for login or register
    private $errorMessage; // Store error messages

    public function __construct()
    {
        // Initialize the login handler with empty credentials
        $this->loginHandler = new withEmail('', '');
        // Initialize the view
        $this->view = new EmailLoginView($this->loginHandler);
        $this->mode = 'login'; // Default to login view
        $this->errorMessage = ''; // Initialize empty error message
    }

    /**
     * Main handler for the login flow.
     */
    public function handleRequest()
    {
        session_start();

        // Restore mode and error message from session if available
        $this->mode = $_SESSION['mode'] ?? 'login';
        $this->errorMessage = $_SESSION['errorMessage'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processFormSubmission();
        }

        if ($this->mode === 'register') {
            $this->fetchCountries();
        } else {
            $this->renderPage();
        }
    }

    /**
     * Process form submissions (login, register, logout).
     */
    private function processFormSubmission()
    {
        $action = $_POST['action'] ?? '';
    
        switch ($action) {
            case 'login':
                $this->handleLogin();
                break;

            case 'register':
                $this->handleRegistration();
                break;
    
            case 'logout':
                $this->handleLogout();
                break;
    
            case 'show_register':
                $this->mode = 'register';
                break;
    
            case 'show_login':
                $this->mode = 'login';
                break;
    
            default:
                $this->errorMessage = 'Invalid action.';
        }
    
        // Store mode and error message in session
        session_start();
        $_SESSION['mode'] = $this->mode;
        $_SESSION['errorMessage'] = $this->errorMessage;
    
        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    /**
     * Handle the login action.
     */
    private function handleLogin()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (strpos($email, '@badmin') !== false) {
            // Redirect to the admin dashboard page if email contains @admin
            session_start();
            $_SESSION['email'] = $email; // Store email in session
            header("Location: ../app/Views/AdminPageView.php"); // Admin dashboard
            exit();
        }

        if (strpos($email, '@eadmin') !== false) {
            // Redirect to the admin dashboard page if email contains @admin
            session_start();
            $_SESSION['email'] = $email; // Store email in session
            header("Location: ../app/Views/EAdminView.php"); // Admin dashboard
            exit();
        }


        if (strpos($email, '@vadmin') !== false) {
            // Redirect to the admin dashboard page if email contains @admin
            session_start();
            $_SESSION['email'] = $email; // Store email in session
            header("Location: ../app/Views/vAdminView.php"); // Admin dashboard
            exit();
        }

        // Normal login handling
        if ($this->loginHandler->login(['email' => $email, 'password' => $password])) {
            // Store email in session
            session_start();
            $_SESSION['email'] = $email;

            // Redirect to the regular home page
            header("Location: ../app/Views/HomePageView.php");
            exit();
        } else {
            $this->errorMessage = 'Invalid email or password. Please try again.';
        }
    }

    /**
     * Handle the registration action.
     */
    public function fetchCountries()
    {
        $countries = Address::getCountries();
        $cities = Address::getCities();
        $this->view->renderForm($this->mode, $this->errorMessage, $countries, $cities);
    }

    private function handleRegistration()
    {
        // Retrieve form inputs
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';
        $phoneNo = $_POST['phoneNo'] ?? '';
        $userTypeID = $_POST['userTypeID'] ?? ''; 
        $nationalID = $_POST['nationalID'] ?? '';  
        $address_string = $_POST['address'] ?? ''; 

        $adminType = '';

        // Determine userTypeID based on the email
        if (strpos($email, '@vadmin') !== false) {
            $adminType = '@vadmin';
        } elseif (strpos($email, '@eadmin') !== false) {
            $adminType = '@eadmin';
        } elseif (strpos($email, '@badmin') !== false) {
            $adminType = '@badmin';
        } else {
            $adminType = "else";
        }

        $address = new Address($address_string, $city, 'Neighborhood');

        if ($this->loginHandler->register($email, $password, $firstName, $lastName, $phoneNo, $userTypeID,  $nationalID, $address)) {
            $this->errorMessage = 'Registration successful! You can now log in.';
        } else {
            $this->errorMessage = 'Registration failed. The email might already be in use.';
            $this->mode = 'register'; // Stay on the register form on failure
        }
    }

    /**
     * Handle the logout action.
     */
    private function handleLogout()
    {
        if ($this->loginHandler->logout()) {
            $this->errorMessage = 'You have been logged out successfully.';
        }
    }

    /**
     * Render the appropriate page based on the user's authentication status and mode.
     */
    private function renderPage()
    {
        if ($this->loginHandler->isAuthenticated()) {
            $this->view->renderStatus();
            $this->view->renderLogoutButton();
        } else {
            // Pass the error message to the view
            $this->view->renderForm($this->mode, $this->errorMessage);
        }
    }
}
