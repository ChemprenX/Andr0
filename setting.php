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
            $userId = $decoded->data->id;
            $userRole = $decoded->data->role;

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = $_POST['email'];
                $sql = "UPDATE users SET email = '$email' WHERE id = $userId";
                if ($conn->query($sql) === TRUE) {
                    echo "Email updated successfully";
                } else {
                    echo "Error updating email: " . $conn->error;
                }
            }

            include 'header.php';
            include 'sidebar.php';
            ?>
            <section>
                <h2>Settings</h2>
                <form method="post">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <button type="submit">Update Email</button>
                </form>
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
