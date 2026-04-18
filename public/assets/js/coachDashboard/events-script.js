let currentEditingEventId = null;
let currentFilter = 'all';

function initializeFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            filterButtons.forEach(function (btn) {
                btn.classList.remove('active');
            });

            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter') || 'all';
            filterEvents(currentFilter);
        });
    });
}

function filterEvents(filter) {
    const eventCards = document.querySelectorAll('.event-card');

    eventCards.forEach(function (card) {
        const cardType = card.getAttribute('data-type');

        if (filter === 'all') {
            card.style.display = 'block';
            return;
        }

        if (filter === 'matches' && cardType === 'match') {
            card.style.display = 'block';
            return;
        }

        if (filter === 'training' && cardType === 'training') {
            card.style.display = 'block';
            return;
        }

        card.style.display = 'none';
    });
}

function initializePagination() {
    const paginationButtons = document.querySelectorAll('.pagination-btn.page-number');

    paginationButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            paginationButtons.forEach(function (btn) {
                btn.classList.remove('active');
            });

            this.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
}

function extractIdFromOnclick(onclickValue) {
    if (!onclickValue) {
        return null;
    }

    const match = onclickValue.match(/editEvent\((\d+)\)/);
    return match ? parseInt(match[1], 10) : null;
}

function initializeExistingCardIds() {
    const cards = document.querySelectorAll('.event-card');

    cards.forEach(function (card, index) {
        let eventId = parseInt(card.getAttribute('data-event-id'), 10);

        if (Number.isNaN(eventId)) {
            const editBtn = card.querySelector('.edit-icon-btn');
            const extractedId = editBtn ? extractIdFromOnclick(editBtn.getAttribute('onclick')) : null;
            eventId = extractedId || (index + 1);
            card.setAttribute('data-event-id', String(eventId));
        }

        const editButton = card.querySelector('.edit-icon-btn');
        const deleteButton = card.querySelector('.delete-icon-btn');

        if (editButton) {
            editButton.setAttribute('onclick', 'editEvent(' + eventId + ')');
        }

        if (deleteButton) {
            deleteButton.setAttribute('onclick', 'deleteEvent(' + eventId + ')');
        }
    });
}

function getNextEventId() {
    const cards = document.querySelectorAll('.event-card[data-event-id]');
    let maxId = 0;

    cards.forEach(function (card) {
        const cardId = parseInt(card.getAttribute('data-event-id'), 10);
        if (!Number.isNaN(cardId) && cardId > maxId) {
            maxId = cardId;
        }
    });

    return maxId + 1;
}

function openAddEventModal() {
    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('eventForm');

    currentEditingEventId = null;
    modalTitle.textContent = 'Add New Event';
    form.reset();
    modal.classList.add('active');
}

function closeEventModal() {
    const modal = document.getElementById('eventModal');
    modal.classList.remove('active');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    if (Number.isNaN(date.getTime())) {
        return dateString;
    }

    const options = { year: 'numeric', month: 'long', day: '2-digit' };
    return date.toLocaleDateString('en-US', options).replace(',', '');
}

function toInputDate(displayDate) {
    const parsed = new Date(displayDate);
    if (Number.isNaN(parsed.getTime())) {
        return '';
    }

    const year = parsed.getFullYear();
    const month = String(parsed.getMonth() + 1).padStart(2, '0');
    const day = String(parsed.getDate()).padStart(2, '0');

    return year + '-' + month + '-' + day;
}

function getEventCardById(eventId) {
    return document.querySelector('.event-card[data-event-id="' + eventId + '"]');
}

function getCardDetails(card) {
    const detailItems = card.querySelectorAll('.detail-item span');

    return {
        type: card.getAttribute('data-type') || '',
        title: (card.querySelector('.event-title') || {}).textContent || '',
        date: detailItems[0] ? detailItems[0].textContent.trim() : '',
        time: detailItems[1] ? detailItems[1].textContent.trim() : '',
        location: detailItems[2] ? detailItems[2].textContent.trim() : '',
        coach: detailItems[3] ? detailItems[3].textContent.trim() : ''
    };
}

function editEvent(eventId) {
    const card = getEventCardById(eventId);
    if (!card) {
        return;
    }

    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('modalTitle');
    const formData = getCardDetails(card);

    currentEditingEventId = eventId;
    modalTitle.textContent = 'Edit Event';

    document.getElementById('eventType').value = formData.type;
    document.getElementById('eventTitle').value = formData.title;
    document.getElementById('eventDate').value = toInputDate(formData.date);
    document.getElementById('eventTime').value = formData.time;
    document.getElementById('eventLocation').value = formData.location;
    document.getElementById('eventCoach').value = formData.coach;

    modal.classList.add('active');
}

