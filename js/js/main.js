// Main JavaScript for EventHub
document.addEventListener('DOMContentLoaded', function() {
    console.log('EventHub loaded successfully!');
    
    // Add loading animation to buttons
    const buttons = document.querySelectorAll('button[type="submit"]');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="loading"></span> Processing...';
            this.disabled = true;
            
            // Reset after 2 seconds
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 2000);
        });
    });
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
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
    
    // Feature card animations
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Search functionality for events page
    if (window.location.pathname.includes('events.html')) {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Search events...';
        searchInput.className = 'form-control mb-4';
        searchInput.style.maxWidth = '300px';
        searchInput.style.margin = '0 auto';
        
        const eventsContainer = document.querySelector('.container.my-4');
        eventsContainer.insertBefore(searchInput, eventsContainer.firstChild);
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const eventCards = document.querySelectorAll('.event-card');
            
            eventCards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const description = card.querySelector('.card-text').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // Page visit counter (local storage)
    if (typeof(Storage) !== "undefined") {
        let visitCount = localStorage.getItem('visitCount') || 0;
        visitCount = parseInt(visitCount) + 1;
        localStorage.setItem('visitCount', visitCount);
        console.log(`Page visits: ${visitCount}`);
    }
});

// Utility functions
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Export functions for global use
window.EventHub = {
    showNotification: showNotification,
    isValidEmail: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
};