<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// Fetch total number of users with role 'user'
$sql_users_count = "SELECT COUNT(*) AS user_count FROM users WHERE role = 'user'";
$result_users_count = $conn->query($sql_users_count);
if ($result_users_count && $result_users_count->num_rows > 0) {
    $row_users_count = $result_users_count->fetch_assoc();
    $user_count = $row_users_count['user_count'];
} else {
    $user_count = 0; // Default value if query fails
}

// Fetch size limit information for each user
$sql_user_limits = "SELECT username, storage_limit FROM users WHERE role = 'user'";
$result_user_limits = $conn->query($sql_user_limits);
$user_limits = [];
if ($result_user_limits && $result_user_limits->num_rows > 0) {
    while ($row_user_limit = $result_user_limits->fetch_assoc()) {
        $username = $row_user_limit['username'];
        $storage_limit_kb = $row_user_limit['storage_limit']; // in KB
        $storage_limit_mb = $storage_limit_kb / (1024 * 1024); // Convert to MB for display
        $user_limits[$username] = $storage_limit_mb;
    }
}

echo "<h1>Admin Dashboard</h1>";
echo "<p>Welcome, " . $_SESSION['username'] . "</p>";

echo "<h2>User Information</h2>";
echo "<p>Total number of users: $user_count</p>";

echo "<h3>Storage Limits:</h3>";
echo "<ul>";
foreach ($user_limits as $username => $storage_limit_mb) {
    echo "<li>$username: $storage_limit_mb GB</li>";
}
echo "</ul>";
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
    <a href="../profile.php">Profile</a><br>
    <a href="../user/logout.php">Logout</a>
</body>
</html>
