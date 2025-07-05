// ===== HOME ENHANCED JAVASCRIPT =====

document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });

    // Initialize all components
    initCounterAnimation();
    initLiveActivityFeed();
    initNewsletterForm();
    initScrollEffects();
    initInteractiveElements();
});

// Counter Animation for Hero Stats
function initCounterAnimation() {
    const counters = document.querySelectorAll('.stat-number[data-count]');

    const animateCounter = (counter) => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };

        updateCounter();
    };

    // Intersection Observer for triggering animation
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                entry.target.classList.add('animated');
                animateCounter(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
}

// Live Activity Feed
function initLiveActivityFeed() {
    const activityFeed = document.getElementById('liveActivityFeed');
    if (!activityFeed) return;

    // Mock live activities data
    const activities = [
        {
            type: 'new_thread',
            user: 'Nguyễn Văn A',
            action: 'đã tạo chủ đề mới',
            title: 'Thiết kế bánh răng cho hộp số',
            time: '2 phút trước',
            avatar: '/images/avatars/user1.jpg'
        },
        {
            type: 'new_answer',
            user: 'Trần Thị B',
            action: 'đã trả lời câu hỏi',
            title: 'Cách tính toán độ bền vật liệu',
            time: '5 phút trước',
            avatar: '/images/avatars/user2.jpg'
        },
        {
            type: 'achievement',
            user: 'Lê Văn C',
            action: 'đã đạt được huy hiệu',
            title: 'Expert CAD Designer',
            time: '10 phút trước',
            avatar: '/images/avatars/user3.jpg'
        },
        {
            type: 'new_member',
            user: 'Phạm Thị D',
            action: 'đã tham gia cộng đồng',
            title: '',
            time: '15 phút trước',
            avatar: '/images/avatars/user4.jpg'
        }
    ];

    // Render activities
    function renderActivities() {
        const html = activities.map(activity => `
            <div class="activity-item" data-aos="fade-up">
                <div class="activity-avatar">
                    <img src="${activity.avatar}" alt="${activity.user}" onerror="this.src='/images/default-avatar.png'">
                </div>
                <div class="activity-content">
                    <p><strong>${activity.user}</strong> ${activity.action} ${activity.title ? `"${activity.title}"` : ''}</p>
                    <small class="text-muted">${activity.time}</small>
                </div>
                <div class="activity-icon">
                    <i class="fas ${getActivityIcon(activity.type)}"></i>
                </div>
            </div>
        `).join('');

        activityFeed.innerHTML = html;
    }

    function getActivityIcon(type) {
        const icons = {
            'new_thread': 'fa-comment-dots',
            'new_answer': 'fa-reply',
            'achievement': 'fa-trophy',
            'new_member': 'fa-user-plus'
        };
        return icons[type] || 'fa-bell';
    }

    // Initial render
    renderActivities();

    // Simulate real-time updates
    setInterval(() => {
        // Add new activity occasionally
        if (Math.random() > 0.7) {
            const newActivity = {
                type: 'new_thread',
                user: 'Người dùng mới',
                action: 'đã tạo chủ đề mới',
                title: 'Câu hỏi kỹ thuật mới',
                time: 'Vừa xong',
                avatar: '/images/default-avatar.png'
            };
            activities.unshift(newActivity);
            activities.pop(); // Keep only 4 items
            renderActivities();
        }
    }, 30000); // Update every 30 seconds
}

// Newsletter Form
function initNewsletterForm() {
    const form = document.getElementById('newsletterForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const email = form.querySelector('input[type="email"]').value;
        const button = form.querySelector('button');
        const originalText = button.textContent;

        // Show loading state
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        button.disabled = true;

        // Simulate API call
        setTimeout(() => {
            // Show success message
            button.innerHTML = '<i class="fas fa-check"></i> Đã đăng ký!';
            button.classList.remove('btn-primary');
            button.classList.add('btn-success');

            // Reset form
            form.reset();

            // Reset button after 3 seconds
            setTimeout(() => {
                button.textContent = originalText;
                button.disabled = false;
                button.classList.remove('btn-success');
                button.classList.add('btn-primary');
            }, 3000);

            // Show toast notification
            showToast('Cảm ơn bạn đã đăng ký nhận tin!', 'success');
        }, 2000);
    });
}

