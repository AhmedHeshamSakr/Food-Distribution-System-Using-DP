<?php

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

require_once 'app/Models/Login.php';
require_once 'app/Models/FirebaseFacade.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents("php://input");
    $userData = json_decode($rawInput, true);

    
    try {
        $firebaseFacade = new FirebaseFacade();
      

        $verifiedIdToken = $firebaseFacade->auth->verifyIdToken($userData["token"],false,360000);
         // Extract user details from the token claims
         $uid = $verifiedIdToken->claims()->get('sub');
         $email = $verifiedIdToken->claims()->get('email');
         $firstName = $verifiedIdToken->claims()->get('given_name') ?? '';
         $lastName = $verifiedIdToken->claims()->get('family_name') ?? '';

        $uid = $verifiedIdToken->claims()->get('sub');
    
        $user = $auth->getUser($uid);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            "status" => "success",
            "message" => "Hello there.",
            "user" =>$user,
            
        ]);
    } catch (FailedToVerifyToken $e) {
        echo json_encode([
            
            "status" => "error",
            "message" => "Hello {$e}.",
        ]);
        echo 'The token is invalid: '.$e->getMessage();
    }
    
    
    // Log received data for debugging
    error_log("Received User Data: " . print_r($userData, true));

    
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login with Google</title>
    <!-- Firebase v8.x -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-auth.js"></script>
</head>
<body>
    <h1>Login with Google</h1>
    <button onclick="googleLogin()">Login with Google</button>
    <div id="greeting"></div>

    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyCRvhH01n6T79pR3x9Pbj-yjmBztpiCpmA",
            authDomain: "food-distribution-system-200e7.firebaseapp.com",
            projectId: "food-distribution-system-200e7",
            storageBucket: "food-distribution-system-200e7.firebasestorage.app",
            messagingSenderId: "111556773931",
            appId: "1:111556773931:web:f35a8a7d9a74947952026e"
        };

        firebase.initializeApp(firebaseConfig);

        const auth = firebase.auth();
        const provider = new firebase.auth.GoogleAuthProvider();

        function googleLogin() {
            auth.signInWithPopup(provider)
                .then(result => {
                    const user = result.user;

                    console.log("user is")
                    console.log(user)
                    user.getIdToken().then((token)=>{
                        console.log(token)
                        fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            name: user.displayName,
                            email: user.email,
                            token:token
                        })
                    })
                    .then(response=>  response.json())
                    .then(data => {
                        console.log(`response ${data}`);
                        alert(data.message);
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
                    })

                    
                })
                .catch(error => {
                    alert("Login failed.");
                    console.error("Error:", error);
                });
        }
    </script>
</body>
</html>
