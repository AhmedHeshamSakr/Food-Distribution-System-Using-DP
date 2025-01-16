<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Login Test</title>

    <!-- Use Firebase v8.x -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-auth.js"></script>

    <script>
        // Firebase Configuration
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

        const auth = firebase.auth();
        const provider = new firebase.auth.GoogleAuthProvider();

        function googleLogin() {
            auth.signInWithPopup(provider).then(result => {
                result.user.getIdToken().then(idToken => {
                    // Send the ID token to the backend for verification
                    fetch('test.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ idToken })
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data); // Log the backend response
                        alert(data); // Notify the user
                    })
                    .catch(err => console.error('Error:', err));
                });
            }).catch(err => console.error('Google Login Error:', err));
        }
    </script>
</head>
<body>
    <h1>Test Google Login</h1>
    <button onclick="googleLogin()">Login with Google</button>
</body>
</html>