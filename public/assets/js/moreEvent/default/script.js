// Event Modal Functionality
document.addEventListener('DOMContentLoaded', () => {
    const eventModal = document.getElementById('eventModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalClose = document.getElementById('modalClose');

    // Get all read more buttons
    const readMoreButtons = document.querySelectorAll('.read-more-btn');

    // Open modal when read more button is clicked
    readMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.dataset.id;
            const title = this.dataset.title;
            const date = this.dataset.date;
            const time = this.dataset.time;
            const location = this.dataset.location;
            const image = this.dataset.image;
            const description = this.dataset.description;

            // Populate modal with event data
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalDate').textContent = date + (time ? ' at ' + time : '');
            document.getElementById('modalLocation').textContent = location ? '📍 ' + location : '';
            
            // Handle image
            const modalImageDiv = document.getElementById('modalImage');
            if (image) {
                modalImageDiv.innerHTML = `<img src="${ROOT}/uploads/event_images/${image}" alt="${title}">`;
            } else {
                // Show emoji placeholder
                const eventEmojis = {
                    'match': '⚽',
                    'tournament': '🏆',
                    'training': '🏃',
                    'meeting': '🎯',
                    'social': '🎉',
                    'other': '📅'
                };
                modalImageDiv.innerHTML = `<div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 120px;">${eventEmojis['match'] || '📅'}</div>`;
            }

            document.getElementById('modalText').innerHTML = description.replace(/\n/g, '<br>');

            // Show modal
            eventModal.classList.add('open');
        });
    });

    // Close modal when close button or overlay is clicked
    modalClose.addEventListener('click', () => {
        eventModal.classList.remove('open');
    });

    modalOverlay.addEventListener('click', () => {
        eventModal.classList.remove('open');
    });

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && eventModal.classList.contains('open')) {
            eventModal.classList.remove('open');
        }
    });
});

// Shared functionality
// Smooth scrolling for navigation
function scrollToHash(hash, smooth = true) {
    if (!hash) return;
    const target = document.querySelector(hash);
    if (!target) return;
    // account for fixed header height
    const header = document.querySelector('.header');
    const headerHeight = header ? header.getBoundingClientRect().height : 0;
    const rect = target.getBoundingClientRect();
    const targetY = window.scrollY + rect.top - headerHeight - 10; // small extra gap
    window.scrollTo({
        top: targetY,
        behavior: smooth ? 'smooth' : 'auto'
    });
}

document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href^="#"]');
    if (!a) return;
    const href = a.getAttribute('href');
    if (!href || href === '#') return;
    e.preventDefault();
    // update history (keeps back button behavior)
    history.pushState(null, '', href);
    scrollToHash(href, true);
});

// On load, if URL contains hash, scroll with offset
window.addEventListener('load', () => {
    if (location.hash) {
        // small timeout to allow layout & fixed header measurements
        setTimeout(() => scrollToHash(location.hash, false), 50);
    }
});

// Custom notification system
function showNotification(message) {
    const existingNotification = document.querySelector('.custom-notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = 'custom-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: linear-gradient(135deg, #663399, #9966cc);
        color: white;
        padding: 1rem 2rem;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        z-index: 9999;
        font-weight: 500;
        max-width: 300px;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Loading animation
window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease-in';
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

// Global keyboard navigation for accessibility
document.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
        const focusedElement = document.activeElement;
        if (focusedElement.classList.contains('news-card') || 
            focusedElement.classList.contains('event-card') || 
            focusedElement.classList.contains('team-member')) {
            focusedElement.style.outline = '3px solid #663399';
        }
    }
});

document.addEventListener('click', () => {
    document.querySelectorAll('.news-card, .event-card, .team-member').forEach(el => {
        el.style.outline = 'none';
    });
});