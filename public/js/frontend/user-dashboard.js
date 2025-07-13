/**
 * User Dashboard JavaScript
 * Handles dashboard interactions and dynamic content
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize data tables
    initializeDataTables();
    
    // Initialize filters
    initializeFilters();
    
    // Initialize real-time updates
    initializeRealTimeUpdates();
    
    // Initialize charts if present
    initializeCharts();
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize data tables with sorting and pagination
 */
function initializeDataTables() {
    const tables = document.querySelectorAll('.dashboard-table table');
    
    tables.forEach(table => {
        // Add sorting functionality
        const headers = table.querySelectorAll('th[data-sortable]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                sortTable(table, this);
            });
        });
    });
}

/**
 * Sort table by column
 */
function sortTable(table, header) {
    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    const isAscending = header.classList.contains('sort-asc');
    
    // Remove existing sort classes
    header.parentNode.querySelectorAll('th').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
    });
    
    // Add new sort class
    header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
    
    // Sort rows
    rows.sort((a, b) => {
        const aValue = a.children[columnIndex].textContent.trim();
        const bValue = b.children[columnIndex].textContent.trim();
        
        // Try to parse as numbers
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return isAscending ? bNum - aNum : aNum - bNum;
        }
        
        // String comparison
        return isAscending ? 
            bValue.localeCompare(aValue) : 
            aValue.localeCompare(bValue);
    });
    
    // Reorder rows in DOM
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Initialize filters
 */
function initializeFilters() {
    const filterForms = document.querySelectorAll('.dashboard-filters form');
    
    filterForms.forEach(form => {
        const inputs = form.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                // Auto-submit form on filter change
                if (this.type !== 'text') {
                    form.submit();
                }
            });
            
            // For text inputs, submit on Enter or after delay
            if (input.type === 'text') {
                let timeout;
                input.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }
        });
    });
}

/**
 * Initialize real-time updates
 */
function initializeRealTimeUpdates() {
    // Update stats every 30 seconds
    setInterval(updateStats, 30000);
    
    // Update activity feed every 60 seconds
    setInterval(updateActivityFeed, 60000);
}

/**
 * Update dashboard stats
 */
function updateStats() {
    fetch('/user/dashboard/stats', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStatsCards(data.stats);
        }
    })
    .catch(error => {
        console.error('Error updating stats:', error);
    });
}

/**
 * Update stats cards with new data
 */
function updateStatsCards(stats) {
    Object.keys(stats).forEach(key => {
        const card = document.querySelector(`[data-stat="${key}"]`);
        if (card) {
            const valueElement = card.querySelector('.stats-value');
            const changeElement = card.querySelector('.stats-change');
            
            if (valueElement) {
                animateValue(valueElement, parseInt(valueElement.textContent), stats[key].value);
            }
            
            if (changeElement && stats[key].change !== undefined) {
                changeElement.textContent = `${stats[key].change > 0 ? '+' : ''}${stats[key].change}%`;
                changeElement.className = `stats-change ${stats[key].change >= 0 ? 'positive' : 'negative'}`;
            }
        }
    });
}

/**
 * Animate number changes
 */
function animateValue(element, start, end) {
    const duration = 1000;
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(start + (end - start) * progress);
        element.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

/**
 * Update activity feed
 */
function updateActivityFeed() {
    const activityFeed = document.querySelector('.activity-feed');
    if (!activityFeed) return;
    
    fetch('/user/dashboard/activity', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.activities) {
            updateActivityFeedContent(activityFeed, data.activities);
        }
    })
    .catch(error => {
        console.error('Error updating activity feed:', error);
    });
}

/**
 * Update activity feed content
 */
function updateActivityFeedContent(container, activities) {
    // Only add new activities, don't replace all
    const existingIds = Array.from(container.querySelectorAll('[data-activity-id]'))
        .map(el => el.getAttribute('data-activity-id'));
    
    activities.forEach(activity => {
        if (!existingIds.includes(activity.id.toString())) {
            const activityElement = createActivityElement(activity);
            container.insertBefore(activityElement, container.firstChild);
        }
    });
    
    // Remove old activities if too many
    const maxActivities = 20;
    const activityElements = container.querySelectorAll('.activity-item');
    if (activityElements.length > maxActivities) {
        for (let i = maxActivities; i < activityElements.length; i++) {
            activityElements[i].remove();
        }
    }
}

/**
 * Create activity element
 */
function createActivityElement(activity) {
    const div = document.createElement('div');
    div.className = 'activity-item';
    div.setAttribute('data-activity-id', activity.id);
    
    div.innerHTML = `
        <div class="activity-icon ${activity.type}">
            <i class="${activity.icon}"></i>
        </div>
        <div class="activity-content">
            <div class="activity-title">${activity.title}</div>
            <div class="activity-description">${activity.description}</div>
            <div class="activity-time">${activity.time}</div>
        </div>
    `;
    
    return div;
}

/**
 * Initialize charts if Chart.js is available
 */
function initializeCharts() {
    if (typeof Chart === 'undefined') return;
    
    // Initialize any charts on the page
    const chartElements = document.querySelectorAll('[data-chart]');
    
    chartElements.forEach(element => {
        const chartType = element.getAttribute('data-chart');
        const chartData = JSON.parse(element.getAttribute('data-chart-data') || '{}');
        
        initializeChart(element, chartType, chartData);
    });
}

/**
 * Initialize individual chart
 */
function initializeChart(element, type, data) {
    const ctx = element.getContext('2d');
    
    new Chart(ctx, {
        type: type,
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

/**
 * Utility function to show loading state
 */
function showLoading(container) {
    container.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
}

/**
 * Utility function to show error state
 */
function showError(container, message) {
    container.innerHTML = `
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="empty-state-title">Error</div>
            <div class="empty-state-description">${message}</div>
        </div>
    `;
}
