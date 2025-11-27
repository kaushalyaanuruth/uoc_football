// Hero section animations and interactions
document.addEventListener('DOMContentLoaded', () => {
    const heroTitle = document.querySelector('.hero h1');
    
    if (heroTitle) {
        // Add entrance animation
        heroTitle.style.opacity = '0';
        heroTitle.style.transform = 'translateY(50px)';
        
        setTimeout(() => {
            heroTitle.style.transition = 'all 1s ease-out';
            heroTitle.style.opacity = '1';
            heroTitle.style.transform = 'translateY(0)';
        }, 500);
    }

    // Parallax effect for hero background
    window.addEventListener('scroll', () => {
        const hero = document.querySelector('.hero');
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.15;
        
        if (hero) {
            hero.style.transform = `translateY(${rate}px)`;
        }
    });
});