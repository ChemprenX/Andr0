<?php
include 'config.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $payload = [
            'iss' => 'http://yourdomain.com',
            'aud' => 'http://yourdomain.com',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + (2 * 60 * 60), // Token berlaku selama 2 jam
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ];

        $jwt = JWT::encode($payload, $jwt_secret_key, 'HS256');

        echo json_encode([
            'message' => 'Login successful',
            'token' => $jwt
        ]);
    } else {
        echo json_encode([
            'message' => 'Invalid username or password'
        ]);
    }
}
?>
