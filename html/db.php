<?php
// Configuration for Render Deployment
$host = '127.0.0.1'; // Localhost inside the container
$user = 'ctf_user';
$pass = 'ctf_pass';
$dbname = 'ctf_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>