    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/jquery/jquery.min.js' : 'plugins/jquery/jquery.min.js' ?>"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/jquery-ui/jquery-ui.min.js' : 'plugins/jquery-ui/jquery-ui.min.js' ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/bootstrap/js/bootstrap.bundle.min.js' : 'plugins/bootstrap/js/bootstrap.bundle.min.js' ?>"></script>
    <!-- ChartJS -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/chart.js/Chart.min.js' : 'plugins/chart.js/Chart.min.js' ?>"></script>
    <!-- Sparkline -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/sparklines/sparkline.js' : 'plugins/sparklines/sparkline.js' ?>"></script>
    <!-- JQVMap -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/jqvmap/jquery.vmap.min.js' : 'plugins/jqvmap/jquery.vmap.min.js' ?>"></script>
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/jqvmap/maps/jquery.vmap.usa.js' : 'plugins/jqvmap/maps/jquery.vmap.usa.js' ?>"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/jquery-knob/jquery.knob.min.js' : 'plugins/jquery-knob/jquery.knob.min.js' ?>"></script>
    <!-- daterangepicker -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/moment/moment.min.js' : 'plugins/moment/moment.min.js' ?>"></script>
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/daterangepicker/daterangepicker.js' : 'plugins/daterangepicker/daterangepicker.js' ?>"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js' : 'plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js' ?>"></script>
    <!-- Summernote -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/summernote/summernote-bs4.min.js' : 'plugins/summernote/summernote-bs4.min.js' ?>"></script>
    <!-- overlayScrollbars -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js' : 'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js' ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dist/js/adminlte.js' : 'dist/js/adminlte.js' ?>"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dist/js/demo.js' : 'dist/js/demo.js' ?>"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/comercios/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/productos/') !== false) ? '../dist/js/pages/dashboard.js' : 'dist/js/pages/dashboard.js' ?>"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    
    <!-- Custom JS adicional si es necesario -->
    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>
</body>
</html>
