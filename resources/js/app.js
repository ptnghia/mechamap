/**
 * MechaMap Main Application JavaScript
 * This file contains basic JavaScript functionality for the main application
 * No build process required - uses vanilla JavaScript and CDN libraries
 */

// Basic JavaScript for main application
document.addEventListener('DOMContentLoaded', function() {
    console.log('MechaMap main application loaded');

    // Initialize any main app functionality here
    initializeMainApp();
});

function initializeMainApp() {
    // Add any main application initialization code here
    console.log('Main application initialized');

    // Example: Initialize tooltips if Bootstrap is loaded
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}
