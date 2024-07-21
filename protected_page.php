<?php
session_start();

define('SSO_SERVER', 'http://localhost/auth_server.php'); // Ganti dengan URL server SSO Anda

if (!isset($_SESSION['token'])) {
    header('Location: client.php');
    exit;
}

$ch = curl_init(SSO_SERVER . '?token=' . urlencode($_SESSION['token']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (isset($data['username'])) {
    $username = $data['username'];
    $role = $data['role'];
    echo 'Welcome, ' . $username . '<br>';
    echo 'Your role is: ' . $role . '<br><br>';

    if ($role === 'admin') {
        echo '<a href="admin.php">Admin Dashboard</a><br>';
    }
    if ($role === 'user') {
        echo '<a href="user.php">User Dashboard</a><br>';
    }
    if ($role === 'operator') {
        echo '<a href="operator.php">Operator Dashboard</a><br>';
    }
} else {
    echo 'Access denied: ' . $data['error'];
}
?>
