<?php

require_once __DIR__ . "/LoginContext.php";
require_once __DIR__ . "/../../config/firebase-config.php";

$loginContext = new LoginContext();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginType = $_POST['loginType'];

    if ($loginType === 'google') {
        $idToken = $_POST['idToken'];
        $withGoogle = new withGoogle($auth);
        $loginContext->setStrategy($withGoogle);
        $result = $loginContext->login($idToken);
        echo json_encode($result);
    } elseif ($loginType === 'email') {
        $credentials = [
            'email' => $_POST['email'],
            'password' => $_POST['password'],
        ];
        $withEmail = new withEmail($credentials['email'], $credentials['password']);
        $loginContext->setStrategy($withEmail);
        $result = $loginContext->login($credentials);

        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Login successful' : 'Invalid credentials',
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid login type']);
    }
}
?>
