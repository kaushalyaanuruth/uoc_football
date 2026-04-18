// Landing Page Animation & Parallax Controller

(function() {
    'use strict';

    // Parallax Effect on Scroll
    function setupParallax() {
        const heroImg = document.querySelector('.hero-img');
        
        if (!heroImg) return;
        
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            // Slower movement for parallax effect (50% of scroll speed)
            const parallaxValue = scrollY * 0.5;
            heroImg.style.transform = `translateY(${parallaxValue}px)`;
        });
    }

    // Intersection Observer for Scroll-triggered Animations
    function setupIntersectionObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Add visible class to trigger animation
                    entry.target.classList.add('visible');
                    
                    // Optional: Stop observing after animation triggers
                    // observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all animatable sections
        document.querySelectorAll('.section-animate').forEach(el => {
            observer.observe(el);
        });

        // Observe card animations
        document.querySelectorAll('.card-animate').forEach(el => {
            observer.observe(el);
        });
    }

    // Add animation classes to sections on page load
    function initializeAnimations() {
        // Add section-animate class to main sections
        const sections = [
            '.news',
            '.events',
            '.team',
            '.gallery'
        ];

        sections.forEach(selector => {
            const element = document.querySelector(selector);
            if (element) {
                element.classList.add('section-animate');
            }
        });

        // Add card-animate class to news cards
        document.querySelectorAll('.news-card').forEach(card => {
            if (!card.classList.contains('card-animate')) {
                card.classList.add('card-animate');
            }
        });

        // Add card-animate class to event cards
        document.querySelectorAll('.event-card').forEach(card => {
            if (!card.classList.contains('card-animate')) {
                card.classList.add('card-animate');
            }
        });

        // Add animation classes to team members
        document.querySelectorAll('.team-member').forEach(member => {
            // Already has animation, but ensure visibility on init
            member.style.animationPlayState = 'running';
        });

        // Add animation classes to gallery items
        document.querySelectorAll('.gallery-item').forEach(item => {
            item.style.animationPlayState = 'running';
        });
    }

    // Smooth Scroll for Anchor Links
    function setupSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Skip if href is just "#"
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (!target) return;
                
                e.preventDefault();
                
                // Smooth scroll with offset for fixed header (80px)
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top + window.scrollY;
                const offsetPosition = elementPosition - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            });
        });
    }

    // Highlight Active Navigation Link on Scroll
    function setupActiveNavHighlight() {
        const sections = document.querySelectorAll('[id]');
        const navLinks = document.querySelectorAll('.nav-menu a[href^="#"]');

        window.addEventListener('scroll', () => {
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                if (scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    }

    // Fade in hero content on load
    function fadeInHeroContent() {
        const heroContent = document.querySelector('.hero-content');
        if (heroContent) {
            heroContent.style.animation = 'fade-in 1s ease';
        }
    }

    // Add fade-in keyframe
    function injectKeyframes() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fade-in {
                from {
                    opacity: 0;
                }
                to {
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Initialize all animations when DOM is ready
    function init() {
        injectKeyframes();
        fadeInHeroContent();
        initializeAnimations();
        setupIntersectionObserver();
        setupParallax();
        setupSmoothScroll();
        setupActiveNavHighlight();
        setupMobileMenu();
        setupStatCounters();
        setupEventFiltering();
        setupCountdownTimers();
        setupGalleryLightbox();
    }

    // Gallery Lightbox
    function setupGalleryLightbox() {
        const lightboxModal = document.getElementById('lightboxModal');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxClose = document.getElementById('lightboxClose');
        const lightboxPrev = document.getElementById('lightboxPrev');
        const lightboxNext = document.getElementById('lightboxNext');
        const galleryImages = Array.from(document.querySelectorAll('[data-lightbox]'));
        
        let currentIndex = 0;

        function openLightbox(index) {
            currentIndex = index;
            lightboxImage.src = galleryImages[index].src;
            lightboxModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightboxModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function showNext() {
            currentIndex = (currentIndex + 1) % galleryImages.length;
            lightboxImage.src = galleryImages[currentIndex].src;
        }

        function showPrev() {
            currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
            lightboxImage.src = galleryImages[currentIndex].src;
        }

        galleryImages.forEach((img, index) => {
            img.addEventListener('click', () => openLightbox(index));
            img.style.cursor = 'pointer';
        });

        lightboxClose.addEventListener('click', closeLightbox);
        lightboxPrev.addEventListener('click', showPrev);
        lightboxNext.addEventListener('click', showNext);

        lightboxModal.addEventListener('click', (e) => {
            if (e.target === lightboxModal) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (!lightboxModal.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') showNext();
            if (e.key === 'ArrowLeft') showPrev();
        });
    }

    // Event Filtering
    function setupEventFiltering() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const eventCards = document.querySelectorAll('.event-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Update active button
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.dataset.filter;

                // Filter cards
                eventCards.forEach(card => {
                    if (filter === 'all' || card.dataset.type === filter) {
                        card.classList.remove('hidden');
                        setTimeout(() => {
                            card.style.display = '';
                        }, 0);
                    } else {
                        card.classList.add('hidden');
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    // Countdown Timer
    function setupCountdownTimers() {
        const countdownElements = document.querySelectorAll('.event-countdown');

        function updateCountdown(element) {
            const timestamp = parseInt(element.dataset.timestamp);
            const now = new Date().getTime();
            const distance = timestamp - now;

            if (distance < 0) {
                element.innerHTML = '<span class="countdown-label">Event Started</span>';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            element.querySelectorAll('[data-unit="days"]').forEach(el => el.textContent = days);
            element.querySelectorAll('[data-unit="hours"]').forEach(el => el.textContent = hours);
            element.querySelectorAll('[data-unit="minutes"]').forEach(el => el.textContent = minutes);
        }

        countdownElements.forEach(el => {
            updateCountdown(el);
            setInterval(() => updateCountdown(el), 60000);
        });
    }

    // Animate counters on scroll
    function setupStatCounters() {
        const observerOptions = {
            threshold: 0.3,
            rootMargin: '-50px'
        };

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.dataset.counted) {
                    const target = parseInt(entry.target.dataset.target);
                    animateCounter(entry.target, target);
                    entry.target.dataset.counted = 'true';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stat-number').forEach(el => {
            observer.observe(el);
        });
    }

    // Animate individual counter
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 30;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 30);
    }

    // Mobile Menu Toggle
    function setupMobileMenu() {
        const hamburger = document.getElementById('hamburgerMenu');
        const drawer = document.getElementById('mobileDrawer');
        
        if (!hamburger || !drawer) return;
        
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            drawer.classList.toggle('active');
        });
        
        // Close drawer when a link is clicked
        drawer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                drawer.classList.remove('active');
            });
        });
        
        // Close drawer when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !drawer.contains(e.target)) {
                hamburger.classList.remove('active');
                drawer.classList.remove('active');
            }
        });
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
