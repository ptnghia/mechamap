<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="{{ route('admin.dashboard') }}">
        {{ config('app.name', 'Laravel') }} Admin
    </a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="w-100 d-flex">
        <form class="w-100 ms-3 me-3 d-none d-md-flex">
            <input class="form-control form-control-dark" type="text" placeholder="{{ __('Search') }}" aria-label="Search">
        </form>
    </div>

    <div class="navbar-nav flex-row">
        <div class="nav-item dropdown">
            <a class="nav-link px-3 dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ Auth::guard('admin')->user()->getAvatarUrl() }}" alt="{{ Auth::guard('admin')->user()->name }}" class="rounded-circle me-2" width="24" height="24">
                <span>{{ Auth::guard('admin')->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="bi bi-person me-2"></i>{{ __('My Profile') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.profile.password') }}"><i class="bi bi-key me-2"></i>{{ __('Change Password') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('profile.show', Auth::guard('admin')->user()->username) }}" target="_blank"><i class="bi bi-eye me-2"></i>{{ __('View Public Profile') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right me-2"></i>{{ __('View Site') }}</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<script>
    // Initialize dropdown when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Get the dropdown toggle element
        var dropdownToggle = document.getElementById('navbarDropdown');

        // Add click event listener
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();

            // Toggle the dropdown menu
            var dropdownMenu = this.nextElementSibling;
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
                this.setAttribute('aria-expanded', 'false');
            } else {
                dropdownMenu.classList.add('show');
                this.setAttribute('aria-expanded', 'true');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target)) {
                var dropdownMenu = dropdownToggle.nextElementSibling;
                if (dropdownMenu.classList.contains('show')) {
                    dropdownMenu.classList.remove('show');
                    dropdownToggle.setAttribute('aria-expanded', 'false');
                }
            }
        });
    });
</script>
