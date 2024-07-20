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
    echo 'Welcome, ' . $data['username'];
} else {
    echo 'Access denied: ' . $data['error'];
}
?>