function deleteEvent(eventId) {
    const card = getEventCardById(eventId);
    if (!card) {
        return;
    }

    const confirmed = window.confirm('Delete this event card?');
    if (!confirmed) {
        return;
    }

    card.remove();
    showSuccessMessage('Event deleted successfully!');
}

function updateEventCard(card, eventData, eventId) {
    const icon = eventData.type === 'training' ? '🏃' : '⚽';
    const subtitle = eventData.type === 'training' ? 'Training Session' : 'Match';

    card.classList.remove('training', 'match');
    card.classList.add(eventData.type);
    card.setAttribute('data-type', eventData.type);
    card.setAttribute('data-event-id', String(eventId));

    const iconNode = card.querySelector('.event-icon');
    const titleNode = card.querySelector('.event-title');
    const subtitleNode = card.querySelector('.event-subtitle');
    const detailSpans = card.querySelectorAll('.detail-item span');

    if (iconNode) {
        iconNode.textContent = icon;
    }
    if (titleNode) {
        titleNode.textContent = eventData.title;
    }
    if (subtitleNode) {
        subtitleNode.textContent = subtitle;
    }

    if (detailSpans[0]) {
        detailSpans[0].textContent = eventData.date;
    }
    if (detailSpans[1]) {
        detailSpans[1].textContent = eventData.time;
    }
    if (detailSpans[2]) {
        detailSpans[2].textContent = eventData.location;
    }
    if (detailSpans[3]) {
        detailSpans[3].textContent = eventData.coach;
    }

    const editButton = card.querySelector('.edit-icon-btn');
    const deleteButton = card.querySelector('.delete-icon-btn');

    if (editButton) {
        editButton.setAttribute('onclick', 'editEvent(' + eventId + ')');
    }
    if (deleteButton) {
        deleteButton.setAttribute('onclick', 'deleteEvent(' + eventId + ')');
    }
}

function createEventCard(data) {
    const card = document.createElement('div');
    card.className = 'event-card ' + data.type;
    card.setAttribute('data-type', data.type);
    card.setAttribute('data-event-id', String(data.id));

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
            <div class="event-card-actions">
                <button type="button" class="edit-icon-btn" onclick="editEvent(${data.id})" aria-label="Edit event" title="Edit event">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
                <button type="button" class="delete-icon-btn" onclick="deleteEvent(${data.id})" aria-label="Delete event" title="Delete event">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                        <path d="M10 11v6M14 11v6"></path>
                    </svg>
                </button>
            </div>
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

function handleFormSubmit(event) {
    event.preventDefault();

    const eventType = document.getElementById('eventType').value;
    const eventTitle = document.getElementById('eventTitle').value;
    const eventDate = document.getElementById('eventDate').value;
    const eventTime = document.getElementById('eventTime').value;
    const eventLocation = document.getElementById('eventLocation').value;
    const eventCoach = document.getElementById('eventCoach').value;

    if (!eventType || !eventTitle || !eventDate || !eventTime || !eventLocation || !eventCoach) {
        alert('Please fill in all fields');
        return;
    }

    const eventData = {
        type: eventType,
        title: eventTitle,
        date: formatDate(eventDate),
        time: eventTime,
        location: eventLocation,
        coach: eventCoach
    };

    const eventsGrid = document.getElementById('eventsGrid');

    if (currentEditingEventId !== null) {
        const existingCard = getEventCardById(currentEditingEventId);
        if (existingCard) {
            updateEventCard(existingCard, eventData, currentEditingEventId);
            showSuccessMessage('Event updated successfully!');
        }
    } else {
        const eventId = getNextEventId();
        const eventCard = createEventCard({
            id: eventId,
            type: eventData.type,
            title: eventData.title,
            date: eventData.date,
            time: eventData.time,
            location: eventData.location,
            coach: eventData.coach
        });

        eventsGrid.insertBefore(eventCard, eventsGrid.firstChild);
        showSuccessMessage('Event added successfully!');
    }

    closeEventModal();
    currentEditingEventId = null;
    filterEvents(currentFilter);
}

function ensureNotificationStyle() {
    if (document.getElementById('coach-events-toast-style')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'coach-events-toast-style';
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
}

function showSuccessMessage(message) {
    ensureNotificationStyle();

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

    document.body.appendChild(notification);

    setTimeout(function () {
        notification.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(function () {
            notification.remove();
        }, 300);
    }, 3000);
}

window.addEventListener('click', function (event) {
    const modal = document.getElementById('eventModal');
    if (event.target === modal) {
        closeEventModal();
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeEventModal();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    initializeExistingCardIds();
    initializeFilters();
    initializePagination();

    const form = document.getElementById('eventForm');
    form.addEventListener('submit', handleFormSubmit);

    filterEvents(currentFilter);
    console.log('Schedule page initialized successfully');
});