// Values section interactions
document.addEventListener('DOMContentLoaded', () => {
    const valueItems = document.querySelectorAll('.value-item');
    
    if (valueItems.length > 0) {
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.2,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 200); // Stagger animation
                }
            });
        }, observerOptions);

        // Set initial state and observe each value item
        valueItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'all 0.6s ease-out';
            observer.observe(item);
        });

        // Add hover effects to circles
        const circles = document.querySelectorAll('.value-circle');
        circles.forEach(circle => {
            circle.addEventListener('mouseenter', () => {
                circle.style.transform = 'scale(1.1)';
                circle.style.transition = 'transform 0.3s ease';
            });
            
            circle.addEventListener('mouseleave', () => {
                circle.style.transform = 'scale(1)';
            });
        });
    }
});