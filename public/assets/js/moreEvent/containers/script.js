
        // Mobile menu toggle
        function toggleMenu() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('active');
        }

        // Session click handler
        function openSession(sessionId) {
            console.log('Opening session:', sessionId);
            // Add session opening logic here
            alert(`Opening training session ${sessionId}`);
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navMenu = document.getElementById('navMenu');
            const hamburger = document.querySelector('.hamburger');
            
            if (!navMenu.contains(event.target) && !hamburger.contains(event.target)) {
                navMenu.classList.remove('active');
            }
        });

        // Add smooth scrolling for back button
        document.querySelector('.back-button').addEventListener('click', function(e) {
            e.preventDefault();
            window.history.back();
        });

        // Add hover effects for session cards
        document.querySelectorAll('.session-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(-5px)';
            });
        });

        // Responsive handling
        function handleResize() {
            if (window.innerWidth > 768) {
                document.getElementById('navMenu').classList.remove('active');
            }
        }

        window.addEventListener('resize', handleResize);
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('UOC Football Training Page Loaded');
            
            // Add loading animation
            document.querySelectorAll('.session-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    