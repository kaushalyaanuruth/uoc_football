// Notices Page JavaScript

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeSearch();
    initializeNoticeCards();
    initializeButtons();
});

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filterNotices(searchTerm);
        });
    }
}

// Filter notices based on search term
function filterNotices(searchTerm) {
    const noticeCards = document.querySelectorAll('.notice-card');
    
    noticeCards.forEach(card => {
        const title = card.querySelector('.notice-title').textContent.toLowerCase();
        const description = card.querySelector('.notice-description').textContent.toLowerCase();
        const author = card.querySelector('.meta-item:first-child span').textContent.toLowerCase();
        const date = card.querySelector('.meta-item:last-child span').textContent.toLowerCase();
        
        const matchesSearch = title.includes(searchTerm) || 
                             description.includes(searchTerm) || 
                             author.includes(searchTerm) || 
                             date.includes(searchTerm);
        
        if (matchesSearch) {
            card.style.display = 'block';
            // Re-trigger animation
            card.style.animation = 'none';
            setTimeout(() => {
                card.style.animation = 'fadeInUp 0.4s ease-out';
            }, 10);
        } else {
            card.style.display = 'none';
        }
    });
}

// Notice card interactions
function initializeNoticeCards() {
    const noticeCards = document.querySelectorAll('.notice-card');
    
    noticeCards.forEach(card => {
        card.addEventListener('click', function() {
            const title = this.querySelector('.notice-title').textContent;
            const description = this.querySelector('.notice-description').textContent;
            const author = this.querySelector('.meta-item:first-child span').textContent;
            const date = this.querySelector('.meta-item:last-child span').textContent;
            
            // You could open a modal here with full notice details
            console.log('Notice clicked:', { title, description, author, date });
            
            // Example: Show alert (replace with modal in production)
            showNoticeDetails({ title, description, author, date });
        });
    });
}

// Show notice details (can be replaced with a modal)
function showNoticeDetails(notice) {
    // This is a simple alert example
    // In production, you'd want to show a modal
    alert(`
Notice Details:

Author: ${notice.author}
Date: ${notice.date}

${notice.description}
    `);
}

// Initialize buttons
function initializeButtons() {
    const addNoticeBtn = document.querySelector('.add-notice-btn');
    const filterBtn = document.querySelector('.filter-btn');
    
    if (addNoticeBtn) {
        addNoticeBtn.addEventListener('click', function() {
            console.log('Add notice button clicked');
            // Open modal or navigate to add notice page
            alert('Add Notice functionality - This would open a form to create a new notice');
        });
    }
    
    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            console.log('Filter button clicked');
            // Toggle filter panel
            toggleFilterPanel();
        });
    }
}

// Toggle filter panel
function toggleFilterPanel() {
    // This would show/hide a filter options panel
    // For now, just show an alert
    alert('Filter options:\n- By Date\n- By Author\n- By Status (New/Old)');
    
    // In production, you'd create a filter panel like this:
    /*
    const filterPanel = document.createElement('div');
    filterPanel.className = 'filter-panel';
    filterPanel.innerHTML = `
        <h3>Filter Options</h3>
        <div class="filter-option">
            <label>Date Range</label>
            <input type="date">
        </div>
        <div class="filter-option">
            <label>Author</label>
            <select>
                <option>All</option>
                <option>Coach Mathews</option>
                <option>Coach Williams</option>
            </select>
        </div>
    `;
    */
}

// Mark notice as read
function markAsRead(noticeElement) {
    const badge = noticeElement.querySelector('.new-badge');
    if (badge) {
        badge.style.opacity = '0';
        setTimeout(() => {
            badge.remove();
        }, 300);
    }
}

// Add new notice (for future implementation)
function addNotice(noticeData) {
    const noticesGrid = document.querySelector('.notices-grid');
    
    const noticeCard = document.createElement('div');
    noticeCard.className = 'notice-card';
    noticeCard.innerHTML = `
        <div class="notice-header">
            <h3 class="notice-title">Notice</h3>
            ${noticeData.isNew ? '<span class="new-badge">New</span>' : ''}
        </div>
        <div class="notice-meta">
            <div class="meta-item">
                <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>${noticeData.author}</span>
            </div>
            <div class="meta-item">
                <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>${noticeData.date}</span>
            </div>
        </div>
        <p class="notice-description">${noticeData.description}</p>
    `;
    
    noticesGrid.insertBefore(noticeCard, noticesGrid.firstChild);
    
    // Add click listener to new card
    noticeCard.addEventListener('click', function() {
        showNoticeDetails(noticeData);
    });
}

// Console log for debugging
console.log('Notices page initialized successfully');