@extends('layouts.app')

@section('title', 'Marketplace - ' . config('app.name'))

@section('meta')
    <meta name="description" content="Discover mechanical engineering products, CAD files, and technical solutions from verified suppliers and manufacturers.">
    <meta name="keywords" content="mechanical engineering, CAD files, technical products, marketplace, suppliers, manufacturers">
@endsection

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        /* Product Cards */
        .product-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border: 1px solid #e9ecef;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
            background-color: #f8f9fa;
        }

        /* Category Cards */
        .category-card {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-decoration: none;
            color: white;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }

        /* Search Bar */
        .search-container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Loading animation */
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Cart Sidebar */
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
        }

        .cart-sidebar.open {
            right: 0;
        }

        .cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .cart-overlay.show {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <!-- Unified Header -->
    <x-header
        :show-banner="get_setting('show_banner', true)"
        :is-marketplace="true"
    />

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 fw-bold mb-4">MechaMap Marketplace</h1>
                        <p class="lead mb-4">Discover premium mechanical parts, CAD files, and engineering solutions</p>

                        <!-- Stats -->
                        <div class="row mb-5">
                            <div class="col-md-4">
                                <div class="text-center text-white">
                                    <h3 class="fw-bold">{{ number_format($stats['total_products'] ?? 0) }}</h3>
                                    <small>Products Available</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center text-white">
                                    <h3 class="fw-bold">{{ number_format($stats['total_sellers'] ?? 0) }}</h3>
                                    <small>Verified Sellers</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center text-white">
                                    <h3 class="fw-bold">{{ number_format($stats['total_categories'] ?? 0) }}</h3>
                                    <small>Categories</small>
                                </div>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="search-container">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" placeholder="Search products, parts, materials..." id="heroSearch">
                                <button class="btn btn-light" type="button" id="heroSearchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">Browse Categories</h2>
                <div class="row g-4" id="categoriesContainer">
                    <!-- Categories will be loaded here -->
                    <div class="col-12 text-center">
                        <div class="loading-spinner"></div>
                        <p class="mt-3">Loading categories...</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h2>Featured Products</h2>
                    <a href="#" class="btn btn-outline-primary">View All</a>
                </div>
                <div class="row g-4" id="productsContainer">
                    <!-- Products will be loaded here -->
                    <div class="col-12 text-center">
                        <div class="loading-spinner"></div>
                        <p class="mt-3">Loading products...</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-5">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-body">
                                <i class="fas fa-box fa-3x text-primary mb-3"></i>
                                <h3 class="fw-bold">10,000+</h3>
                                <p class="text-muted">Products Available</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-body">
                                <i class="fas fa-users fa-3x text-success mb-3"></i>
                                <h3 class="fw-bold">5,000+</h3>
                                <p class="text-muted">Active Sellers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-body">
                                <i class="fas fa-shipping-fast fa-3x text-warning mb-3"></i>
                                <h3 class="fw-bold">24/7</h3>
                                <p class="text-muted">Fast Delivery</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-body">
                                <i class="fas fa-shield-alt fa-3x text-info mb-3"></i>
                                <h3 class="fw-bold">100%</h3>
                                <p class="text-muted">Secure Payment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Cart Sidebar -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-sidebar" id="cartSidebar">
        <div class="p-4 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Shopping Cart</h5>
                <button type="button" class="btn-close" id="closeCart"></button>
            </div>
        </div>
        <div class="p-4" id="cartContent">
            <div class="text-center text-muted">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p>Your cart is empty</p>
                <small>Add some products to get started</small>
            </div>
        </div>
        <div class="p-4 border-top mt-auto">
            <div class="d-grid gap-2">
                <button class="btn btn-primary" disabled>Checkout</button>
                <button class="btn btn-outline-secondary" id="closeCart2">Continue Shopping</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5>MechaMap Marketplace</h5>
                    <p class="text-muted">Your trusted platform for mechanical engineering products and solutions.</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Mechanical Parts</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">CAD Files</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Materials</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Tools</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Contact Us</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Shipping Info</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Returns</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Connect</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Newsletter</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Community</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Blog</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Social Media</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-muted">
                <p>&copy; {{ date('Y') }} MechaMap. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    <script>
        // Global configuration
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            user: @json(Auth::user()),
            apiUrl: '{{ url('/api/v1/marketplace/v2') }}'
        };

        // Configure axios
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
            axios.defaults.baseURL = window.Laravel.apiUrl;
        }

        // Marketplace JavaScript
        class MarketplaceApp {
            constructor() {
                this.cart = [];
                this.categories = [];
                this.products = [];
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.loadCategories();
                this.loadProducts();
                this.setupCartToggle();
            }

            setupEventListeners() {
                // Search functionality
                document.getElementById('heroSearchBtn')?.addEventListener('click', () => {
                    this.performSearch();
                });

                document.getElementById('heroSearch')?.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.performSearch();
                    }
                });

                // Cart close buttons
                document.getElementById('closeCart')?.addEventListener('click', () => {
                    this.closeCart();
                });

                document.getElementById('closeCart2')?.addEventListener('click', () => {
                    this.closeCart();
                });

                document.getElementById('cartOverlay')?.addEventListener('click', () => {
                    this.closeCart();
                });
            }

            setupCartToggle() {
                // Listen for cart toggle events from header
                window.addEventListener('toggle-cart', () => {
                    this.toggleCart();
                });
            }

            performSearch() {
                const query = document.getElementById('heroSearch')?.value.trim();
                if (query) {
                    window.location.href = `/marketplace/search?q=${encodeURIComponent(query)}`;
                }
            }

            toggleCart() {
                const sidebar = document.getElementById('cartSidebar');
                const overlay = document.getElementById('cartOverlay');

                if (sidebar.classList.contains('open')) {
                    this.closeCart();
                } else {
                    this.openCart();
                }
            }

            openCart() {
                document.getElementById('cartSidebar').classList.add('open');
                document.getElementById('cartOverlay').classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            closeCart() {
                document.getElementById('cartSidebar').classList.remove('open');
                document.getElementById('cartOverlay').classList.remove('show');
                document.body.style.overflow = '';
            }

            async loadCategories() {
                try {
                    // Use real categories data
                    const categories = @json($categories ?? []);
                    this.renderCategories(categories);
                } catch (error) {
                    console.error('Error loading categories:', error);
                    this.showError('categoriesContainer', 'Failed to load categories');
                }
            }

            async loadProducts() {
                try {
                    // Use real featured products data
                    const products = @json($featuredProducts ?? []);
                    this.renderProducts(products);
                } catch (error) {
                    console.error('Error loading products:', error);
                    this.showError('productsContainer', 'Failed to load products');
                }
            }

            renderCategories(categories) {
                const container = document.getElementById('categoriesContainer');
                container.innerHTML = categories.map(category => `
                    <div class="col-md-4 col-lg-2 mb-4">
                        <a href="/marketplace/category/${category.id}" class="category-card text-decoration-none">
                            <i class="${category.icon} fa-2x mb-3"></i>
                            <h6 class="fw-bold">${category.name}</h6>
                            <small>${category.count} items</small>
                        </a>
                    </div>
                `).join('');
            }

            renderProducts(products) {
                const container = document.getElementById('productsContainer');
                container.innerHTML = products.map(product => `
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card product-card h-100">
                            <img src="${product.image}" class="card-img-top product-image" alt="${product.name}"
                                 onerror="this.src='/images/placeholder-product.jpg'">
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">${product.name}</h6>
                                <div class="mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-2">
                                            ${'★'.repeat(Math.floor(product.rating))}${'☆'.repeat(5-Math.floor(product.rating))}
                                        </div>
                                        <small class="text-muted">(${product.reviews})</small>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="text-primary mb-0">$${product.price}</h5>
                                        <button class="btn btn-outline-primary btn-sm" onclick="marketplace.addToCart(${product.id})">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            addToCart(productId) {
                // Add product to cart logic
                console.log('Adding product to cart:', productId);

                // Show success message
                this.showToast('Product added to cart!', 'success');

                // Update cart count in header
                this.updateCartCount();
            }

            updateCartCount() {
                const cartBadge = document.getElementById('cartCount');
                if (cartBadge) {
                    cartBadge.textContent = this.cart.length;
                }
            }

            showError(containerId, message) {
                const container = document.getElementById(containerId);
                container.innerHTML = `
                    <div class="col-12 text-center">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${message}
                        </div>
                    </div>
                `;
            }

            showToast(message, type = 'info') {
                // Simple toast notification
                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>
                    ${message}
                `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        }

        // Initialize marketplace app
        let marketplace;
        document.addEventListener('DOMContentLoaded', function() {
            marketplace = new MarketplaceApp();
        });
    </script>

    <!-- Authentication Modal -->
    @guest
    <x-auth-modal id="authModal" size="lg" />
    @endguest
</body>
</html>
