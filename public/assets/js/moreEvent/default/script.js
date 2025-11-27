// Component Loader Function
function loadComponent(elementId, componentPath) {
    const loadHTML = (filePath) => {
        return fetch(filePath)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                return response.text();
            });
    };

    const loadCSS = (filePath) => {
        return new Promise((resolve, reject) => {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = filePath;
            link.onload = () => resolve();
            link.onerror = () => reject(new Error(`Failed to load CSS: ${filePath}`));
            document.head.appendChild(link);
        });
    };

    const loadJS = (filePath) => {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = filePath;
            script.onload = () => resolve();
            script.onerror = () => reject(new Error(`Failed to load JS: ${filePath}`));
            document.body.appendChild(script);
        });
    };

    // Load component files
    Promise.all([
        loadHTML(`${componentPath}/index.html`),
        loadCSS(`${componentPath}/style.css`),
        loadJS(`${componentPath}/script.js`)
    ]).then(([html]) => {
        document.getElementById(elementId).innerHTML = html;
    }).catch(err => {
        console.error(`Error loading component ${elementId}:`, err);
    });
}

// Load all components when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    loadComponent('header', 'header');
    loadComponent('containers', 'containers');
    loadComponent('footer', 'footer');
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