/**
 * Showcase Rating System JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeRatingSystem();
});

function initializeRatingSystem() {
    const ratingForm = document.getElementById('rating-form');
    if (!ratingForm) return;

    const showcaseId = ratingForm.dataset.showcaseId;
    const ratingStars = document.querySelectorAll('.rating-star');
    const deleteButton = document.getElementById('delete-rating');

    // Initialize existing ratings
    initializeExistingRatings();

    // Handle star clicks
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const category = this.dataset.category;
            const rating = parseInt(this.dataset.rating);
            setRating(category, rating);
        });

        star.addEventListener('mouseenter', function() {
            const category = this.dataset.category;
            const rating = parseInt(this.dataset.rating);
            highlightStars(category, rating);
        });
    });

    // Handle mouse leave to reset highlights
    document.querySelectorAll('.rating-input').forEach(container => {
        container.addEventListener('mouseleave', function() {
            const category = this.dataset.category;
            const currentRating = parseInt(document.getElementById(category).value) || 0;
            highlightStars(category, currentRating);
        });
    });

    // Handle form submission
    ratingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        submitRating(showcaseId);
    });

    // Handle delete rating
    if (deleteButton) {
        deleteButton.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn xóa đánh giá này?')) {
                deleteRating(showcaseId);
            }
        });
    }
}

function initializeExistingRatings() {
    const categories = ['technical_quality', 'innovation', 'usefulness', 'documentation'];
    
    categories.forEach(category => {
        const input = document.getElementById(category);
        if (input && input.value) {
            const rating = parseInt(input.value);
            highlightStars(category, rating);
        }
    });
}

function setRating(category, rating) {
    // Update hidden input
    document.getElementById(category).value = rating;
    
    // Update visual stars
    highlightStars(category, rating);
}

function highlightStars(category, rating) {
    const stars = document.querySelectorAll(`[data-category="${category}"] .rating-star`);
    
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

function submitRating(showcaseId) {
    const form = document.getElementById('rating-form');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Validate ratings
    const categories = ['technical_quality', 'innovation', 'usefulness', 'documentation'];
    const ratings = {};
    let isValid = true;
    
    categories.forEach(category => {
        const value = parseInt(document.getElementById(category).value);
        if (!value || value < 1 || value > 5) {
            isValid = false;
            showError(`Vui lòng đánh giá ${getCategoryName(category)}`);
            return;
        }
        ratings[category] = value;
    });
    
    if (!isValid) return;
    
    // Disable submit button
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('technical_quality', ratings.technical_quality);
    formData.append('innovation', ratings.innovation);
    formData.append('usefulness', ratings.usefulness);
    formData.append('documentation', ratings.documentation);
    formData.append('review', document.getElementById('review').value);
    
    // Submit rating
    fetch(`/showcases/${showcaseId}/ratings`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            updateRatingSummary(data);
            
            // Update form button text
            submitButton.innerHTML = '<i class="fas fa-star"></i> Cập nhật đánh giá';
            
            // Show delete button if not exists
            if (!document.getElementById('delete-rating')) {
                addDeleteButton();
            }
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

function deleteRating(showcaseId) {
    const deleteButton = document.getElementById('delete-rating');
    const originalText = deleteButton.innerHTML;
    
    deleteButton.disabled = true;
    deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xóa...';
    
    fetch(`/showcases/${showcaseId}/ratings`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            updateRatingSummary(data);
            resetRatingForm();
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Có lỗi xảy ra khi xóa đánh giá.');
    })
    .finally(() => {
        deleteButton.disabled = false;
        deleteButton.innerHTML = originalText;
    });
}

function updateRatingSummary(data) {
    // Update overall rating
    const ratingNumber = document.querySelector('.rating-number');
    if (ratingNumber) {
        ratingNumber.textContent = parseFloat(data.average_rating).toFixed(1);
    }
    
    // Update rating count
    const ratingCount = document.querySelector('.rating-count');
    if (ratingCount) {
        ratingCount.textContent = `${data.ratings_count} đánh giá`;
    }
    
    // Update overall stars
    updateStarsDisplay('.rating-stars', data.average_rating);
    
    // Update category ratings
    if (data.category_averages) {
        Object.keys(data.category_averages).forEach(category => {
            const categoryElement = document.querySelector(`[data-category-breakdown="${category}"]`);
            if (categoryElement) {
                const stars = categoryElement.querySelector('.stars-small');
                const value = categoryElement.querySelector('.rating-value');
                
                if (stars) updateStarsDisplay(stars, data.category_averages[category]);
                if (value) value.textContent = parseFloat(data.category_averages[category]).toFixed(1);
            }
        });
    }
}

function updateStarsDisplay(container, rating) {
    const stars = typeof container === 'string' 
        ? document.querySelector(container).querySelectorAll('.fa-star')
        : container.querySelectorAll('.fa-star');
    
    stars.forEach((star, index) => {
        if (index < Math.round(rating)) {
            star.classList.remove('text-muted');
            star.classList.add('text-warning');
        } else {
            star.classList.remove('text-warning');
            star.classList.add('text-muted');
        }
    });
}

function resetRatingForm() {
    // Clear all ratings
    const categories = ['technical_quality', 'innovation', 'usefulness', 'documentation'];
    categories.forEach(category => {
        document.getElementById(category).value = '';
        highlightStars(category, 0);
    });
    
    // Clear review
    document.getElementById('review').value = '';
    
    // Update button text
    const submitButton = document.querySelector('#rating-form button[type="submit"]');
    submitButton.innerHTML = '<i class="fas fa-star"></i> Gửi đánh giá';
    
    // Remove delete button
    const deleteButton = document.getElementById('delete-rating');
    if (deleteButton) {
        deleteButton.remove();
    }
}

function addDeleteButton() {
    const submitButton = document.querySelector('#rating-form button[type="submit"]');
    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.className = 'btn btn-outline-danger';
    deleteButton.id = 'delete-rating';
    deleteButton.innerHTML = '<i class="fas fa-trash"></i> Xóa đánh giá';
    
    deleteButton.addEventListener('click', function() {
        if (confirm('Bạn có chắc muốn xóa đánh giá này?')) {
            const showcaseId = document.getElementById('rating-form').dataset.showcaseId;
            deleteRating(showcaseId);
        }
    });
    
    submitButton.parentNode.appendChild(deleteButton);
}

function getCategoryName(category) {
    const names = {
        'technical_quality': 'Chất lượng kỹ thuật',
        'innovation': 'Tính sáng tạo',
        'usefulness': 'Tính hữu ích',
        'documentation': 'Chất lượng tài liệu'
    };
    return names[category] || category;
}

function showSuccess(message) {
    // You can integrate with your existing notification system
    alert(message); // Replace with your notification system
}

function showError(message) {
    // You can integrate with your existing notification system
    alert(message); // Replace with your notification system
}
