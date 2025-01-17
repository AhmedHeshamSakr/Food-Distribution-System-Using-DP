<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();

}// Include required files
require_once __DIR__ . '/../Models/Event.php';
require_once __DIR__ . '/../Models/Address.php';
require_once __DIR__ . '/../Views/EventView.php';
require_once __DIR__ . '/../Controllers/EventController.php';
require_once __DIR__ . '/../Views/VolunteerView.php';

/**
 * Helper function to determine if user is admin based on email
 */
function isAdminEmail($email): bool {
    return strpos($email, '@eadmin') !== false;
}

/**
 * Enhanced user context determination with better logic flow
 */
function determineUserContext(): bool {
    // First check for login
    if (!isset($_SESSION['email'])) {
        header("Location: login.php");
    }

    // Get the user's email
    $userEmail = $_SESSION['email'];
    // If view is specified in URL, use that
    if (isset($_GET['view'])) {
        $requestedView = $_GET['view'];
        
        // Validate the requested view based on user type
        if ($requestedView === 'eadmin' && !isAdminEmail($userEmail)) {
            // If non-admin tries to access admin view, force volunteer view
            $_SESSION['requested_view'] = 'volunteer';
            return true;
        }
        
        $_SESSION['requested_view'] = $requestedView;
        return $requestedView === 'volunteer';
    }
    
    // If no view in URL, check session
    if (isset($_SESSION['requested_view'])) {
        // Validate stored view preference
        if ($_SESSION['requested_view'] === 'eadmin' && !isAdminEmail($userEmail)) {
            $_SESSION['requested_view'] = 'volunteer';
            return true;
        }
        return $_SESSION['requested_view'] === 'volunteer';
    }
    
    // Default based on email type
    $defaultView = isAdminEmail($userEmail) ? 'eadmin' : 'volunteer';
    $_SESSION['requested_view'] = $defaultView;
    return $defaultView === 'volunteer';
}

// Add debugging to see what's happening
function debugInfo() {
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; font-family: monospace;'>";
    echo "Current Email: " . ($_SESSION['email'] ?? 'Not set') . "<br>";
    echo "Requested View: " . ($_SESSION['requested_view'] ?? 'Not set') . "<br>";
    echo "URL View Parameter: " . ($_GET['view'] ?? 'Not set') . "<br>";
    echo "</div>";
}

try {
    // For debugging purposes only - remove in production
    // debugInfo();
    
    $isVolunteerView = determineUserContext();
    $controller = new EventController($isVolunteerView);
    $controller->handleRequest();
    echo json_encode([
        'success' => true,
        'message' => 'Successfully registered'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    error_log($e->getMessage());
    echo '<div style="color: red; padding: 20px;">';
    echo 'An error occurred while processing your request. Please try again later.';
    echo '</div>';
}