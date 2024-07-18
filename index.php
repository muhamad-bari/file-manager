<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
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
        header('Location: admin/index.php');
        exit();
    }

    // For non-admin users, fetch storage limit and root directory
    $storage_limit = $row['storage_limit']; // in bytes
    $root_directory = str_replace('../users', 'users', $row['root_directory']); // replace ../users with users
} else {
    echo "User not found.";
    exit();
}


// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Check if file size exceeds storage limit
    $total_storage_used = getTotalStorageUsed($root_directory); // Function to calculate total storage used
    $file_size = $file['size'] / 1024;

    $max_storage_limit = $storage_limit; // Function to convert storage limit to bytes

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
            $fileinkb = filesize($file)/1024;
            $total_size += filesize($fileinkb);
        } elseif (is_dir($file)) {
            // Recursively calculate size for subdirectories
            $total_size += getTotalStorageUsed($file);
        }
    }

    return $total_size;
}


// Function to display list of files in user's directory
function displayUserFiles($root_directory) {
    $directory = $root_directory . '/';
    
    // Open a directory, and read its contents
    if (is_dir($directory)) {
        if ($dh = opendir($directory)) {
            echo "<h2>Files in your directory:</h2>";
            echo "<table border='1'>";
            echo "<tr><th>No.</th><th>Nama File</th><th>Jenis File</th><th>Size</th><th>Tanggal Ditambahkan</th><th>Aksi</th></tr>";
            
            $counter = 1;
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..") {
                    $file_path = $directory . $file;
                    echo "<tr>";
                    echo "<td>$counter</td>";
                    echo "<td>$file</td>";
                    echo "<td>". pathinfo($file_path, PATHINFO_EXTENSION) . "</td>";
                    echo "<td>". filesize_formatted(filesize($file_path)) . "</td>";
                    echo "<td>". date("Y-m-d H:i:s", filemtime($file_path)) . "</td>";
                    echo "<td>";
                    echo "<form method='post' style='display: inline;'>";
                    echo "<input type='hidden' name='file_name' value='$file'>";
                    echo "<button type='submit' name='delete_file'>Delete</button>";
                    echo "</form>";
                    echo "<form method='get' action='download.php' style='display: inline;'>";
                    echo "<input type='hidden' name='file_name' value='$file_path'>";
                    echo "<button type='submit'>Download</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                    $counter++;
                }
            }
            
            echo "</table>";
            closedir($dh);
        } else {
            echo "Could not open directory";
        }
    } else {
        echo "Directory does not exist";
    }
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

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file']) && isset($_POST['file_name'])) {
    $file_name = $_POST['file_name'];
    $file_path = $root_directory . '/' . $file_name;

    // Check if file exists and delete it
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo "File '$file_name' has been deleted.";
        } else {
            echo "Error deleting file '$file_name'.";
        }
    } else {
        echo "File '$file_name' does not exist.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $username; ?></h1>
    
    <form action="index.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file" id="file">
        <input type="submit" value="Upload File" name="submit">
    </form>

    <?php displayUserFiles($root_directory); ?>

    <a href="logout.php">Logout</a>
</body>
</html>
