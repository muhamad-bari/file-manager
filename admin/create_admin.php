<?php
include '../db.php';

$username = 'admin';
$password = md5('admin'); // Ganti 'adminpassword' dengan password yang Anda inginkan
$role = 'admin';

// Check if admin already exists
$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "Admin user created successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Admin user already exists.";
}

$conn->close();
?>
