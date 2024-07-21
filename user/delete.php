<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$path = urldecode($_GET['path']);
$root_directory = '';

$sql = "SELECT root_directory FROM users WHERE username='$username'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $root_directory = 'users/' . $row['root_directory'];
} else {
    echo "User not found.";
    exit();
}

if (strpos($path, $root_directory) === 0 && file_exists($path)) {
    if (is_file($path)) {
        unlink($path);
        echo "File deleted successfully.";
    } else {
        echo "Cannot delete directory.";
    }
} else {
    echo "Invalid path.";
}
?>

<a href="index.php">Back to File Manager</a>
