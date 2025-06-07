/**
 * Navigation Dropdown Functionality
 * Đảm bảo dropdown menu hoạt động đúng cách
 */
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra Bootstrap có sẵn không
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JavaScript chưa được tải!');
        return;
    }

    // Khởi tạo tất cả dropdown toggles
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    const dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    console.log(`Đã khởi tạo ${dropdownList.length} dropdown menus`);

    // Thêm event listener để debug
    dropdownElementList.forEach(function(dropdownToggleEl) {
        dropdownToggleEl.addEventListener('click', function(e) {
            console.log('Dropdown clicked:', this.id);
        });

        // Listen for Bootstrap dropdown events
        dropdownToggleEl.addEventListener('show.bs.dropdown', function() {
            console.log('Dropdown showing:', this.id);
        });

        dropdownToggleEl.addEventListener('shown.bs.dropdown', function() {
            console.log('Dropdown shown:', this.id);
        });

        dropdownToggleEl.addEventListener('hide.bs.dropdown', function() {
            console.log('Dropdown hiding:', this.id);
        });
    });
});
