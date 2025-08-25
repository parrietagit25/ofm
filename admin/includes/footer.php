    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    
    <script>
        // Activar menú lateral
        document.addEventListener('DOMContentLoaded', function() {
            // Activar el menú correspondiente según la página actual
            const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
            const menuItems = document.querySelectorAll('.nav-link');
            
            menuItems.forEach(item => {
                if (item.getAttribute('href') && item.getAttribute('href').includes(currentPage)) {
                    item.classList.add('active');
                    // Activar el menú padre si existe
                    const parentMenu = item.closest('.nav-treeview');
                    if (parentMenu) {
                        parentMenu.style.display = 'block';
                        const parentLink = parentMenu.previousElementSibling;
                        if (parentLink) {
                            parentLink.classList.add('active');
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
