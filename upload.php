<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Fetch user details including storage limit and root directory
$sql = "SELECT storage_limit, root_directory FROM users WHERE username='$username'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $storage_limit = $row['storage_limit']; // in bytes or -1 for unlimited
    $root_directory = str_replace('../users', 'users', $row['root_directory']); // replace ../users with users
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Check if file size exceeds storage limit
    $total_storage_used = getTotalStorageUsed($root_directory); // Function to calculate total storage used
    $file_size = $file['size'] / 1024;
    var_dump($file_size);
    die;

    if ($storage_limit != -1 && ($total_storage_used + $file_size) > $storage_limit) {
        echo "Storage limit exceeded. Cannot upload file.";
        exit();
    }

    // Proceed with file upload
    $target_dir = $root_directory . '/';
    $target_file = $target_dir . basename($file['name']);
    $uploadOk = 1;

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "File already exists.";
        $uploadOk = 0;
    }

    // Allow all file formats (remove format checking)
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            echo "The file ". basename($file['name']). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Function to calculate total storage used by user
function getTotalStorageUsed($root_directory) {
    $directory = $root_directory . '/';
    $total_size = 0;

    foreach (glob($directory . "*") as $file) {
        if (is_file($file)) {
            $total_size += filesize($file);
        } elseif (is_dir($file)) {
            // Recursively calculate size for subdirectories
            $total_size += getTotalStorageUsed($file);
        }
    }

    return $total_size;
}
?>
