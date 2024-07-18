<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

echo "<h1>Admin Dashboard</h1>";
echo "<p>Welcome, " . $_SESSION['username'] . "</p>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <a href="manage_storage.php">Manage Storage</a><br>
    <a href="manage_directories.php">Manage Directories</a><br>
    <a href="manage_users.php">Manage Users</a><br>
    <a href="edit_profile.php">Edit Profile</a><br>
    <a href="../logout.php">Logout</a>
</body>
</html>
