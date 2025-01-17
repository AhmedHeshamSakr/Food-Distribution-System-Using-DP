<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

 

    


    public function handleRequest()
    {
        // Ensure we have a clean session start
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Restore mode and error message from session if available
        $this->mode = $_SESSION['mode'] ?? 'login';
        $this->errorMessage = $_SESSION['errorMessage'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processFormSubmission();
        }

        $this->renderPage();
    }

    /**
     * Enhanced login handler that stores user ID and role in session
     */
    private function handleLogin()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Check for admin login first
        if (strpos($email, '@admin') !== false) {
            if ($this->loginHandler->login(['email' => $email, 'password' => $password])) {
                $this->initializeUserSession($email, true);
                header("Location: ../app/Views/AdminPageView.php");
                exit();
            }
            $this->errorMessage = 'Invalid admin credentials.';
            return;
        }

        // Handle regular user login
        if ($this->loginHandler->login(['email' => $email, 'password' => $password])) {
            $this->initializeUserSession($email, false);
            header("Location: ../app/Views/HomePageView.php");
            exit();
        }
        
        $this->errorMessage = 'Invalid email or password. Please try again.';
    }

    /**
     * Initialize user session with all necessary data
     * @param string $email User's email
     * @param bool $isAdmin Whether the user is an admin
     */
    private function initializeUserSession(string $email, bool $isAdmin): void
    {
        // Ensure clean session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get user details from login handler
        $userDetails = $this->loginHandler->getUserDetails($email);
        
        // Store essential user information in session
        $_SESSION['user_id'] = $userDetails['id'] ?? null;
        $_SESSION['email'] = $email;
        $_SESSION['is_admin'] = $isAdmin;
        $_SESSION['user_type'] = $userDetails['userTypeID'] ?? null;
        $_SESSION['full_name'] = $userDetails['firstName'] . ' ' . $userDetails['lastName'];
        $_SESSION['last_activity'] = time();
        
        // Set session timeout to 2 hours
        session_set_cookie_params(7200);
    }

    /**
     * Enhanced registration handler that automatically logs in the user
     */
    private function handleRegistration()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';
        $phoneNo = $_POST['phoneNo'] ?? '';
        $userTypeID = $_POST['userTypeID'] ?? '';
        $nationalID = $_POST['nationalID'] ?? '';
        $address_string = $_POST['address'] ?? '';

        $address = new Address($address_string, Address::getIdByName('Egypt'), 'City');
        $address->create();

        if ($this->loginHandler->register($email, $password, $firstName, $lastName, $phoneNo, $userTypeID, $nationalID, $address)) {
            // Automatically log in the user after successful registration
            if ($this->loginHandler->login(['email' => $email, 'password' => $password])) {
                $this->initializeUserSession($email, false);
                header("Location: ../app/Views/HomePageView.php");
                exit();
            }
            
            $this->errorMessage = 'Registration successful! Please log in.';
            $this->mode = 'login';
        } else {
            $this->errorMessage = 'Registration failed. The email might already be in use.';
            $this->mode = 'register';
        }
    }

    /**
     * Enhanced logout handler that properly cleans up session data
     */
    private function handleLogout()
    {
        // Clear all session data
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        $this->errorMessage = 'You have been logged out successfully.';
        $this->mode = 'login';
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