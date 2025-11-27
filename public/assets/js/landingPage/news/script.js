// News section interactions
document.addEventListener('DOMContentLoaded', () => {
    const newsCards = document.querySelectorAll('.news-card');
    const viewAllNewsBtn = document.querySelector('.view-all-news-btn');
    
    // View All News button click handler
    if (viewAllNewsBtn) {
        viewAllNewsBtn.addEventListener('click', (e) => {
            console.log('See All News button clicked');
            // Default behavior will follow the href link
        });
    }
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Setup animations and interactions for each news card
    newsCards.forEach((card, index) => {
        // Set initial animation state
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease-out ${index * 0.2}s`;
        observer.observe(card);

        // Hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 20px 40px rgba(179, 179, 179, 0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 5px 20px rgba(179, 179, 179, 0.1)';
        });

        // Click functionality
        card.addEventListener('click', function() {
            const newsTitle = this.querySelector('h3').textContent;
            if (typeof showNotification === 'function') {
                showNotification(`📰 "${newsTitle}" clicked! This would normally navigate to the full article.`);
            }
        });
    });
});