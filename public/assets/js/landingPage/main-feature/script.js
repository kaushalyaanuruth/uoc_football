// Main feature interactions
document.addEventListener('DOMContentLoaded', () => {
    const featureCard = document.querySelector('.feature-card');
    
    if (featureCard) {
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -10px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Set initial state and observe
        featureCard.style.opacity = '0';
        featureCard.style.transform = 'translateY(30px)';
        featureCard.style.transition = 'all 0.6s ease-out';
        observer.observe(featureCard);

        // Click interaction
        featureCard.addEventListener('click', () => {
            if (typeof showNotification === 'function') {
                showNotification('🏆 Imperial Blaze\'25 feature clicked! This would show more tournament details.');
            }
        });
    }
});