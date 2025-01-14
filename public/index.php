<?php

require_once __DIR__ . '/../app/Controllers/LoginController.php';

$controller = new LoginController();
$controller->handleRequest();

?>
<script src="https://www.gstatic.com/firebasejs/9.6.11/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.11/firebase-auth.js"></script>
<script>
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
    projectId: "YOUR_PROJECT_ID",
    appId: "YOUR_APP_ID"
};

firebase.initializeApp(firebaseConfig);

function googleLogin() {
    const provider = new firebase.auth.GoogleAuthProvider();
    firebase.auth().signInWithPopup(provider).then((result) => {
        console.log(result.user.email);
    }).catch((error) => console.error(error));
}
</script>
