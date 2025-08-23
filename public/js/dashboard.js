/**
 * Dashboard JavaScript
 * Handles dashboard functionality and interactions
 */

(function() {
    'use strict';

    // Dashboard namespace
    window.Dashboard = window.Dashboard || {};

    /**
     * Dashboard initialization
     */
    Dashboard.init = function() {
        console.log('Dashboard: Initializing...');
        
        // Initialize components
        Dashboard.initSidebar();
        Dashboard.initAlerts();
        Dashboard.initTooltips();
        Dashboard.initPopovers();
        Dashboard.initCharts();
        Dashboard.initRefresh();
        
        console.log('Dashboard: Initialized successfully');
    };

    /**
     * Sidebar functionality
     */
    Dashboard.initSidebar = function() {
        const wrapper = document.querySelector('.dashboard-wrapper');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        const overlay = document.querySelector('.sidebar-overlay');

        // Create toggle button if it doesn't exist
        if (!toggleBtn && window.innerWidth <= 768) {
            const btn = document.createElement('button');
            btn.className = 'sidebar-toggle btn btn-primary';
            btn.innerHTML = '<i class="fas fa-bars"></i>';
            btn.setAttribute('aria-label', 'Toggle Sidebar');
            document.body.appendChild(btn);
            
            btn.addEventListener('click', function() {
                if (wrapper) {
                    wrapper.classList.toggle('sidebar-open');
                }
            });
        }

        // Create overlay if it doesn't exist
        if (!overlay && wrapper) {
            const overlayEl = document.createElement('div');
            overlayEl.className = 'sidebar-overlay';
            wrapper.appendChild(overlayEl);
            
            overlayEl.addEventListener('click', function() {
                wrapper.classList.remove('sidebar-open');
            });
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && wrapper) {
                wrapper.classList.remove('sidebar-open');
            }
        });
    };

    /**
     * Auto-hide alerts
     */
    Dashboard.initAlerts = function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        
        alerts.forEach(function(alert) {
            // Auto-hide after 5 seconds
            setTimeout(function() {
                if (alert && alert.parentNode) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        });
    };

    /**
     * Initialize Bootstrap tooltips
     */
    Dashboard.initTooltips = function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    };

    /**
     * Initialize Bootstrap popovers
     */
    Dashboard.initPopovers = function() {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    };

    /**
     * Initialize charts (placeholder for future chart implementation)
     */
    Dashboard.initCharts = function() {
        // Placeholder for chart initialization
        // Can be extended with Chart.js or other charting libraries
        console.log('Dashboard: Charts placeholder initialized');
    };

    /**
     * Initialize refresh functionality
     */
    Dashboard.initRefresh = function() {
        const refreshBtns = document.querySelectorAll('[data-refresh]');
        
        refreshBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('data-refresh');
                Dashboard.refreshSection(target);
            });
        });
    };

    /**
     * Refresh a specific section
     */
    Dashboard.refreshSection = function(sectionId) {
        const section = document.getElementById(sectionId);
        if (!section) return;

        // Add loading state
        section.classList.add('dashboard-loading');
        
        // Simulate refresh (replace with actual AJAX call)
        setTimeout(function() {
            section.classList.remove('dashboard-loading');
            console.log('Dashboard: Section refreshed:', sectionId);
        }, 1000);
    };

    /**
     * Utility functions
     */
    Dashboard.utils = {
        /**
         * Format numbers with commas
         */
        formatNumber: function(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        /**
         * Show loading spinner
         */
        showLoading: function(element) {
            if (element) {
                element.innerHTML = '<div class="dashboard-loading"><div class="dashboard-spinner"></div></div>';
            }
        },

        /**
         * Hide loading spinner
         */
        hideLoading: function(element, content) {
            if (element) {
                element.innerHTML = content || '';
            }
        },

        /**
         * Show toast notification
         */
        showToast: function(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            document.body.appendChild(toast);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                if (toast && toast.parentNode) {
                    const bsAlert = new bootstrap.Alert(toast);
                    bsAlert.close();
                }
            }, 5000);
        }
    };

    /**
     * AJAX helpers
     */
    Dashboard.ajax = {
        /**
         * Generic AJAX request
         */
        request: function(url, options = {}) {
            const defaults = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            };
            
            const config = Object.assign(defaults, options);
            
            return fetch(url, config)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Dashboard AJAX error:', error);
                    Dashboard.utils.showToast('An error occurred. Please try again.', 'danger');
                    throw error;
                });
        },

        /**
         * GET request
         */
        get: function(url) {
            return this.request(url);
        },

        /**
         * POST request
         */
        post: function(url, data) {
            return this.request(url, {
                method: 'POST',
                body: JSON.stringify(data)
            });
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', Dashboard.init);
    } else {
        Dashboard.init();
    }

    // Export to global scope
    window.Dashboard = Dashboard;

})();
