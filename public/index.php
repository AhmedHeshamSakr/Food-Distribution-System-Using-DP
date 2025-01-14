
<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the login controller
require_once __DIR__ . '/../app/Controllers/LoginController.php';

// Create the controller instance
$controller = new LoginController();

// Handle the request (process form submissions and render the appropriate page)
$controller->handleRequest();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Register</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card w-100" style="max-width: 400px;">
            <div class="card-body">
                <!-- Google Login Button -->
                <button class="btn btn-primary btn-block" onclick="googleLogin()">Login with Google</button>

                <!-- Email/Password Login Form -->
                <form id="emailLoginForm" class="mt-3">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Login with Email</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.6.11/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.11/firebase-auth.js"></script>

    <script>
    const firebaseConfig = {
        apiKey: "AIzaSyCRvhH01n6T79pR3x9Pbj-yjmBztpiCpmA",
        authDomain: "food-distribution-system-200e7.firebaseapp.com",
        projectId: "food-distribution-system-200e7",
        storageBucket: "food-distribution-system-200e7.firebasestorage.app",
        messagingSenderId: "111556773931",
        appId: "1:111556773931:web:f35a8a7d9a74947952026e"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    // Google Login Function
    function googleLogin() {
        const provider = new firebase.auth.GoogleAuthProvider();
        firebase.auth().signInWithPopup(provider).then((result) => {
            result.user.getIdToken().then((idToken) => {
                fetch('/app/Controllers/loginHandler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ loginType: 'google', idToken }),
                })
                .then((response) => response.json())
                .then((data) => console.log(data))
                .catch((error) => console.error(error));
            });
        }).catch((error) => console.error(error));
    }

    // Email Login Handler
    document.getElementById('emailLoginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        fetch('/app/Controllers/loginHandler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                loginType: 'email',
                email: email,
                password: password
            }),
        })
        .then((response) => response.json())
        .then((data) => console.log(data))
        .catch((error) => console.error(error));
    });
    </script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
