<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="../assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="" class="navbar-brand">
        <img src="../assets/dist/img/AdminLTELogo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">File Manager Lite</span>
      </a>

      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="index-user.php" class="nav-link">Home</a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">Contact</a>
          </li>
        </ul>
      </div>
    <!-- Right navbar links -->
    <ul class="order-md-3 navbar-nav navbar-no-expand ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="#" class="btn btn-default" data-toggle="modal" data-target="#modal-create">
          <i class="fas fa-folder-plus"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" class="btn btn-default" data-toggle="modal" data-target="#modal-upload">
          <i class="fas fa-upload"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
          <i class="fas fa-user-circle"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">
          <i class="fas fa-sign-out-alt"></i>
          </a>
        </li>
      </ul>
  </nav>      
  <!-- /.navbar -->