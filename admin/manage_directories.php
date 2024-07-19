<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $new_root_directory_type = $_POST['root_directory_type'];
    $storage_limit = $_POST['storage_limit'];

    $sql = "SELECT username, root_directory FROM users WHERE id='$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if ($new_root_directory_type == 'universal') {
            $new_root_directory = '../users/';
            $new_root_directory_db = 'universal'; // Just a string for DB entry, not a path
        } else {
            if (!empty($_POST['root_directory'])) {
                $new_root_directory = '../users/' . $_POST['root_directory'];
                $new_root_directory_db = '../users/' . $_POST['root_directory'];
                
                // Check if new directory already exists
                if (file_exists('../users/' . $new_root_directory)) {
                    echo "<script>
                            alert('Folder already exists.');
                            window.location.href = 'manage_directories.php';
                          </script>";
                    exit();
                }

                // Create new directory if it doesn't exist
                if (!file_exists('../users/' . $new_root_directory_db)) {
                    mkdir('../users/' . $new_root_directory_db, 0777, true);
                }
            } else {
                $new_root_directory_db = $row['root_directory'];
            }
        }

        // Update database
        $sql = "UPDATE users SET root_directory='$new_root_directory_db', storage_limit='$storage_limit' WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('Directory and storage limit updated successfully.');
                    window.location.href = 'manage_directories.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error updating database: " . $conn->error . "');
                    window.location.href = 'manage_directories.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('User not found.');
                window.location.href = 'manage_directories.php';
              </script>";
    }
}

$users = $conn->query("SELECT * FROM users");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User Directories</title>
</head>
<body>
    <h1>Manage User Directories</h1>
    <form action="manage_directories.php" method="post">
        <label for="user_id">Select User:</label>
        <select name="user_id" required>
            <?php while ($user = $users->fetch_assoc()) { ?>
                <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
            <?php } ?>
        </select>
        <label for="root_directory_type">New Root Directory Type:</label>
        <select name="root_directory_type" required>
            <option value="universal">Universal Access</option>
            <option value="manual">Input Manual</option>
        </select>
        <div id="manual_input" style="display: none;">
            <label for="root_directory">New Root Directory:</label>
            <input type="text" name="root_directory">
        </div>
        <label for="storage_limit">Storage Limit:</label>
        <select name="storage_limit" required>
            <option value="5242880">5GB</option>
            <option value="10485760">10GB</option>
            <option value="26214400">25GB</option>
            <option value="31457280">30GB</option>
            <option value="104857600">100GB</option>
            <option value="262144000">250GB</option>
            <option value="524288000">500GB</option>
            <option value="1048576000">1TB</option>
            <option value="-1">Unlimited</option>
        </select>
        <input type="submit" value="Update">
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const rootDirectoryType = document.querySelector('select[name="root_directory_type"]');
        const manualInput = document.getElementById('manual_input');

        rootDirectoryType.addEventListener('change', function() {
            if (rootDirectoryType.value === 'manual') {
                manualInput.style.display = 'block';
            } else {
                manualInput.style.display = 'none';
            }
        });

        // Trigger change event on page load
        rootDirectoryType.dispatchEvent(new Event('change'));
    });
    </script>

    <a href="admin_dashboard.php">Back to Admin Dashboard</a>
</body>
</html>
