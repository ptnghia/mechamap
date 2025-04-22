<!-- Admin CSS -->
<style>
    body {
        font-family: 'Inter', sans-serif;
    }
    .sidebar {
        min-height: calc(100vh - 56px);
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    }
    .sidebar .nav-link {
        color: #333;
        padding: .75rem 1rem;
        font-weight: 500;
    }
    .sidebar .nav-link.active {
        color: #0d6efd;
    }
    .sidebar .nav-link:hover {
        color: #0d6efd;
    }
    .sidebar .nav-link i {
        margin-right: .5rem;
    }
    .navbar-brand {
        padding-top: .75rem;
        padding-bottom: .75rem;
        font-size: 1rem;
        background-color: rgba(0, 0, 0, .25);
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
    }
    .footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
    .dropdown-menu {
        min-width: 12rem;
    }
    .dropdown-item {
        padding: 0.5rem 1rem;
    }
    .dropdown-item i {
        width: 1rem;
        text-align: center;
    }
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
</style>
