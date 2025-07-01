// MechaMap Admin App.js
console.log('MechaMap Admin App.js loaded');

(function() {
    'use strict';
    
    // Initialize app when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('MechaMap Admin initialized');
        
        // Initialize sidebar
        initSidebar();
        
        // Initialize tooltips
        initTooltips();
        
        // Initialize dropdowns
        initDropdowns();
        
        // Initialize responsive features
        initResponsive();
    });
    
    // Sidebar functionality
    function initSidebar() {
        const sidebarToggle = document.querySelector('[data-toggle="sidebar"]');
        const sidebar = document.querySelector('.vertical-menu');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        }
    }
    
    // Initialize Bootstrap tooltips
    function initTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }
    
    // Initialize Bootstrap dropdowns
    function initDropdowns() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        }
    }
    
    // Responsive features
    function initResponsive() {
        // Mobile menu handling
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('show');
            });
        }
    }
    
})();
