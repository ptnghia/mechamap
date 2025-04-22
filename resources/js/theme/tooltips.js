/**
 * Tooltips functionality
 */

/**
 * Initialize tooltips
 */
export function initTooltips() {
    const tooltips = document.querySelectorAll('.tooltip');
    
    tooltips.forEach(tooltip => {
        const tooltipText = tooltip.querySelector('.tooltip-text');
        
        if (tooltipText) {
            // Position tooltips
            tooltip.addEventListener('mouseenter', () => {
                const rect = tooltipText.getBoundingClientRect();
                const tooltipRect = tooltip.getBoundingClientRect();
                
                // Default position is bottom
                let position = tooltip.getAttribute('data-tooltip-position') || 'bottom';
                
                // Reset positions
                tooltipText.style.top = null;
                tooltipText.style.right = null;
                tooltipText.style.bottom = null;
                tooltipText.style.left = null;
                
                // Position based on attribute
                switch (position) {
                    case 'top':
                        tooltipText.style.bottom = '100%';
                        tooltipText.style.left = '50%';
                        tooltipText.style.transform = 'translateX(-50%) translateY(-8px)';
                        break;
                    case 'right':
                        tooltipText.style.left = '100%';
                        tooltipText.style.top = '50%';
                        tooltipText.style.transform = 'translateY(-50%) translateX(8px)';
                        break;
                    case 'left':
                        tooltipText.style.right = '100%';
                        tooltipText.style.top = '50%';
                        tooltipText.style.transform = 'translateY(-50%) translateX(-8px)';
                        break;
                    default: // bottom
                        tooltipText.style.top = '100%';
                        tooltipText.style.left = '50%';
                        tooltipText.style.transform = 'translateX(-50%) translateY(8px)';
                        break;
                }
            });
        }
    });
}
