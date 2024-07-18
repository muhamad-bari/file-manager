<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "file_manager_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to create user directory
function createUserDirectory($directory) {
    $user_dir = 'users/' . $directory;
    if (!file_exists($user_dir)) {
        mkdir($user_dir, 0777, true);
    }
}
?>