// Scroll Effects
function initScrollEffects() {
    let lastScrollTop = 0;
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Add/remove navbar background on scroll
        if (scrollTop > 100) {
            navbar?.classList.add('navbar-scrolled');
        } else {
            navbar?.classList.remove('navbar-scrolled');
        }

        // Parallax effect for hero section
        const heroSection = document.querySelector('.hero-section');
        if (heroSection && scrollTop < window.innerHeight) {
            const parallaxSpeed = 0.5;
            heroSection.style.transform = `translateY(${scrollTop * parallaxSpeed}px)`;
        }

        lastScrollTop = scrollTop;
    });
}

// Interactive Elements
function initInteractiveElements() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Interactive cards hover effects
    const cards = document.querySelectorAll('.value-card, .quick-action-card, .testimonial-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Floating elements animation
    const floatingCards = document.querySelectorAll('.floating-card');
    floatingCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.5}s`;

        // Add random movement
        setInterval(() => {
            const randomX = (Math.random() - 0.5) * 20;
            const randomY = (Math.random() - 0.5) * 20;
            card.style.transform = `translate(${randomX}px, ${randomY}px)`;
        }, 3000 + index * 1000);
    });

    // Partner logos hover effect
    const partnerLogos = document.querySelectorAll('.partner-logo');
    partnerLogos.forEach(logo => {
        logo.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.filter = 'grayscale(0%)';
        });

        logo.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.filter = 'grayscale(100%)';
        });
    });
}

// Toast Notification System
function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas ${getToastIcon(type)} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });
    bsToast.show();

    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function getToastIcon(type) {
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    return icons[type] || 'fa-info-circle';
}

// Lazy Loading for Images
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Performance Monitoring
function initPerformanceMonitoring() {
    // Monitor page load time
    window.addEventListener('load', function() {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        console.log(`Page loaded in ${loadTime}ms`);

        // Send analytics if needed
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_load_time', {
                'event_category': 'Performance',
                'event_label': 'Home Page',
                'value': loadTime
            });
        }
    });
}

// Initialize additional features
document.addEventListener('DOMContentLoaded', function() {
    initLazyLoading();
    initPerformanceMonitoring();
});

// CSS for activity feed (add to CSS file)
const activityFeedStyles = `
.activity-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.activity-item:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

.activity-avatar img {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
}

.activity-content {
    flex: 1;
}

.activity-content p {
    margin: 0;
    font-size: 0.85rem;
    line-height: 1.4;
}

.activity-icon {
    color: #667eea;
    font-size: 0.9rem;
}

.toast-container {
    z-index: 9999;
}
`;

// Inject styles
const styleSheet = document.createElement('style');
styleSheet.textContent = activityFeedStyles;
document.head.appendChild(styleSheet);

// Error handling for missing images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            // Set fallback image
            if (this.src.includes('avatar') || this.src.includes('user')) {
                this.src = '/images/default-avatar.png';
            } else if (this.src.includes('hero') || this.src.includes('engineering')) {
                this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjNjY3ZWVhIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5NZWNoYU1hcCBDb21tdW5pdHk8L3RleHQ+PC9zdmc+';
            } else if (this.src.includes('partner') || this.src.includes('company')) {
                this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjYwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9IiNmOGY5ZmEiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNmM3NTdkIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+UGFydG5lcjwvdGV4dD48L3N2Zz4=';
            } else {
                this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZTllY2VmIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlPC90ZXh0Pjwvc3ZnPg==';
            }
        });
    });
});

// Search functionality moved to unified-search.js
// This file now focuses on homepage-specific enhancements only
