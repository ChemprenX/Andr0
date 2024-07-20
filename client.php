<?php
session_start();

define('SSO_SERVER', 'http://localhost/auth_server.php'); // Ganti dengan URL server SSO Anda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $ch = curl_init(SSO_SERVER);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'username' => $username,
        'password' => $password
    ]));
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (isset($data['token'])) {
        $_SESSION['token'] = $data['token'];
        header('Location: protected_page.php');
        exit;
    } else {
        echo 'Login failed: ' . $data['error'];
    }
} else {
    echo '<form method="POST">
        Username: <input type="text" name="username"><br>
        Password: <input type="password" name="password"><br>
        <input type="submit" value="Login">
    </form>';
}
?>
