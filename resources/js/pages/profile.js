/**
 * Profile page functionality
 */

/**
 * Initialize profile page
 */
export function initProfilePage() {
    document.addEventListener('DOMContentLoaded', function() {
        // Handle profile tabs
        initProfileTabs();
        
        // Handle setup progress close button
        initSetupProgressClose();
    });
}

/**
 * Initialize profile tabs
 */
function initProfileTabs() {
    const tabLinks = document.querySelectorAll('.profile-tabs .nav-link');
    const tabContents = document.querySelectorAll('.tab-content > div');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            tabLinks.forEach(tab => tab.classList.remove('active'));
            
            // Add active class to current tab
            this.classList.add('active');
            
            // Hide all tab contents
            tabContents.forEach(content => content.classList.add('d-none'));
            
            // Show current tab content
            const target = this.getAttribute('href').substring(1);
            document.getElementById(target).classList.remove('d-none');
            
            // Update URL hash
            window.history.replaceState(null, null, `#${target}`);
        });
    });
    
    // Check if URL has hash and activate corresponding tab
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        const activeTab = document.querySelector(`.profile-tabs .nav-link[href="#${hash}"]`);
        
        if (activeTab) {
            activeTab.click();
        }
    }
}

/**
 * Initialize setup progress close button
 */
function initSetupProgressClose() {
    const closeButton = document.querySelector('.setup-card .btn-close');
    
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            const setupCard = document.querySelector('.setup-card');
            
            if (setupCard) {
                setupCard.classList.add('d-none');
                
                // Save preference to localStorage
                localStorage.setItem('hideSetupProgress', 'true');
            }
        });
    }
    
    // Check if setup progress should be hidden
    const hideSetupProgress = localStorage.getItem('hideSetupProgress');
    
    if (hideSetupProgress === 'true') {
        const setupCard = document.querySelector('.setup-card');
        
        if (setupCard) {
            setupCard.classList.add('d-none');
        }
    }
}

// Auto-initialize profile page
initProfilePage();
