// Header scroll effect
window.addEventListener('scroll', () => {
    const header = document.querySelector('.header');
    if (header) {
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
            header.style.backdropFilter = 'blur(10px)';
        } else {
            header.style.background = 'white';
            header.style.backdropFilter = 'none';
        }
    }
});

// Team portal click handler
document.addEventListener('DOMContentLoaded', () => {
    const teamPortal = document.querySelector('.team-portal');
    if (teamPortal) {
        teamPortal.addEventListener('click', (e) => {
            e.preventDefault();
            if (typeof showNotification === 'function') {
                showNotification('🏈 Team Portal clicked! This would navigate to the team management system.');
            }
        });
    }
});


// Mobile Menu Toggle
const menuBtn = document.getElementById('menuBtn');
const navLinks = document.getElementById('navLinks');

menuBtn.addEventListener('click', () => {
    navLinks.classList.toggle('active');
});

// Filter Functionality
const filterButtons = document.querySelectorAll('.filter-btn');
const galleryItems = document.querySelectorAll('.gallery-item');

filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons
        filterButtons.forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        button.classList.add('active');

        const filterValue = button.getAttribute('data-filter');

        galleryItems.forEach(item => {
            if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
});

// Lightbox Functionality
const lightbox = document.getElementById('lightbox');
const lightboxImage = document.getElementById('lightboxImage');
const lightboxTitle = document.getElementById('lightboxTitle');
const lightboxDesc = document.getElementById('lightboxDesc');
const lightboxClose = document.getElementById('lightboxClose');
const lightboxPrev = document.getElementById('lightboxPrev');
const lightboxNext = document.getElementById('lightboxNext');

let currentImageIndex = 0;
let visibleImages = [];

// Update visible images array
function updateVisibleImages() {
    visibleImages = Array.from(galleryItems).filter(item => !item.classList.contains('hidden'));
}

// Open lightbox on image click
galleryItems.forEach((item, index) => {
    item.addEventListener('click', () => {
        updateVisibleImages();
        currentImageIndex = visibleImages.indexOf(item);
        openLightbox(item);
    });
});

function openLightbox(item) {
    const img = item.querySelector('img');
    const title = item.querySelector('.overlay-content h3').textContent;
    const desc = item.querySelector('.overlay-content p').textContent;

    lightboxImage.src = img.src;
    lightboxTitle.textContent = title;
    lightboxDesc.textContent = desc;
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    lightbox.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close lightbox
lightboxClose.addEventListener('click', closeLightbox);

// Close on background click
lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
        closeLightbox();
    }
});

// Close with ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && lightbox.classList.contains('active')) {
        closeLightbox();
    }
});

// Previous image
lightboxPrev.addEventListener('click', () => {
    currentImageIndex = (currentImageIndex - 1 + visibleImages.length) % visibleImages.length;
    showImage(currentImageIndex);
});

// Next image
lightboxNext.addEventListener('click', () => {
    currentImageIndex = (currentImageIndex + 1) % visibleImages.length;
    showImage(currentImageIndex);
});

function showImage(index) {
    const item = visibleImages[index];
    const img = item.querySelector('img');
    const title = item.querySelector('.overlay-content h3').textContent;
    const desc = item.querySelector('.overlay-content p').textContent;

    lightboxImage.style.opacity = '0';
    
    setTimeout(() => {
        lightboxImage.src = img.src;
        lightboxTitle.textContent = title;
        lightboxDesc.textContent = desc;
        lightboxImage.style.opacity = '1';
    }, 200);
}

// Keyboard navigation for lightbox
document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('active')) return;

    if (e.key === 'ArrowLeft') {
        lightboxPrev.click();
    } else if (e.key === 'ArrowRight') {
        lightboxNext.click();
    }
});

// Load More Functionality
const loadMoreBtn = document.getElementById('loadMoreBtn');
let itemsToShow = 9;

// Hide items initially
function initGallery() {
    galleryItems.forEach((item, index) => {
        if (index >= itemsToShow) {
            item.style.display = 'none';
        }
    });
}

