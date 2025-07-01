/**
 * MechaMap Mini Cart UX Enhancements
 * Cải thiện trải nghiệm người dùng cho mini cart trong header
 */

class MiniCartEnhancements {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadMiniCart();
        this.initializeAnimations();
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Mini cart remove buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.mini-cart-remove-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.mini-cart-remove-btn');
                const itemId = btn.dataset.itemId;
                this.handleMiniCartRemoval(itemId, btn);
            }
        });

        // Cart toggle with animation
        const cartToggle = document.getElementById('cartToggle');
        if (cartToggle) {
            cartToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMiniCart();
            });
        }

        // Auto-refresh mini cart when cart is updated
        window.addEventListener('cart-updated', () => {
            this.loadMiniCart();
        });
    }

    /**
     * Handle mini cart item removal
     */
    handleMiniCartRemoval(itemId, button) {
        // Show loading state
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        fetch(`/marketplace/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Animate item removal
                const itemElement = button.closest('.mini-cart-item');
                this.animateItemRemoval(itemElement, () => {
                    this.loadMiniCart();
                    this.showMiniToast('success', 'Item removed from cart');
                    
                    // Dispatch event for other components
                    window.dispatchEvent(new CustomEvent('cart-updated'));
                });
            } else {
                this.showMiniToast('error', data.message || 'Failed to remove item');
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showMiniToast('error', 'Failed to remove item');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    /**
     * Animate item removal from mini cart
     */
    animateItemRemoval(element, callback) {
        element.style.transition = 'all 0.3s ease';
        element.style.transform = 'translateX(100%)';
        element.style.opacity = '0';

        setTimeout(() => {
            element.remove();
            if (callback) callback();
        }, 300);
    }

    /**
     * Toggle mini cart with animation
     */
    toggleMiniCart() {
        const miniCart = document.getElementById('miniCart');
        const cartToggle = document.getElementById('cartToggle');
        
        if (miniCart && cartToggle) {
            // Use Bootstrap dropdown
            const dropdown = new bootstrap.Dropdown(cartToggle);
            dropdown.toggle();
            
            // Add animation class
            miniCart.classList.add('mini-cart-animated');
        }
    }

    /**
     * Load mini cart data
     */
    loadMiniCart() {
        fetch('/marketplace/cart/data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateMiniCartUI(data.cart);
                }
            })
            .catch(error => console.error('Error loading mini cart:', error));
    }

    /**
     * Update mini cart UI with enhanced animations
     */
    updateMiniCartUI(cart) {
        const cartCount = document.getElementById('cartCount');
        const mobileCartCount = document.getElementById('mobileCartCount');
        const miniCartItemCount = document.getElementById('miniCartItemCount');
        const miniCartItems = document.getElementById('miniCartItems');
        const miniCartSubtotal = document.getElementById('miniCartSubtotal');
        const miniCartFooter = document.getElementById('miniCartFooter');

        // Update cart counts with animation
        this.animateCountUpdate(cartCount, cart.total_items);
        this.animateCountUpdate(mobileCartCount, cart.total_items);
        this.animateCountUpdate(miniCartItemCount, cart.total_items);

        if (cart.total_items === 0) {
            // Show enhanced empty state
            if (miniCartFooter) miniCartFooter.style.display = 'none';
            miniCartItems.innerHTML = `
                <div class="text-center text-muted py-4 mini-cart-empty">
                    <div class="mb-3">
                        <i class="fas fa-shopping-cart" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                    <h6 class="mb-2">Your cart is empty</h6>
                    <p class="small mb-3">Add some products to get started</p>
                    <a href="/marketplace/products" class="btn btn-primary btn-sm">
                        <i class="fas fa-shopping-bag me-1"></i>
                        Start Shopping
                    </a>
                </div>
            `;
        } else {
            // Show cart items with enhanced UI
            if (miniCartFooter) miniCartFooter.style.display = 'block';

            let itemsHTML = '';
            cart.items.forEach((item, index) => {
                itemsHTML += `
                    <div class="mini-cart-item border-bottom" style="animation-delay: ${index * 0.1}s;">
                        <div class="p-3">
                            <div class="row align-items-center">
                                <div class="col-3">
                                    ${item.product_image ?
                                        `<img src="${item.product_image}" class="img-fluid rounded shadow-sm" alt="${item.product_name}" style="height: 50px; object-fit: cover;">` :
                                        `<div class="bg-light rounded d-flex align-items-center justify-content-center shadow-sm" style="height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>`
                                    }
                                </div>
                                <div class="col-6">
                                    <h6 class="mb-1 small fw-bold text-truncate" title="${item.product_name}">
                                        ${item.product_name}
                                    </h6>
                                    <div class="small text-muted mb-1">
                                        <i class="fas fa-cube me-1"></i>
                                        Qty: ${item.quantity}
                                    </div>
                                    ${item.is_on_sale ?
                                        `<div class="small">
                                            <span class="text-danger fw-bold">$${item.sale_price}</span>
                                            <span class="text-muted text-decoration-line-through ms-1">$${item.unit_price}</span>
                                        </div>` :
                                        `<div class="small fw-bold">$${item.unit_price}</div>`
                                    }
                                </div>
                                <div class="col-3 text-end">
                                    <div class="fw-bold text-primary mb-2">$${item.total_price}</div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mini-cart-remove-btn" 
                                            data-item-id="${item.id}" title="Remove item">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            miniCartItems.innerHTML = itemsHTML;

            // Update subtotal with animation
            if (miniCartSubtotal) {
                this.animateValueUpdate(miniCartSubtotal, `$${cart.subtotal}`);
            }
        }
    }

    /**
     * Animate count updates
     */
    animateCountUpdate(element, newCount) {
        if (!element) return;

        const oldCount = parseInt(element.textContent) || 0;
        
        if (oldCount !== newCount) {
            element.style.transform = 'scale(1.2)';
            element.style.transition = 'transform 0.2s ease';
            
            setTimeout(() => {
                element.textContent = newCount;
                element.style.display = newCount > 0 ? 'inline' : 'none';
                element.style.transform = 'scale(1)';
            }, 100);
        }
    }

    /**
     * Animate value updates
     */
    animateValueUpdate(element, newValue) {
        if (!element) return;

        element.style.opacity = '0.5';
        element.style.transition = 'opacity 0.2s ease';
        
        setTimeout(() => {
            element.textContent = newValue;
            element.style.opacity = '1';
        }, 100);
    }

    /**
     * Show mini toast notification
     */
    showMiniToast(type, message) {
        // Create mini toast for cart actions
        const toastHTML = `
            <div class="mini-toast mini-toast-${type}">
                <i class="fas ${type === 'success' ? 'fa-check' : 'fa-exclamation-triangle'} me-2"></i>
                ${message}
            </div>
        `;

        const toastContainer = document.querySelector('.toast-container') || document.body;
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toast = toastContainer.lastElementChild;
        
        // Show toast
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Hide and remove toast
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    /**
     * Initialize animations
     */
    initializeAnimations() {
        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            .mini-cart-animated {
                animation: miniCartSlideIn 0.3s ease;
            }
            
            @keyframes miniCartSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .mini-cart-item {
                animation: fadeInUp 0.3s ease forwards;
                opacity: 0;
                transform: translateY(10px);
            }
            
            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .mini-cart-item:hover {
                background-color: #f8f9fa;
                transition: background-color 0.2s ease;
            }
            
            .mini-cart-remove-btn:hover {
                transform: scale(1.1);
                transition: transform 0.2s ease;
            }
            
            .mini-toast {
                position: fixed;
                top: 80px;
                right: 20px;
                background: white;
                border-radius: 8px;
                padding: 12px 16px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
                font-size: 14px;
                border-left: 4px solid;
            }
            
            .mini-toast.show {
                opacity: 1;
                transform: translateX(0);
            }
            
            .mini-toast-success {
                border-left-color: #28a745;
                color: #28a745;
            }
            
            .mini-toast-error {
                border-left-color: #dc3545;
                color: #dc3545;
            }
            
            .mini-cart-empty {
                animation: fadeIn 0.5s ease;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.miniCartUX = new MiniCartEnhancements();
    
    // Override global functions for compatibility
    window.loadMiniCart = () => window.miniCartUX.loadMiniCart();
    window.updateMiniCartUI = (cart) => window.miniCartUX.updateMiniCartUI(cart);
    window.removeMiniCartItem = (itemId) => {
        const btn = document.querySelector(`[data-item-id="${itemId}"]`);
        if (btn) window.miniCartUX.handleMiniCartRemoval(itemId, btn);
    };
});
