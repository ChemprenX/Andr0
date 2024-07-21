password_reset.php
<?php
include 'config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $resetToken = bin2hex(random_bytes(50));
        $expiresAt = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $sql = "INSERT INTO password_resets (user_id, reset_token, expires_at) VALUES ({$user['id']}, '$resetToken', '$expiresAt')";
        if ($conn->query($sql) === TRUE) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.example.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@example.com';
                $mail->Password = 'your_email_password';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('your_email@example.com', 'Your Application');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset';
                $mail->Body    = "Click <a href='http://yourdomain.com/reset_password.php?token=$resetToken'>here</a> to reset your password.";

                $mail->send();
                echo 'Password reset email has been sent.';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo 'No user found with that email address.';
    }
}
?>
