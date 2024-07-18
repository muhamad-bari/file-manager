<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role']; // pastikan form register memiliki input untuk role
    $root_directory = ($role === 'admin') ? '../users/' : '../users/' . $username;
    $storage_limit = ($role === 'admin') ? '-1' : 1048576; // 1GB in MB, or 'unlimited' for admin

    // Check if username already exists
    $check_sql = "SELECT COUNT(*) as count FROM users WHERE username='$username'";
    $result = $conn->query($check_sql);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        // Username already exists, show alert
        echo "<script>alert('Username sudah digunakan'); window.location.href='register.php';</script>";
    } else {
        // Username does not exist, proceed with insert
        $sql = "INSERT INTO users (username, password, role, root_directory, storage_limit) VALUES ('$username', '$password', '$role', '$root_directory', '$storage_limit')";

        if ($conn->query($sql) === TRUE) {
            if ($role !== 'admin') {
                createUserDirectory($root_directory);
            }
            echo "<script>alert('User baru berhasil didaftarkan.'); window.location.href='login.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
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
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>

