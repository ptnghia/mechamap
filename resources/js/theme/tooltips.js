/**
 * Tooltip functionality
 */

/**
 * Initialize tooltips
 */
export function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    
    tooltips.forEach(tooltip => {
        const text = tooltip.getAttribute('data-tooltip');
        const position = tooltip.getAttribute('data-tooltip-position') || 'top';
        
        const tooltipElement = document.createElement('span');
        tooltipElement.classList.add('tooltip-text', `tooltip-${position}`);
        tooltipElement.textContent = text;
        
        tooltip.classList.add('tooltip');
        tooltip.appendChild(tooltipElement);
    });
}
