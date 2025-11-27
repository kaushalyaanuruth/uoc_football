// Team section interactions
document.addEventListener('DOMContentLoaded', () => {
    const teamMembers = document.querySelectorAll('.team-member');
    const exploreBtn = document.querySelector('.team-section .view-all-btn');
    
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

    // Setup animations and interactions for each team member
    teamMembers.forEach((member, index) => {
        // Set initial animation state
        member.style.opacity = '0';
        member.style.transform = 'translateY(30px)';
        member.style.transition = `all 0.6s ease-out ${index * 0.2}s`;
        observer.observe(member);

        // Click interaction
        member.addEventListener('click', function() {
            const name = this.querySelector('.team-name').textContent;
            const role = this.querySelector('.team-role').textContent;
            if (typeof showNotification === 'function') {
                showNotification(`👤 ${name} (${role}) profile clicked! This would show detailed information.`);
            }
        });
    });

    // Explore full team button
    if (exploreBtn) {
        exploreBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (typeof showNotification === 'function') {
                showNotification('👥 Explore Full Team clicked! This would show the complete team roster.');
            }
        });
    }
});