loadMoreBtn.addEventListener('click', () => {
    const hiddenItems = Array.from(galleryItems).filter(item => 
        item.style.display === 'none' && !item.classList.contains('hidden')
    );

    // Show next 6 items
    for (let i = 0; i < Math.min(6, hiddenItems.length); i++) {
        hiddenItems[i].style.display = 'block';
        hiddenItems[i].style.animation = 'fadeIn 0.5s ease';
    }

    // Hide button if no more items
    const remainingHidden = Array.from(galleryItems).filter(item => 
        item.style.display === 'none'
    );

    if (remainingHidden.length === 0) {
        loadMoreBtn.style.display = 'none';
    }
});

// Fade in animation keyframes (add to CSS if needed)
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

// Smooth scroll to top when clicking logo
document.querySelector('.logo').addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Intersection Observer for fade-in animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '0';
            entry.target.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                entry.target.style.transition = 'all 0.6s ease';
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, 100);
            
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe all gallery items for animation on scroll
galleryItems.forEach(item => {
    observer.observe(item);
});

// Initialize gallery with limited items
// initGallery();

// Add loading effect to images
document.querySelectorAll('.gallery-image img').forEach(img => {
    img.addEventListener('load', function() {
        this.style.opacity = '1';
    });
    img.style.opacity = '0';
    img.style.transition = 'opacity 0.5s';
});

// Parallax effect for hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector('.hero');
    if (hero) {
        hero.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});

// Add active state to current page in navigation
const currentPage = window.location.pathname.split('/').pop();
document.querySelectorAll('.nav-links a').forEach(link => {
    if (link.getAttribute('href') === currentPage) {
        link.classList.add('active');
    }
});

// Preload next/prev images in lightbox for smooth navigation
function preloadImage(index) {
    if (visibleImages[index]) {
        const img = new Image();
        const item = visibleImages[index];
        img.src = item.querySelector('img').src;
    }
}

// Preload adjacent images when lightbox opens
lightbox.addEventListener('transitionend', () => {
    if (lightbox.classList.contains('active')) {
        preloadImage(currentImageIndex + 1);
        preloadImage(currentImageIndex - 1);
    }
});

// Touch swipe support for mobile lightbox navigation
let touchStartX = 0;
let touchEndX = 0;

lightbox.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
});

lightbox.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
});

function handleSwipe() {
    const swipeThreshold = 50;
    
    if (touchEndX < touchStartX - swipeThreshold) {
        // Swipe left - next image
        lightboxNext.click();
    }
    
    if (touchEndX > touchStartX + swipeThreshold) {
        // Swipe right - previous image
        lightboxPrev.click();
    }
}

// Update page title dynamically based on filter
filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        const category = button.getAttribute('data-filter');
        const categoryName = category === 'all' ? 'All Photos' : category.charAt(0).toUpperCase() + category.slice(1);
        document.title = `${categoryName} - Gallery - UOC Football`;
    });
});

// Console log for debugging
console.log('Gallery initialized with', galleryItems.length, 'items');

// Initialize visible images array on load
updateVisibleImages();


// Footer interactions
document.addEventListener('DOMContentLoaded', () => {
    const socialLinks = document.querySelectorAll('.social-link');
    const sponsorLogos = document.querySelectorAll('.footer-sponsor');
    const footerLogos = document.querySelectorAll('.footer-logo');

    // Social links interactions
    socialLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const platform = link.querySelector('img').alt;
            if (typeof showNotification === 'function') {
                showNotification(`📱 ${platform} clicked! This would open our ${platform} page.`);
            }
        });
    });

    // Sponsor logo interactions
    sponsorLogos.forEach(logo => {
        logo.addEventListener('click', () => {
            const sponsor = logo.alt;
            if (typeof showNotification === 'function') {
                showNotification(`🤝 Thank you ${sponsor} for supporting UOC Football!`);
            }
        });
    });

    // Footer logo interactions
    footerLogos.forEach(logo => {
        logo.addEventListener('click', () => {
            const logoName = logo.alt;
            if (typeof showNotification === 'function') {
                showNotification(`🏫 ${logoName} clicked! This would navigate to the university homepage.`);
            }
        });
    });
});