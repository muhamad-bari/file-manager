<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// Fetch size limit information for each user
$sql_user_limits = "SELECT username, storage_limit, root_directory FROM users WHERE role = 'user'";
$result_user_limits = $conn->query($sql_user_limits);
$user_limits = [];
if ($result_user_limits && $result_user_limits->num_rows > 0) {
    echo "<h1>Manage Storage</h1>";
    echo "<table border='1'>";
    echo "<tr><th>Username</th><th>Storage Limit</th><th>Root Directory</th></tr>";
    while ($row_user_limit = $result_user_limits->fetch_assoc()) {
        $username = $row_user_limit['username'];
        $storage_limit_kb = $row_user_limit['storage_limit']; // in KB
        $storage_limit_gb = $storage_limit_kb / (1024 * 1024); // Convert to GB for display
        $root_directory = $row_user_limit['root_directory'];
        echo "<tr>";
        echo "<td>$username</td>";
        echo "<td>$storage_limit_gb GB</td>";
        echo "<td><a href='details.php?directory=$root_directory'>$root_directory</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No users found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Storage</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Admin Dashboard</a>
</body>
</html>
