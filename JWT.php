<?php
require 'vendor/php-jwt/JWT.php';

use \Firebase\JWT\JWT;

define('SECRET_KEY', 'your_secret_key');
define('ALGORITHM', 'HS256');

// Membuat token
function createToken($username) {
    $payload = [
        'username' => $username,
        'exp' => time() + 3600
    ];
    return JWT::encode($payload, SECRET_KEY, ALGORITHM);
}

// Memverifikasi token
function verifyToken($token) {
    try {
        $decoded = JWT::decode($token, SECRET_KEY, [ALGORITHM]);
        return $decoded;
    } catch (Exception $e) {
        return false;
    }
}

// Contoh penggunaan
$token = createToken('user');
echo 'Token: ' . $token . "\n";

$decoded = verifyToken($token);
if ($decoded) {
    echo 'Decoded: ';
    print_r($decoded);
} else {
    echo 'Invalid token';
}
?>
