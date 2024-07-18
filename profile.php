<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Fetch user details from database
$sql = "SELECT username, role, storage_limit FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $role = $row['role'];
    $storage_limit = $row['storage_limit'];

    // Convert storage limit to human-readable format
    function convertStorageLimit($storage_limit) {
        if ($storage_limit == -1) {
            return 'Unlimited';
        }

        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $size = $storage_limit;
        $unit = 'B';

        for ($i = 0; $i < count($units) - 1; $i++) {
            if ($size >= 1024) {
                $size /= 1024;
                $unit = $units[$i + 1];
            } else {
                break;
            }
        }

        return round($size, 2) . ' ' . $unit;
    }

    $storage_limit_display = convertStorageLimit($storage_limit);
} else {
    echo "User not found.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
</head>
<body>
    <h1>User Profile</h1>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>
    <p><strong>Storage Limit:</strong> <?php echo htmlspecialchars($storage_limit_display); ?></p>
</body>
</html>
