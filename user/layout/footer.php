<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Made with <i class="fa-brands fa-php"></i>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2024 <a href="https://muhamad-bari.dev">Muhamad Bari</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/plugins/jszip/jszip.min.js"></script>
<script src="../assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- SweetAlert2 -->
<script src="../assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- ChartJS -->
<script src="../assets/plugins/chart.js/Chart.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.min.js"></script>
<!-- Page specific script -->
<script>
$(function () {
    function updatePreviousFolderButton() {
        var currentFolder = new URLSearchParams(window.location.search).get('folder');
        var rootDirectory = 'users/' + '<?php echo $_SESSION['username']; ?>';

        // If current folder is not the root directory, show the button
        if (currentFolder && currentFolder !== rootDirectory) {
            $('#previous-folder-btn').show();
        } else {
            $('#previous-folder-btn').hide();
        }
    }

    function myCustomFunction() {
        var currentFolder = new URLSearchParams(window.location.search).get('folder');
        var parentFolder = currentFolder.substring(0, currentFolder.lastIndexOf('/'));
        window.location.href = 'index-user.php?folder=' + encodeURIComponent(parentFolder);
    }

    $("#example1").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "buttons": [
            {
                text: 'Previous Folder',
                attr: {
                    id: 'previous-folder-btn'
                },
                action: function (e, dt, node, config) {
                    myCustomFunction();
                }
            }
        ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    // Call the function to update the button visibility on page load
    updatePreviousFolderButton();
});

$(document).on('click', '[data-toggle="modal"]', function() {
    var file = $(this).data('file');
    var folder = $(this).data('folder');
    $('#old_file_name').val(file);
    $('#rename_folder').val(folder);
});

var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData        = {
      labels: [
          'Used Storage',
          'Remaining Storage',
      ],
      datasets: [
        {
          data: [usedStorage, remainingStorage],
          backgroundColor : ['#f56954', '#d2d6de'],
        }
      ]
    }
    var donutOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(donutChartCanvas, {
      type: 'doughnut',
      data: donutData,
      options: donutOptions
    })

    $(document).ready(function() {
                var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                var loginSuccess = <?php echo $username; ?>;
                <?php unset($username); ?>

                if (loginSuccess) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Login Berhasil'
                    });
                } exit();
            });
</script>
</script>
</body>
</html>
