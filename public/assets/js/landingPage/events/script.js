// Events section interactions
document.addEventListener('DOMContentLoaded', () => {
    const eventsScroll = document.querySelector('.events-scroll');
    const eventCards = document.querySelectorAll('.event-card');
    const viewAllBtn = document.querySelector('.events-section .view-all-btn');
    let scrollInterval;

    // Auto-scroll events
    const autoScrollEvents = () => {
        if (!eventsScroll) return;
        
        scrollInterval = setInterval(() => {
            if (eventsScroll.scrollLeft >= eventsScroll.scrollWidth - eventsScroll.clientWidth) {
                eventsScroll.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                eventsScroll.scrollBy({ left: 270, behavior: 'smooth' });
            }
        }, 3000);
    };

    // Initialize auto-scroll
    autoScrollEvents();

    // Pause auto-scroll on hover
    if (eventsScroll) {
        eventsScroll.addEventListener('mouseenter', () => {
            clearInterval(scrollInterval);
        });

        eventsScroll.addEventListener('mouseleave', () => {
            autoScrollEvents();
        });
    }

    // Event card interactions
    eventCards.forEach(card => {
        // Hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 8px 30px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
        });

        // Click functionality
        card.addEventListener('click', function() {
            const eventTitle = this.querySelector('h4').textContent;
            const eventDate = this.querySelector('.event-date').textContent;
            if (typeof showNotification === 'function') {
                showNotification(`📅 Event "${eventTitle}" on ${eventDate} clicked! This would show event details.`);
            }
        });
    });

    // View All button
    if (viewAllBtn) {
        viewAllBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (typeof showNotification === 'function') {
                showNotification('📋 View All Events clicked! This would show the complete events calendar.');
            }
        });
    }
});