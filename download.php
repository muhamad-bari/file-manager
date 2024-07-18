<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Fetch user details including root directory
$sql = "SELECT root_directory FROM users WHERE username='$username'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $root_directory = str_replace('../users', 'users', $row['root_directory']); // replace ../users with users
}

// Check if file_name parameter exists
if (isset($_GET['file_name'])) {
    $file_path = $_GET['file_name'];

    // Validate file path to prevent directory traversal
    if (strpos($file_path, $root_directory) === 0 && file_exists($file_path)) {
        // Set headers for file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        ob_clean();
        flush();
        readfile($file_path);
        exit;
    } else {
        echo "File not found or access denied.";
    }
} else {
    echo "Invalid file request.";
}
?>
