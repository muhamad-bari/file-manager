<?php
session_start();
include '../db.php';

// Check if user is logged in as admin or redirect
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// Initialize variables
$directory = '';
$username = '';
$totalStorage = 0;
$usedStorage = 0;

// Check if directory parameter is set
if (isset($_GET['directory'])) {
    $directory = $_GET['directory'];

    // Validate directory to prevent directory traversal
    if (strpos($directory, '../users/') === 0) {
        // Fetch username from directory path
        $username = substr($directory, strlen('../users/'));
        $username = explode('/', $username)[0]; // Extract username part

        // Fetch storage limit for the user
        $sql = "SELECT storage_limit FROM users WHERE username='$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $totalStorage = $row['storage_limit']; // in KB
        }

        // Function to calculate used storage
        function calculateUsedStorage($directory) {
            $usedStorage = 0;
            if (is_dir($directory)) {
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $usedStorage += $file->getSize();
                    }
                }
            }
            return $usedStorage / 1024; // Convert to KB
        }

        // Calculate used storage
        $usedStorage = calculateUsedStorage($directory);

        // Function to display files in the user's directory
        function displayUserFiles($directory) {
            // Open directory and read its contents
            if (is_dir($directory)) {
                if ($dh = opendir($directory)) {
                    echo "<h2>Files in the directory:</h2>";
                    echo "<table border='1'>";
                    echo "<tr><th>No.</th><th>Nama File</th><th>Jenis File</th><th>Size</th><th>Tanggal Ditambahkan</th><th>Aksi</th></tr>";
                    $counter = 1;
                    while (($file = readdir($dh)) !== false) {
                        if ($file != "." && $file != "..") {
                            $file_path = $directory . '/' . $file;
                            $file_size = is_dir($file_path) ? 0 : filesize($file_path);
                            echo "<tr>";
                            echo "<td>$counter</td>";
                            echo "<td>";
                            if (is_dir($file_path)) {
                                echo "<a href='details.php?directory=$file_path'>$file/</a>"; // Add "/" if it's a directory
                            } else {
                                echo "$file";
                            }
                            echo "</td>";
                            echo "<td>". (is_dir($file_path) ? "Folder" : pathinfo($file_path, PATHINFO_EXTENSION)) . "</td>";
                            echo "<td>". (is_dir($file_path) ? "-" : filesize_formatted($file_size)) . "</td>";
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
                    echo "<script>alert('Could not open directory.');</script>";
                }
            } else {
                echo "<script>alert('Directory does not exist.');</script>";
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

        // Calculate remaining storage
        $remainingStorage = $totalStorage - $usedStorage;
    } else {
        echo "<script>alert('Invalid directory.');</script>";
    }
} else {
    echo "<script>alert('Directory parameter not specified.');</script>";
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file']) && isset($_POST['file_name'])) {
    $file_name = $_POST['file_name'];
    $file_path = $directory . '/' . $file_name;

    // Check if file exists and delete it
    if (file_exists($file_path)) {
        if (is_dir($file_path)) {
            if (deleteDirectory($file_path)) {
                echo "<script>alert('Directory $file_name has been deleted.');</script>";
            } else {
                echo "<script>alert('Error deleting directory $file_name.');</script>";
            }
        } else {
            if (unlink($file_path)) {
                echo "<script>alert('File $file_name has been deleted.');</script>";
            } else {
                echo "<script>alert('Error deleting file $file_name.');</script>";
            }
        }
    } else {
        echo "<script>alert('Entity $file_name does not exist.');</script>";
    }
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Directory Details</title>
</head>
<body>
    <h1>User Directory Details</h1>
    <p>Username: <?php echo $username; ?></p>
    <p>Directory: <?php echo $directory; ?></p>
    <p>Total Storage: <?php echo filesize_formatted($totalStorage * 1024); ?></p>
    <p>Used Storage: <?php echo filesize_formatted($usedStorage * 1024); ?></p>
    <p>Remaining Storage: <?php echo filesize_formatted(($totalStorage - $usedStorage) * 1024); ?></p>

    <!-- Display files in the user's directory -->
    <?php displayUserFiles($directory); ?>

    <a href="manage_storage.php">Back to Manage Storage</a>
</body>
</html>
