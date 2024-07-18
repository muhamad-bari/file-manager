<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    if ($action == 'add') {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $role = $_POST['role'];

        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            createUserDirectory($username);
            echo "User added successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif ($action == 'edit') {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $role = $_POST['role'];

        $sql = "UPDATE users SET username='$username', role='$role' WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            echo "User updated successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif ($action == 'delete') {
        $user_id = $_POST['user_id'];
        $sql = "DELETE FROM users WHERE id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            echo "User deleted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
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
    <title>Manage Users</title>
</head>
<body>
    <h1>Manage Users</h1>
    <form action="manage_users.php" method="post">
        <input type="hidden" name="action" value="add">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <label for="role">Role:</label>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
        <input type="submit" value="Add User">
    </form>

    <h2>Existing Users</h2>
    <ul>
        <?php while ($user = $users->fetch_assoc()) { ?>
            <li>
                <form action="manage_users.php" method="post" style="display:inline;">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <input type="text" name="username" value="<?php echo $user['username']; ?>">
                    <select name="role">
                        <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                        <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                    </select>
                    <input type="submit" value="Edit">
                </form>
                <form action="manage_users.php" method="post" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this user?');">
                </form>
            </li>
        <?php } ?>
    </ul>
    <a href="index.php">Back to Admin Dashboard</a>
</body>
</html>

