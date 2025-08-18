<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?? 'Admin OFM' ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/fontawesome-free/css/all.min.css' : 'plugins/fontawesome-free/css/all.min.css' ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css' : 'plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css' ?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/icheck-bootstrap/icheck-bootstrap.min.css' : 'plugins/icheck-bootstrap/icheck-bootstrap.min.css' ?>">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/jqvmap/jqvmap.min.css' : 'plugins/jqvmap/jqvmap.min.css' ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dist/css/adminlte.min.css' : 'dist/css/adminlte.min.css' ?>">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/overlayScrollbars/css/OverlayScrollbars.min.css' : 'plugins/overlayScrollbars/css/OverlayScrollbars.min.css' ?>">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/daterangepicker/daterangepicker.css' : 'plugins/daterangepicker/daterangepicker.css' ?>">
    <!-- summernote -->
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/summernote/summernote-bs4.min.css' : 'plugins/summernote/summernote-bs4.min.css' ?>">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    
    <!-- Custom CSS para el menú de usuario -->
    <style>
        .user-menu .dropdown-toggle::after {
            display: none;
        }
        
        .user-menu .nav-link {
            padding: 0.5rem 1rem;
        }
        
        .user-menu .user-image {
            width: 30px;
            height: 30px;
            margin-right: 8px;
        }
        
        .user-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .user-header img {
            width: 80px;
            height: 80px;
            border: 3px solid rgba(255,255,255,0.2);
        }
        
        .user-header p {
            margin: 10px 0 0 0;
        }
        
        .user-header small {
            display: block;
            font-size: 12px;
            opacity: 0.8;
        }
        
        .user-footer {
            padding: 15px;
            background-color: #f8f9fa;
        }
        
        .user-footer .btn {
            border-radius: 20px;
            padding: 8px 20px;
            font-size: 14px;
        }
        
        .user-footer .btn-default {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        
        .user-footer .btn-default:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        
        .navbar-nav .nav-link {
            color: #495057;
            font-weight: 500;
        }
        
        .navbar-nav .nav-link:hover {
            color: #007bff;
        }
        
        .navbar-nav .nav-link.active {
            color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
            border-radius: 5px;
        }
        
        /* Estilos para el sidebar */
        .nav-header {
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            font-weight: bold;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            margin-top: 10px;
        }
        
        .sidebar-dark-primary .nav-header {
            background-color: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .nav-link.text-danger {
            color: #dc3545 !important;
        }
        
        .nav-link.text-danger:hover {
            color: #c82333 !important;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        .sidebar-dark-primary .nav-link.text-danger {
            color: #ff6b6b !important;
        }
        
        .sidebar-dark-primary .nav-link.text-danger:hover {
            color: #ff5252 !important;
            background-color: rgba(255, 107, 107, 0.1);
        }
    </style>
    
    <!-- Custom CSS adicional si es necesario -->
    <?php if (isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dist/img/AdminLTELogo.png' : 'dist/img/AdminLTELogo.png' ?>" alt="AdminLTELogo" height="60" width="60">
    </div>
