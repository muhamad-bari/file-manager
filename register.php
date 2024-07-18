<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role']; // pastikan form register memiliki input untuk role
    $root_directory = ($role === 'admin') ? 'admin_root_directory' : $username . " file manager";
    $storage_limit = ($role === 'admin') ? '-1' : 1048576; // 1GB in MB, or 'unlimited' for admin

    $sql = "INSERT INTO users (username, password, role, root_directory, storage_limit) VALUES ('$username', '$password', '$role', '$root_directory', '$storage_limit')";

    if ($conn->query($sql) === TRUE) {
        if ($role !== 'admin') {
            createUserDirectory($root_directory);
        }
        echo "New user registered successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form action="register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br>
        <label for="role">Role:</label>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
