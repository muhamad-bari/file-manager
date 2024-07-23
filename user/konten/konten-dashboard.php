<?php include 'user.php'; ?>

<?php if (isset($_SESSION['login_success'])): ?>
    <script>
        $(document).ready(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            var loginSuccess = <?php echo $_SESSION['login_success'] ? 'true' : 'false'; ?>;
            <?php unset($_SESSION['login_success']); ?>

            if (loginSuccess) {
                Toast.fire({
                    icon: 'success',
                    title: 'Login Berhasil'
                });
            }
        });
    </script>
<?php endif; ?>
<?php
// Function to display list of files in user's directory
function displayUserFiles($root_directory, $current_folder) {
  $directory = $current_folder . '/';
  
  if (is_dir($directory)) {
      if ($dh = opendir($directory)) {
          echo "<table id='example1' class='table table-bordered table-striped'>";
          echo "<thead>";
          echo "<tr><th>No.</th><th>Nama File</th><th>Jenis File</th><th>Size</th><th>Tanggal Ditambahkan</th><th>Aksi</th></tr>";
          echo "</thead>";
          echo "<tbody>";
          
          $counter = 1;
          while (($file = readdir($dh)) !== false) {
              if ($file != "." && $file != "..") {
                  $file_path = $directory . $file;
                  echo "<tr>";
                  echo "<td>$counter</td>";
                  echo "<td>";
                  if (is_dir($file_path)) {
                      echo "<a href='index-user.php?folder=$file_path'>$file/</a>";
                  } else {
                      echo "$file";
                  }
                  echo "</td>";
                  echo "<td>". (is_dir($file_path) ? "Folder" : pathinfo($file_path, PATHINFO_EXTENSION)) . "</td>";
                  echo "<td>". (is_dir($file_path) ? "-" : filesize_formatted(filesize($file_path))) . "</td>";
                  echo "<td>". date("Y-m-d H:i:s", filemtime($file_path)) . "</td>";
                  echo "<td>";
                  echo "<form method='post' action='user.php' style='display: inline;'>";
                  echo "<input type='hidden' name='file_name' value='$file'>";
                  echo "<input type='hidden' name='folder' value='$current_folder'>";
                  echo "<button type='submit' name='delete_file' style='background: none; border: none; cursor: pointer;'><i class='fas fa-trash-alt'></i></button>";
                  echo "</form>";
                  echo "<form method='get' action='download.php' style='display: inline;'>";
                  echo "<input type='hidden' name='file_name' value='$file_path'>";
                  echo "<button type='submit' style='background: none; border: none; cursor: pointer;'><i class='fas fa-download'></i></button>";
                  echo "</form>";
                  echo "<button type='button' style='background: none; border: none; cursor: pointer;' data-file='$file' data-folder='$current_folder' data-toggle='modal' data-target='#modal-rename'><i class='fas fa-edit'></i></button>";
                  echo "</td>";
                  echo "</tr>";
                  $counter++;
              }
          }
          
          echo "</tbody>";
          echo "</table>";
          closedir($dh);
      } else {
          echo "<script>alert('Could not open directory');</script>";
      }
  } else {
      echo "<script>alert('Directory does not exist');</script>";
  }
}
?>
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"> Welcome, <small><?php echo $username; ?></small></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Home / <?php echo $current_display_folder; ?> / </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
        <!-- modal rename folder-->
<!-- Form rename modal -->
<form action="user.php" method="post">
    <div class="modal fade" id="modal-rename">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Rename Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="folder_name">Input New Name</label>
                        <input type="text" class="form-control" id="folder_name" name="new_file_name" placeholder="New Name">
                        <input type="hidden" name="old_file_name" id="old_file_name">
                        <input type="hidden" name="folder" id="rename_folder">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="rename_file">Rename</button>
                </div>
            </div>
        </div>
    </div>
</form>

        <!-- /.modal -->
              <!-- modal create folder-->
          <form action="user.php" method="post">
            <div class="modal fade" id="modal-create">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Create Folder</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                          <label for="folder_name">Input Folder Name</label>
                          <input type="text" class="form-control" id="folder_name" name="folder_name" placeholder="Folder Name">
                          <input type="hidden" name="curfol" value="<?= isset($_GET['folder']) ? $_GET['folder'] : $root_directory?>">
                    </div>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="create_folder">Create</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        <!-- /.modal -->
        <!-- modal Upload File-->
          <form action="user.php<?php echo isset($_GET['folder']) ? '?folder=' . urlencode($_GET['folder']) : ''; ?>" method="post" enctype="multipart/form-data">
            <div class="modal fade" id="modal-upload">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Upload File</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                          <label for="file">File input</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" name="file" id="file">
                              <label class="custom-file-label" for="file">Choose file</label>
                            </div>
                          </div>
                    </div>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" value="Upload File" name="submit">Upload</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        <!-- /.modal -->

        <!-- Info boxes -->
        <div class="row">
          <div class="col col-sm col-md">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-database"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Storage</span>
                <span class="info-box-number">
                <?php echo $total_storage_limit; ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col col-sm col-md">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-folder-open"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Used Storage</span>
                <span class="info-box-number"><?php echo $used_storage; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>
          <div class="col col-sm col-md">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-hdd"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Remaining Storage</span>
                <span class="info-box-number"><?php echo $remaining_storage; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <div class="col-12">
          <div class="card">
              <div class="card-header">
                <h3 class="card-title">Files In Your Directory</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
              <?php displayUserFiles($root_directory, $current_folder); ?>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
        </div>

        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->