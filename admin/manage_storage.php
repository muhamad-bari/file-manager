<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Implement logic to calculate and display storage usage

echo "<h1>Manage Storage</h1>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Storage</title>
</head>
<body>
    <p>Storage usage information goes here...</p>
    <a href="index.php">Back to Admin Dashboard</a>
</body>
</html>
