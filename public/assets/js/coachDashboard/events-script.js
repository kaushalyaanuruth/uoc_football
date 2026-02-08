// Schedule JavaScript

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializePagination();
});

// Initialize filter buttons
function initializeFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get filter value
            const filter = this.getAttribute('data-filter');
            
            // Filter events
            filterEvents(filter);
        });
    });
}

// Filter events based on type
function filterEvents(filter) {
    const eventCards = document.querySelectorAll('.event-card');
    
    eventCards.forEach(card => {
        const cardType = card.getAttribute('data-type');
        
        if (filter === 'all') {
            card.style.display = 'block';
        } else if (filter === 'matches' && cardType === 'match') {
            card.style.display = 'block';
        } else if (filter === 'training' && cardType === 'training') {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Initialize pagination
function initializePagination() {
    const paginationButtons = document.querySelectorAll('.pagination-btn.page-number');
    
    paginationButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all page buttons
            paginationButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
}

// Open add event modal
function openAddEventModal() {
    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('eventForm');
    
    // Set modal title
    modalTitle.textContent = 'Add New Event';
    
    // Reset form
    form.reset();
    
    // Show modal
    modal.classList.add('active');
}

// Close event modal
function closeEventModal() {
    const modal = document.getElementById('eventModal');
    modal.classList.remove('active');
}

// Edit event
function editEvent(eventId) {
    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('modalTitle');
    
    // Set modal title
    modalTitle.textContent = 'Edit Event';
    
    // In a real application, you would fetch the event data here
    // For now, we'll just open the modal
    
    // Show modal
    modal.classList.add('active');
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eventForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const eventType = document.getElementById('eventType').value;
        const eventTitle = document.getElementById('eventTitle').value;
        const eventDate = document.getElementById('eventDate').value;
        const eventTime = document.getElementById('eventTime').value;
        const eventLocation = document.getElementById('eventLocation').value;
        const eventCoach = document.getElementById('eventCoach').value;
        
        // Validate
        if (!eventType || !eventTitle || !eventDate || !eventTime || !eventLocation || !eventCoach) {
            alert('Please fill in all fields');
            return;
        }
        
        // Create new event card
        const eventCard = createEventCard({
            type: eventType,
            title: eventTitle,
            date: formatDate(eventDate),
            time: eventTime,
            location: eventLocation,
            coach: eventCoach
        });
        
        // Add to grid
        const eventsGrid = document.getElementById('eventsGrid');
        eventsGrid.insertBefore(eventCard, eventsGrid.firstChild);
        
        // Close modal
        closeEventModal();
        
        // Show success message
        showSuccessMessage('Event added successfully!');
    });
});

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: '2-digit' };
    return date.toLocaleDateString('en-US', options).replace(',', '');
}

// Create event card
function createEventCard(data) {
    const card = document.createElement('div');
    card.className = 'event-card ' + data.type;
    card.setAttribute('data-type', data.type);
    
    const icon = data.type === 'training' ? '🏃' : '⚽';
    
    card.innerHTML = `
        <div class="event-card-header">
            <div class="event-icon-wrapper">
                <span class="event-icon">${icon}</span>
            </div>
            <div class="event-header-content">
                <h3 class="event-title">${data.title}</h3>
                <p class="event-subtitle">${data.type === 'training' ? 'Training Session' : 'Match'}</p>
            </div>
            <button class="edit-icon-btn" onclick="editEvent(${Date.now()})">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
            </button>
        </div>
        <div class="event-details">
            <div class="detail-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>${data.date}</span>
            </div>
            <div class="detail-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>${data.time}</span>
            </div>
            <div class="detail-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>${data.location}</span>
            </div>
            <div class="detail-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>${data.coach}</span>
            </div>
        </div>
    `;
    
    return card;
}

// Show success message
function showSuccessMessage(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        z-index: 10000;
        font-weight: 600;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('eventModal');
    if (event.target === modal) {
        closeEventModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEventModal();
    }
});

console.log('Schedule page initialized successfully');