<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

$username = $_SESSION['username'];

// Fetch user role from database
$sql = "SELECT role, storage_limit, root_directory FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $role = $row['role'];

    // Redirect admin to admin/index.php
    if ($role == 'admin') {
        header('Location: admin/admin_dashboard.php');
        exit();
    }

    // For non-admin users, fetch storage limit and root directory
    $storage_limit_kb = $row['storage_limit']; // in kilobytes
    $root_directory = str_replace('../users', 'users', $row['root_directory']); // replace ../users with users
} else {
    echo "<script>alert('User not found.'); window.location.href='index.php';</script>";
    exit();
}

// Calculate total, used, and remaining storage
$total_storage_limit_bytes = ($storage_limit_kb == -1) ? 'Unlimited' : $storage_limit_kb * 1024;
$used_storage_bytes = getTotalStorageUsed($root_directory);
$remaining_storage_bytes = ($storage_limit_kb == -1) ? 'Unlimited' : $total_storage_limit_bytes - $used_storage_bytes;

// Format the bytes to a readable format
$total_storage_limit = ($storage_limit_kb == -1) ? 'Unlimited' : formatBytes($total_storage_limit_bytes);
$used_storage = formatBytes($used_storage_bytes);
$remaining_storage = ($storage_limit_kb == -1) ? 'Unlimited' : formatBytes($remaining_storage_bytes);

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Function to ensure the folder is within the user's root directory
function isFolderWithinRoot($folder, $root_directory) {
    $real_folder = realpath($folder);
    $real_root = realpath($root_directory);
    return strpos($real_folder, $real_root) === 0;
}

// Handle folder navigation
$current_folder = isset($_GET['folder']) ? $_GET['folder'] : $root_directory;
// Ensure the current folder is within the root directory
if (!isFolderWithinRoot($current_folder, $root_directory)) {
    echo "<script>alert('Unauthorized access.'); window.location.href='../index.php';</script>";
    exit();
}
$current_display_folder = str_replace('users/', '', $current_folder); // Untuk display saja, tanpa 'users/'

// Determine the previous folder path
if ($current_folder !== $root_directory) {
    $parent_folder = dirname($current_folder);
} else {
    $parent_folder = null;
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Check if file size exceeds storage limit
    $total_storage_used = getTotalStorageUsed($current_folder); // Function to calculate total storage used
    $file_size = $file['size']; // in bytes

    if ($storage_limit_kb != -1 && ($total_storage_used + $file_size) > ($storage_limit_kb * 1024)) {
        echo "<script>alert('Storage limit exceeded. Cannot upload file.');</script>";
        exit();
    }

    // Proceed with file upload
    $target_dir = $current_folder . '/';
    $target_file = $target_dir . basename($file['name']);
    $uploadOk = 1;

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "<script>alert('File already exists.');</script>";
        $uploadOk = 0;
    }

    // Allow all file formats (remove format checking)
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<script>alert('Sorry, your file was not uploaded.');</script>";
    } else {
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            echo "<script>alert('The file ". basename($file['name']). " has been uploaded.');</script>";
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
        }
    }
}

// Handle file rename
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rename_file']) && isset($_POST['old_file_name']) && isset($_POST['new_file_name'])) {
    $old_file_name = $_POST['old_file_name'];
    $new_file_name = $_POST['new_file_name'];
    $old_file_path = $current_folder . '/' . $old_file_name;
    $new_file_path = $current_folder . '/' . $new_file_name;

    // Check if file exists and rename it
    if (file_exists($old_file_path)) {
        if (rename($old_file_path, $new_file_path)) {
            echo "<script>alert('File \"$old_file_name\" has been renamed to \"$new_file_name\".');</script>";
        } else {
            echo "<script>alert('Error renaming file \"$old_file_name\".');</script>";
        }
    } else {
        echo "<script>alert('File \"$old_file_name\" does not exist.');</script>";
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


// Function to format file size
function filesize_formatted($size) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $unit = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $unit++;
    }
    return round($size, 1) . ' ' . $units[$unit];
}

// Function to delete directory and all its contents recursively
function deleteDirectory($directory) {
    if (!is_dir($directory)) {
        return false;
    }

    $files = array_diff(scandir($directory), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$directory/$file")) ? deleteDirectory("$directory/$file") : unlink("$directory/$file");
    }

    return rmdir($directory);
}

// Handle file or directory deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file']) && isset($_POST['file_name'])) {
    $file_name = $_POST['file_name'];
    $file_path = $current_folder . '/' . $file_name;

    // Check if entity exists and delete it
    if (file_exists($file_path)) {
        if (is_dir($file_path)) {
            if (deleteDirectory($file_path)) {
                echo "<script>alert('Directory \"$file_name\" has been deleted.');</script>";
            } else {
                echo "<script>alert('Error deleting directory \"$file_name\".');</script>";
            }
        } else {
            if (unlink($file_path)) {
                echo "<script>alert('File \"$file_name\" has been deleted.');</script>";
            } else {
                echo "<script>alert('Error deleting file \"$file_name\".');</script>";
            }
        }
    } else {
        echo "<script>alert('Entity \"$file_name\" does not exist.');</script>";
    }
}

// Handle folder creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_folder']) && isset($_POST['folder_name'])) {
    $folder_name = $_POST['folder_name'];
    $curfol = $_POST["curfol"];
    $new_folder_path = $curfol . '/' . $folder_name;

    // Check if folder already exists
    if (file_exists($new_folder_path)) {
        echo "<script>alert('Folder already exists.'); window.location.href='?folder=$curfol';</script>";
    } else {
        // Create the new folder
        if (mkdir($new_folder_path, 0777, true)) {
            echo "<script>alert('Folder \"$folder_name\" created successfully.'); window.location.href='?folder=$curfol';</script>";
        } else {
            echo "<script>alert('Error creating folder \"$folder_name\".'); window.location.href='?folder=$curfol';</script>";
        }
    }
}
$conn->close();
?>