<!-- Admin CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/admin-pagination.css') }}" rel="stylesheet">

<style>
    /* Admin common CSS */
    :root {
      /* Colors */
      --primary-color: #3366CC;
      --secondary-color: #1DCABC;
      --success-color: #22C55E;
      --danger-color: #EF4444;
      --warning-color: #F59E0B;
      --info-color: #0EA5E9;
      --dark-color: #1E293B;
      --light-color: #F8FAFC;
      --gray-color: #6B7280;
      --border-color: #E2E8F0;
      --bg-color: #F1F5F9;

      /* Typography */
      --font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      --font-size-base: 0.875rem;
      --font-size-sm: 0.8125rem;
      --font-size-lg: 1rem;
      --font-size-xl: 1.125rem;
      --font-size-2xl: 1.25rem;
      --font-weight-normal: 400;
      --font-weight-medium: 500;
      --font-weight-bold: 600;
      --line-height-base: 1.5;

      /* Spacing */
      --spacing-1: 0.25rem;
      --spacing-2: 0.5rem;
      --spacing-3: 0.75rem;
      --spacing-4: 1rem;
      --spacing-5: 1.25rem;
      --spacing-6: 1.5rem;

      /* Border radius */
      --border-radius-sm: 0.25rem;
      --border-radius: 0.375rem;
      --border-radius-lg: 0.5rem;

      /* Shadows */
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);

      /* Transitions */
      --transition-speed: 0.2s;
    }

    /* Base styles */
    body {
      font-family: var(--font-family);
      font-size: var(--font-size-base);
      line-height: var(--line-height-base);
      color: var(--dark-color);
      background-color: var(--bg-color);
    }

    h1, h2, h3, h4, h5, h6 {
      font-weight: var(--font-weight-medium);
      margin-bottom: var(--spacing-4);
      color: var(--dark-color);
    }

    h1 { font-size: 1.75rem; }
    h2 { font-size: 1.5rem; }
    h3 { font-size: 1.25rem; }
    h4 { font-size: 1.125rem; }
    h5 { font-size: 1rem; }
    h6 { font-size: 0.875rem; }

    a {
      color: var(--primary-color);
      text-decoration: none;
      transition: color var(--transition-speed);
    }

    a:hover {
      color: #2952a3;
      text-decoration: none;
    }

    /* Card styles */
    .card {
      background-color: #fff;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      border: none;
      margin-bottom: var(--spacing-4);
      overflow: hidden;
    }

    .card-header {
      padding: var(--spacing-4);
      background-color: #fff;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-title {
      margin-bottom: 0;
      font-size: var(--font-size-lg);
      font-weight: var(--font-weight-medium);
    }

    .card-body {
      padding: var(--spacing-4);
    }

    .card-footer {
      padding: var(--spacing-4);
      background-color: #fff;
      border-top: 1px solid var(--border-color);
    }

    /* Table styles */
    .table {
      width: 100%;
      margin-bottom: var(--spacing-4);
      color: var(--dark-color);
      vertical-align: middle;
      border-color: var(--border-color);
    }

    .table th {
      padding: var(--spacing-3) var(--spacing-4);
      font-weight: var(--font-weight-medium);
      border-bottom: 2px solid var(--border-color);
      background-color: var(--light-color);
      font-size: var(--font-size-sm);
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }

    .table td {
      padding: var(--spacing-3) var(--spacing-4);
      border-bottom: 1px solid var(--border-color);
      vertical-align: middle;
    }

    .table-hover tbody tr:hover {
      background-color: rgba(0, 0, 0, 0.02);
    }

    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
    }

    /* Form styles */
    .form-label {
      display: block;
      margin-bottom: var(--spacing-2);
      font-weight: var(--font-weight-medium);
      font-size: var(--font-size-base);
      color: var(--dark-color);
    }

    .form-control,
    .form-select {
      display: block;
      width: 100%;
      padding: 0.375rem 0.75rem;
      font-size: var(--font-size-base);
      font-weight: var(--font-weight-normal);
      line-height: var(--line-height-base);
      color: var(--dark-color);
      background-color: #fff;
      background-clip: padding-box;
      border: 1px solid var(--border-color);
      border-radius: var(--border-radius);
      transition: border-color var(--transition-speed), box-shadow var(--transition-speed);
      height: calc(1.5em + 0.75rem + 2px);
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #a1c0ff;
      outline: 0;
      box-shadow: 0 0 0 0.2rem rgba(51, 102, 204, 0.25);
    }

    .form-text {
      margin-top: var(--spacing-1);
      font-size: var(--font-size-sm);
      color: var(--gray-color);
    }

    .form-group {
      margin-bottom: var(--spacing-4);
    }

    /* Button styles */
    .btn {
      display: inline-block;
      font-weight: var(--font-weight-medium);
      line-height: var(--line-height-base);
      text-align: center;
      text-decoration: none;
      vertical-align: middle;
      cursor: pointer;
      user-select: none;
      background-color: transparent;
      border: 1px solid transparent;
      padding: 0.375rem 0.75rem;
      font-size: var(--font-size-base);
      border-radius: var(--border-radius);
      transition: color var(--transition-speed), background-color var(--transition-speed), border-color var(--transition-speed), box-shadow var(--transition-speed);
    }

    .btn-sm {
      padding: 0.25rem 0.5rem;
      font-size: var(--font-size-sm);
      border-radius: var(--border-radius-sm);
    }

    .btn-lg {
      padding: 0.5rem 1rem;
      font-size: var(--font-size-lg);
      border-radius: var(--border-radius-lg);
    }

    .btn-primary {
      color: #fff;
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-primary:hover {
      color: #fff;
      background-color: #2952a3;
      border-color: #264d99;
    }

    .btn-secondary {
      color: #fff;
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
    }

    .btn-secondary:hover {
      color: #fff;
      background-color: #18a89c;
      border-color: #169e93;
    }

    .btn-success {
      color: #fff;
      background-color: var(--success-color);
      border-color: var(--success-color);
    }

    .btn-success:hover {
      color: #fff;
      background-color: #1ca14f;
      border-color: #1a974a;
    }

    .btn-danger {
      color: #fff;
      background-color: var(--danger-color);
      border-color: var(--danger-color);
    }

    .btn-danger:hover {
      color: #fff;
      background-color: #e02424;
      border-color: #d41f1f;
    }

    .btn-outline-primary {
      color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-outline-primary:hover {
      color: #fff;
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-outline-secondary {
      color: var(--secondary-color);
      border-color: var(--secondary-color);
    }

    .btn-outline-secondary:hover {
      color: #fff;
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
    }

    /* Modal styles */
    .modal-content {
      position: relative;
      display: flex;
      flex-direction: column;
      width: 100%;
      pointer-events: auto;
      background-color: #fff;
      background-clip: padding-box;
      border: 1px solid rgba(0, 0, 0, 0.2);
      border-radius: var(--border-radius-lg);
      outline: 0;
      box-shadow: var(--shadow-lg);
    }

    .modal-header {
      display: flex;
      flex-shrink: 0;
      align-items: center;
      justify-content: space-between;
      padding: var(--spacing-4);
      border-bottom: 1px solid var(--border-color);
      border-top-left-radius: calc(var(--border-radius-lg) - 1px);
      border-top-right-radius: calc(var(--border-radius-lg) - 1px);
    }

    .modal-title {
      margin-bottom: 0;
      line-height: var(--line-height-base);
      font-size: var(--font-size-lg);
      font-weight: var(--font-weight-medium);
    }

    .modal-body {
      position: relative;
      flex: 1 1 auto;
      padding: var(--spacing-4);
    }

    .modal-footer {
      display: flex;
      flex-wrap: wrap;
      flex-shrink: 0;
      align-items: center;
      justify-content: flex-end;
      padding: calc(var(--spacing-4) - var(--spacing-2)) var(--spacing-4);
      border-top: 1px solid var(--border-color);
      border-bottom-right-radius: calc(var(--border-radius-lg) - 1px);
      border-bottom-left-radius: calc(var(--border-radius-lg) - 1px);
    }

    .modal-footer > * {
      margin: var(--spacing-2);
    }

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
