<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Admin JS -->
<script>
    // Enable tooltips and dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        dropdownElementList.forEach(function(dropdownToggleEl) {
            new bootstrap.Dropdown(dropdownToggleEl);
        });

        // Manual toggle for dropdowns (fallback)
        document.querySelectorAll('.dropdown-toggle').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Toggle dropdown manually
                var dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    if (this.classList.contains('show')) {
                        this.classList.remove('show');
                        dropdownMenu.classList.remove('show');
                    } else {
                        // Close all other dropdowns
                        document.querySelectorAll('.dropdown-toggle.show').forEach(function(el) {
                            el.classList.remove('show');
                        });
                        document.querySelectorAll('.dropdown-menu.show').forEach(function(el) {
                            el.classList.remove('show');
                        });

                        // Show this dropdown
                        this.classList.add('show');
                        dropdownMenu.classList.add('show');
                    }
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-toggle.show').forEach(function(el) {
                    el.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-menu.show').forEach(function(el) {
                    el.classList.remove('show');
                });
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert && bootstrap.Alert && typeof bootstrap.Alert === 'function') {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    });
</script>