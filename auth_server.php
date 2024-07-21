<?php
require 'config.php';

require 'vendor/firebase/php-jwt/JWT.php';
use \Firebase\JWT\JWT;

session_start();

//define('SECRET_KEY', getenv('SECRET_KEY')); // Menggunakan variabel lingkungan
//define('ALGORITHM', 'HS256');


define('SECRET_KEY', 'your_secret_key'); // Ganti dengan kunci rahasia yang aman
define('ALGORITHM', 'HS256');

// Koneksi ke database
$mysqli = new mysqli('localhost', 'username', 'password', 'sso_example'); // Ganti dengan kredensial MySQL Anda

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

function generateToken($username, $role) {
    $payload = [
        'username' => $username,
        'role' => $role,
        'exp' => time() + 3600 // Token berlaku selama 1 jam
    ];
    return JWT::encode($payload, SECRET_KEY, ALGORITHM);
}

function verifyToken($token) {
    try {
        $decoded = JWT::decode($token, SECRET_KEY, [ALGORITHM]);
        return $decoded;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Periksa kredensial pengguna
    $stmt = $mysqli->prepare('SELECT user.id, role.role_name FROM user JOIN role ON user.role_id = role.id WHERE username = ? AND password = MD5(?)');
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $role_name);
        $stmt->fetch();
        $_SESSION['username'] = $username;
        echo json_encode(['token' => generateToken($username, $role_name)]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }

    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'];
    if ($data = verifyToken($token)) {
        echo json_encode(['username' => $data->username, 'role' => $data->role]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

$mysqli->close();
?>
