/**
 * MechaMap Rating System
 * Modern, clean rating system for showcase ratings
 * 
 * @version 2.0.0
 * @author MechaMap Team
 */

class RatingSystem {
    constructor(containerSelector = '.rating-form-container') {
        this.container = document.querySelector(containerSelector);
        this.ratings = new Map(); // Store current ratings for each category
        
        if (this.container) {
            this.init();
        } else {
            console.warn('RatingSystem: Container not found:', containerSelector);
        }
    }

    /**
     * Initialize the rating system
     */
    init() {
        console.log('🌟 RatingSystem: Initializing...');
        this.bindEvents();
        this.initializeCurrentRatings();
        console.log('✅ RatingSystem: Initialized successfully');
    }

    /**
     * Bind event listeners using event delegation
     */
    bindEvents() {
        // Use event delegation for better performance and reliability
        this.container.addEventListener('click', this.handleStarClick.bind(this));
        this.container.addEventListener('mouseenter', this.handleStarHover.bind(this), true);
        this.container.addEventListener('mouseleave', this.handleStarLeave.bind(this), true);
        
        // Reset button
        const resetBtn = this.container.querySelector('.btn-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', this.handleReset.bind(this));
        }
    }

    /**
     * Initialize current ratings from hidden inputs
     */
    initializeCurrentRatings() {
        const hiddenInputs = this.container.querySelectorAll('input[type="hidden"]');
        hiddenInputs.forEach(input => {
            const category = input.name;
            const rating = parseInt(input.value) || 0;
            if (rating > 0) {
                this.ratings.set(category, rating);
                this.updateVisualState(category, rating, false);
            }
        });
    }

    /**
     * Handle star click events
     */
    handleStarClick(event) {
        const star = event.target.closest('.rating-star');
        if (!star) return;

        event.preventDefault();
        
        const rating = parseInt(star.dataset.rating);
        const category = star.dataset.category;
        
        if (!rating || !category) {
            console.warn('RatingSystem: Invalid star data:', { rating, category });
            return;
        }

        console.log(`⭐ Rating clicked: ${category} = ${rating}`);
        
        // Update rating
        this.ratings.set(category, rating);
        
        // Update visual state
        this.updateVisualState(category, rating, true);
        
        // Update hidden input
        this.updateHiddenInput(category, rating);
    }

    /**
     * Handle star hover events
     */
    handleStarHover(event) {
        const star = event.target.closest('.rating-star');
        if (!star) return;

        const rating = parseInt(star.dataset.rating);
        const category = star.dataset.category;
        
        if (!rating || !category) return;

        // Show hover preview
        this.updateVisualState(category, rating, false, true);
    }

    /**
     * Handle star mouse leave events
     */
    handleStarLeave(event) {
        const star = event.target.closest('.rating-star');
        if (!star) return;

        const category = star.dataset.category;
        if (!category) return;

        // Restore actual rating state
        const currentRating = this.ratings.get(category) || 0;
        this.updateVisualState(category, currentRating, false);
    }

    /**
     * Update visual state of stars
     */
    updateVisualState(category, rating, animate = false, isHover = false) {
        const categoryStars = this.container.querySelectorAll(`[data-category="${category}"].rating-star`);
        
        categoryStars.forEach((star, index) => {
            const starRating = index + 1; // Convert 0-based index to 1-based rating
            const shouldHighlight = starRating <= rating;
            
            // Remove existing classes
            star.classList.remove('active', 'hover');
            
            // Add appropriate class
            if (shouldHighlight) {
                star.classList.add(isHover ? 'hover' : 'active');
            }
            
            // Add animation if requested
            if (animate && shouldHighlight) {
                star.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    star.style.transform = '';
                }, 150);
            }
        });
    }

    /**
     * Update hidden input value
     */
    updateHiddenInput(category, rating) {
        const hiddenInput = this.container.querySelector(`input[name="${category}"]`);
        if (hiddenInput) {
            hiddenInput.value = rating;
            console.log(`💾 Updated hidden input: ${category} = ${rating}`);
        } else {
            console.warn('RatingSystem: Hidden input not found for category:', category);
        }
    }

    /**
     * Handle reset button click
     */
    handleReset(event) {
        event.preventDefault();
        console.log('🔄 Resetting all ratings...');
        
        // Clear all ratings
        this.ratings.clear();
        
        // Reset all visual states
        const allStars = this.container.querySelectorAll('.rating-star');
        allStars.forEach(star => {
            star.classList.remove('active', 'hover');
        });
        
        // Reset all hidden inputs
        const hiddenInputs = this.container.querySelectorAll('input[type="hidden"]');
        hiddenInputs.forEach(input => {
            input.value = '';
        });
        
        console.log('✅ All ratings reset');
    }

    /**
     * Get current ratings
     */
    getCurrentRatings() {
        return Object.fromEntries(this.ratings);
    }

    /**
     * Set rating programmatically
     */
    setRating(category, rating) {
        if (rating < 1 || rating > 5) {
            console.warn('RatingSystem: Invalid rating value:', rating);
            return false;
        }
        
        this.ratings.set(category, rating);
        this.updateVisualState(category, rating, true);
        this.updateHiddenInput(category, rating);
        return true;
    }

    /**
     * Destroy the rating system (cleanup)
     */
    destroy() {
        if (this.container) {
            this.container.removeEventListener('click', this.handleStarClick);
            this.container.removeEventListener('mouseenter', this.handleStarHover, true);
            this.container.removeEventListener('mouseleave', this.handleStarLeave, true);
        }
        this.ratings.clear();
        console.log('🗑️ RatingSystem destroyed');
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize rating system if container exists
    const ratingContainer = document.querySelector('.rating-form-container');
    if (ratingContainer) {
        window.showcaseRatingSystem = new RatingSystem('.rating-form-container');
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RatingSystem;
}
