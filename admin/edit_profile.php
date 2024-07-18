<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = md5($_POST['current_password']);
    $new_password = md5($_POST['new_password']);
    $confirm_password = md5($_POST['confirm_password']);

    $username = $_SESSION['username'];
    $sql = "SELECT password FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if ($user['password'] == $current_password) {
        if ($new_password == $confirm_password) {
            $sql = "UPDATE users SET password='$new_password' WHERE username='$username'";
            if ($conn->query($sql) === TRUE) {
                echo "Password updated successfully.";
            } else {
                echo "Error updating password: " . $conn->error;
            }
        } else {
            echo "New passwords do not match.";
        }
    } else {
        echo "Current password is incorrect.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
</head>
<body>
    <h1>Edit Profile</h1>
    <form action="edit_profile.php" method="post">
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" required><br>
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required><br>
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" required><br>
        <input type="submit" value="Update Password">
    </form>
    <a href="admin_dashboard.php">Back to Admin Dashboard</a>
</body>
</html>
