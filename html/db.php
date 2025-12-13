<?php
// These match the docker-compose environment variables
$host = 'db';
$user = 'user';
$pass = 'password';
$dbname = 'ctf_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    // This helps us debug if it fails again
    die("Connection failed: " . $conn->connect_error);
}
?>