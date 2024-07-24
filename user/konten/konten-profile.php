<?php
session_start();
include '../db.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
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

        $units = array('KB', 'MB', 'GB', 'TB');
        $size = $storage_limit;
        $unit = 'KB'; // Starting from KB since database values are in KB

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

    // Calculate used storage
    $total_used_storage = 0;
    $directory = "users/$username";
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($files as $file) {
        if ($file->isFile()) {
            $total_used_storage += $file->getSize();
        }
    }

    // Convert used storage to human-readable format
    function convertSize($size) {
        $units = array('Bytes', 'KB', 'MB', 'GB', 'TB');
        $unit = 0;
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        return round($size, 2) . ' ' . $units[$unit];
    }

    $total_used_storage_display = convertSize($total_used_storage);

    // Calculate remaining storage
    $remaining_storage = $storage_limit - ($total_used_storage / 1024); // Convert bytes to KB
    $remaining_storage_display = convertSize($remaining_storage * 1024); // Convert back to human-readable format

    if ($storage_limit == -1) {
        $remaining_storage_display = 'Unlimited';
    }
} else {
    echo "User not found.";
    exit();
}

// Handle password change
if (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $hashed_password = md5($new_password); // Assuming MD5 hashing; consider using bcrypt for better security

    $update_sql = "UPDATE users SET password='$hashed_password' WHERE username='$username'";
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Password changed successfully.'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error updating password.'); window.location.href='profile.php';</script>";
    }
}

//convert to js
$used_storage_js = $total_used_storage / 1024; // Convert bytes to KB
$total_storage_js = $storage_limit; // Already in KB
$remaining_storage_js = $remaining_storage * 1024; // Convert to bytes for chart

$conn->close();
?>

<script>
    var usedStorage = <?php echo json_encode($used_storage_js); ?>;
    var totalStorage = <?php echo json_encode($total_storage_js); ?>;
    var remainingStorage = <?php echo json_encode($remaining_storage_js); ?>;
</script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index-user.php">Home</a></li>
              <li class="breadcrumb-item active">User Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="../assets/dist/img/avatar5.png"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?php echo htmlspecialchars($username); ?></h3>

                <p class="text-muted text-center"><?php echo htmlspecialchars($role); ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Used Storage</b> <a class="float-right"><?php echo htmlspecialchars($total_used_storage_display); ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Remaining Storage</b> <a class="float-right"><?php echo htmlspecialchars($remaining_storage_display); ?> </a>
                  </li>
                </ul>

                <a href="#" class="btn btn-primary btn-block"><b>Upgrade</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#info" data-toggle="tab">Status</a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <!-- /.tab-pane -->
                  <div class="active tab-pane" id="info">
                    <!-- DONUT CHART -->
                    <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Total Storage: <?php echo htmlspecialchars($storage_limit_display); ?></h3>
                            </div>
                        <div class="card-body">
                            <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="settings">
                    <form action="profile.php" method="post" class="form-horizontal">
                      <div class="form-group row">
                        <label for="new_password" class="col-sm-2 col-form-label">New Password</label>
                        <div class="col-sm-10">
                          <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password">
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox"> I've remember the password 
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" name="change_password" class="btn btn-success">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>