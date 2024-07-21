<?php
include 'config.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if ($authHeader) {
    list($jwt) = sscanf($authHeader, 'Bearer %s');

    if ($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key($jwt_secret_key, 'HS256'));
            $userRole = $decoded->data->role;

            include 'header.php';
            include 'sidebar.php';
            ?>
            <section>
                <h2>Dashboard</h2>
                <p>Welcome, <?php echo htmlspecialchars($decoded->data->username); ?>!</p>
                <p>Role: <?php echo htmlspecialchars($userRole); ?></p>
            </section>
            <?php
            include 'footer.php';

        } catch (Exception $e) {
            echo json_encode([
                'message' => 'Access denied',
                'error' => $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'message' => 'No token provided'
        ]);
    }
} else {
    echo json_encode([
        'message' => 'Authorization header not found'
    ]);
}
?>
