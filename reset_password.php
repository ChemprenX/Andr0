<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];

    $sql = "SELECT * FROM password_resets WHERE reset_token = '$token' AND expires_at > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $resetRequest = $result->fetch_assoc();
        $userId = $resetRequest['user_id'];
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = '$hashedPassword' WHERE id = $userId";
        if ($conn->query($sql) === TRUE) {
            $sql = "DELETE FROM password_resets WHERE user_id = $userId";
            $conn->query($sql);
            echo 'Password has been reset successfully.';
        } else {
            echo 'Error updating password: ' . $conn->error;
        }
    } else {
        echo 'Invalid or expired token.';
    }
} else if (isset($_GET['token'])) {
    $token = $_GET['token'];
    ?>
    <form method="post">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <button type="submit">Reset Password</button>
    </form>
    <?php
} else {
    echo 'Invalid request.';
}
?>
