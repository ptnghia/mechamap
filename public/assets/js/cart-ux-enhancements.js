/**
 * MechaMap Cart UX Enhancements
 * Cải thiện trải nghiệm người dùng cho shopping cart
 */

class CartUXEnhancements {
    constructor() {
        this.init();
    }

    init() {
        this.createToastContainer();
        this.createConfirmationModal();
        this.setupEventListeners();
        this.initializeAnimations();
    }

    /**
     * Tạo toast notification container
     */
    createToastContainer() {
        if (document.getElementById('toast-container')) return;

        const toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    /**
     * Hiển thị toast notification
     */
    showToast(type, title, message, duration = 4000) {
        const toastId = 'toast-' + Date.now();
        const iconMap = {
            success: 'fa-check-circle text-success',
            error: 'fa-exclamation-circle text-danger',
            warning: 'fa-exclamation-triangle text-warning',
            info: 'fa-info-circle text-info'
        };

        const bgMap = {
            success: 'bg-success',
            error: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info'
        };

        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgMap[type]} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas ${iconMap[type]} me-2"></i>
                        <div>
                            <div class="fw-bold">${title}</div>
                            <div class="small">${message}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        const container = document.getElementById('toast-container');
        container.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: duration });
        toast.show();

        // Auto remove after hide
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });

        return toast;
    }

    /**
     * Tạo confirmation modal
     */
    createConfirmationModal() {
        if (document.getElementById('confirmationModal')) return;

        const modalHTML = `
            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title" id="confirmationModalLabel">Xác nhận</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <i id="confirmationIcon" class="fas fa-question-circle text-warning" style="font-size: 3rem;"></i>
                            </div>
                            <p id="confirmationMessage" class="text-center mb-0">Bạn có chắc chắn muốn thực hiện hành động này?</p>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-primary" id="confirmationConfirm">Xác nhận</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Hiển thị confirmation modal
     */
    showConfirmation(options = {}) {
        const {
            title = 'Xác nhận',
            message = 'Bạn có chắc chắn muốn thực hiện hành động này?',
            icon = 'fa-question-circle text-warning',
            confirmText = 'Xác nhận',
            cancelText = 'Hủy',
            confirmClass = 'btn-primary',
            onConfirm = () => {}
        } = options;

        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('confirmationModalLabel');
        const modalIcon = document.getElementById('confirmationIcon');
        const modalMessage = document.getElementById('confirmationMessage');
        const confirmBtn = document.getElementById('confirmationConfirm');

        modalTitle.textContent = title;
        modalIcon.className = `fas ${icon}`;
        modalMessage.textContent = message;
        confirmBtn.textContent = confirmText;
        confirmBtn.className = `btn ${confirmClass}`;

        // Remove previous event listeners
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        // Add new event listener
        newConfirmBtn.addEventListener('click', () => {
            bootstrap.Modal.getInstance(modal).hide();
            onConfirm();
        });

        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        return bsModal;
    }

    /**
     * Enhanced loading state
     */
    showEnhancedLoading(element, text = 'Đang xử lý...') {
        if (!element) return;

        const originalContent = element.innerHTML;
        element.dataset.originalContent = originalContent;
        element.disabled = true;

        element.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            ${text}
        `;

        return () => {
            element.disabled = false;
            element.innerHTML = originalContent;
            delete element.dataset.originalContent;
        };
    }

    /**
     * Optimistic UI update for quantity
     */
    optimisticQuantityUpdate(itemId, newQuantity, unitPrice) {
        const itemRow = document.querySelector(`[data-item-id="${itemId}"]`);
        if (!itemRow) return;

        const quantityInput = itemRow.querySelector('.quantity-input');
        const totalPriceElement = itemRow.querySelector('.item-total-price');
        const cartTotalElement = document.querySelector('.cart-total');

        // Store original values
        const originalQuantity = quantityInput.value;
        const originalTotalPrice = totalPriceElement.textContent;

        // Update UI optimistically
        quantityInput.value = newQuantity;
        const newTotalPrice = (newQuantity * unitPrice).toFixed(2);
        totalPriceElement.textContent = `$${newTotalPrice}`;

        // Add visual feedback
        itemRow.style.opacity = '0.7';
        itemRow.style.transition = 'opacity 0.3s ease';

        // Return rollback function
        return () => {
            quantityInput.value = originalQuantity;
            totalPriceElement.textContent = originalTotalPrice;
            itemRow.style.opacity = '1';
        };
    }

    /**
     * Smooth item removal animation
     */
    animateItemRemoval(itemId, callback) {
        const itemRow = document.querySelector(`[data-item-id="${itemId}"]`);
        if (!itemRow) return;

        // Add removal animation
        itemRow.style.transition = 'all 0.5s ease';
        itemRow.style.transform = 'translateX(-100%)';
        itemRow.style.opacity = '0';

        setTimeout(() => {
            itemRow.remove();
            if (callback) callback();
        }, 500);
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Quantity input debouncing
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                clearTimeout(e.target.debounceTimer);
                e.target.debounceTimer = setTimeout(() => {
                    this.handleQuantityChange(e.target);
                }, 500);
            }
        });

        // Enhanced remove buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item-btn')) {
                e.preventDefault();
                const itemId = e.target.dataset.itemId;
                this.handleItemRemoval(itemId);
            }
        });
    }

    /**
     * Handle quantity change with UX enhancements
     */
    handleQuantityChange(input) {
        const itemId = input.dataset.itemId;
        const newQuantity = parseInt(input.value);
        const unitPrice = parseFloat(input.dataset.unitPrice);

        if (newQuantity < 0) {
            input.value = 0;
            return;
        }

        // Optimistic UI update
        const rollback = this.optimisticQuantityUpdate(itemId, newQuantity, unitPrice);

        // Show loading on input
        const stopLoading = this.showEnhancedLoading(input, newQuantity);

        // Make API call
        fetch(`/marketplace/cart/update/${itemId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ quantity: newQuantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (newQuantity === 0) {
                    this.animateItemRemoval(itemId, () => {
                        this.updateCartTotals();
                    });
                } else {
                    this.updateCartTotals();
                }
                this.showToast('success', 'Thành công', 'Đã cập nhật giỏ hàng');
            } else {
                rollback();
                this.showToast('error', 'Lỗi', data.message || 'Không thể cập nhật giỏ hàng');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            rollback();
            this.showToast('error', 'Lỗi', 'Có lỗi xảy ra khi cập nhật giỏ hàng');
        })
        .finally(() => {
            stopLoading();
        });
    }

    /**
     * Handle item removal with confirmation
     */
    handleItemRemoval(itemId) {
        const itemRow = document.querySelector(`[data-item-id="${itemId}"]`);
        const productName = itemRow.querySelector('.product-name').textContent.trim();

        this.showConfirmation({
            title: 'Xóa sản phẩm',
            message: `Bạn có chắc chắn muốn xóa "${productName}" khỏi giỏ hàng?`,
            icon: 'fa-trash text-danger',
            confirmText: 'Xóa',
            confirmClass: 'btn-danger',
            onConfirm: () => {
                this.removeItemFromCart(itemId);
            }
        });
    }

    /**
     * Remove item from cart with animation
     */
    removeItemFromCart(itemId) {
        const itemRow = document.querySelector(`[data-item-id="${itemId}"]`);
        const removeBtn = itemRow.querySelector('.remove-item-btn');
        
        const stopLoading = this.showEnhancedLoading(removeBtn, 'Đang xóa...');

        fetch(`/marketplace/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.animateItemRemoval(itemId, () => {
                    this.updateCartTotals();
                    this.updateCartCount();
                });
                this.showToast('success', 'Thành công', 'Đã xóa sản phẩm khỏi giỏ hàng');
            } else {
                this.showToast('error', 'Lỗi', data.message || 'Không thể xóa sản phẩm');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showToast('error', 'Lỗi', 'Có lỗi xảy ra khi xóa sản phẩm');
        })
        .finally(() => {
            stopLoading();
        });
    }

    /**
     * Update cart totals
     */
    updateCartTotals() {
        fetch('/marketplace/cart/data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cart = data.cart;
                    
                    // Update totals in sidebar
                    const subtotalElement = document.querySelector('.cart-subtotal');
                    const totalElement = document.querySelector('.cart-total');
                    
                    if (subtotalElement) subtotalElement.textContent = `$${cart.subtotal}`;
                    if (totalElement) totalElement.textContent = `$${cart.total_amount}`;
                }
            })
            .catch(error => console.error('Error updating totals:', error));
    }

    /**
     * Update cart count in header
     */
    updateCartCount() {
        fetch('/marketplace/cart/count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartBadge = document.querySelector('#cartCount');
                    if (cartBadge) {
                        cartBadge.textContent = data.count;
                        cartBadge.style.display = data.count > 0 ? 'inline' : 'none';
                    }
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }

    /**
     * Initialize animations
     */
    initializeAnimations() {
        // Add hover effects to cart items
        document.querySelectorAll('.cart-item-row').forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.transform = 'translateY(-2px)';
                row.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                row.style.transition = 'all 0.3s ease';
            });

            row.addEventListener('mouseleave', () => {
                row.style.transform = 'translateY(0)';
                row.style.boxShadow = 'none';
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.cartUX = new CartUXEnhancements();
});
