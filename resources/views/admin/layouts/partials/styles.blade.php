<!-- Admin CSS -->
<style>
    /* Các style bổ sung cho admin */
    .dropdown-toggle::after {
        margin-left: 0.5rem;
    }
    .navbar-nav .dropdown-menu {
        position: absolute;
    }
    .navbar-dark .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.85);
    }
    .navbar-dark .navbar-nav .nav-link:hover {
        color: #fff;
    }

    /* Các style cho các thành phần cụ thể */
    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .card {
        margin-bottom: 1.5rem;
    }

    /* Các style cho các trang cụ thể */
    .media-preview {
        max-height: 150px;
        object-fit: contain;
    }

    .preview-item {
        margin-bottom: 1rem;
    }

    .preview-container {
        position: relative;
        padding-top: 75%;
        background-color: #f8f9fa;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .preview-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* Các style cho các biểu đồ */
    canvas {
        max-width: 100%;
    }
</style>
