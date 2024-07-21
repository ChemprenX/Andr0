<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_database";

$jwt_secret_key = "your_secret_key"; // Ubah dengan kunci rahasia Anda

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
