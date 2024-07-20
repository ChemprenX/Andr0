<?php
session_start();

define('SECRET_KEY', 'your_secret_key'); // Ganti dengan kunci rahasia yang aman

function generateToken($username) {
    $payload = json_encode([
        'username' => $username,
        'exp' => time() + 3600 // Token berlaku selama 1 jam
    ]);
    return base64_encode($payload) . '.' . hash_hmac('sha256', $payload, SECRET_KEY);
}

function verifyToken($token) {
    list($payload, $signature) = explode('.', $token);
    if (hash_hmac('sha256', base64_decode($payload), SECRET_KEY) === $signature) {
        $data = json_decode(base64_decode($payload), true);
        if ($data['exp'] > time()) {
            return $data['username'];
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ganti logika ini dengan logika autentikasi sesuai kebutuhan Anda
    if ($username === 'user' && $password === 'password') {
        $_SESSION['username'] = $username;
        echo json_encode(['token' => generateToken($username)]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'];
    if ($username = verifyToken($token)) {
        echo json_encode(['username' => $username]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
