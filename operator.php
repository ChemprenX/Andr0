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

            if ($userRole === 'operator') {
                include 'header.php';
                include 'sidebar.php';
                ?>
                <section>
                    <h2>Operator Panel</h2>
                    <p>Welcome to the Operator Panel, <?php echo htmlspecialchars($decoded->data->username); ?>!</p>
                    <form method="post" action="update_settings.php">
                        <label for="email">Operator Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($decoded->data->email); ?>" required>
                        <button type="submit">Update Email</button>
                    </form>
                </section>
                <?php
                include 'footer.php';
            } else {
                echo 'Access denied. Operators only.';
            }

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
