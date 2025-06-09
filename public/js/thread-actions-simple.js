// Simple Thread Actions - Form-based approach
// Không cần AJAX, chỉ sử dụng native form submissions

document.addEventListener('DOMContentLoaded', function() {
    console.log('Thread Actions: Form-based approach initialized');

    // Optional: Add confirmation dialogs for certain actions
    const bookmarkForms = document.querySelectorAll('form[action*="bookmark"]');
    const followForms = document.querySelectorAll('form[action*="follow"]');

    // Thêm loading state cho buttons khi submit form
    function addLoadingState(form) {
        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Đang xử lý...';

            // Khôi phục button sau 3 giây để tránh stuck state
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }, 3000);
        }
    }

    // Thêm event listeners cho forms
    [...bookmarkForms, ...followForms].forEach(form => {
        form.addEventListener('submit', function(e) {
            addLoadingState(this);
        });
    });
});
