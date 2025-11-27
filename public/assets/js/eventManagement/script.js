// Event Management JavaScript

// Global variables
let currentEventId = null;
let isEditMode = false;

// DOM Elements
const modal = document.getElementById('eventModal');
const modalTitle = document.getElementById('modalTitle');
const eventForm = document.getElementById('eventForm');
const addEventBtn = document.querySelector('.add-event-btn');
const closeBtn = document.querySelector('.close-btn');
const cancelBtn = document.querySelector('.btn-secondary');

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
});

// Initialize all event listeners
function initializeEventListeners() {
    // Add event button
    if (addEventBtn) {
        addEventBtn.addEventListener('click', openAddModal);
    }

    // Close modal buttons
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    // Click outside modal to close
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Form submission
    if (eventForm) {
        eventForm.addEventListener('submit', handleFormSubmit);
    }

    // Edit buttons - use event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-btn')) {
            const btn = e.target.closest('.edit-btn');
            const eventId = btn.getAttribute('data-id');
            if (eventId) {
                openEditModal(eventId);
            }
        }
    });

    // Delete buttons - use event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const eventId = btn.getAttribute('data-id');
            if (eventId) {
                deleteEvent(eventId);
            }
        }
    });
}

// Open modal for adding new event
function openAddModal() {
    isEditMode = false;
    currentEventId = null;
    
    if (modalTitle) {
        modalTitle.textContent = 'Add New Event';
    }
    
    // Reset form
    if (eventForm) {
        eventForm.reset();
    }
    
    // Show modal
    if (modal) {
        modal.classList.add('show');
    }
}

// Open modal for editing event
function openEditModal(eventId) {
    isEditMode = true;
    currentEventId = eventId;
    
    if (modalTitle) {
        modalTitle.textContent = 'Edit Event';
    }
    
    // Fetch event data
    fetchEventData(eventId);
    
    // Show modal
    if (modal) {
        modal.classList.add('show');
    }
}

// Close modal
function closeModal() {
    if (modal) {
        modal.classList.remove('show');
    }
    
    // Reset form and state
    if (eventForm) {
        eventForm.reset();
    }
    isEditMode = false;
    currentEventId = null;
}

// Fetch event data for editing
async function fetchEventData(eventId) {
    try {
        console.log('Fetching event data for ID:', eventId);
        
        const response = await fetch(`../eventManagement/get?id=${eventId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Fetched event data:', data);

        if (data.success) {
            populateForm(data.data);
        } else {
            showMessage('Failed to fetch event data: ' + (data.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error fetching event:', error);
        showMessage('Error fetching event data: ' + error.message, 'error');
    }
}

// Populate form with event data
function populateForm(eventData) {
    // Set form field values
    if (document.getElementById('eventTitle')) {
        document.getElementById('eventTitle').value = eventData.title || '';
    }
    
    if (document.getElementById('eventDate')) {
        // Combine event_date and event_time to datetime-local format (YYYY-MM-DDTHH:MM)
        if (eventData.event_date) {
            const dateTime = eventData.event_date + 'T' + (eventData.event_time || '00:00:00').substring(0, 5);
            document.getElementById('eventDate').value = dateTime;
        }
    }
    
    if (document.getElementById('eventCategory')) {
        document.getElementById('eventCategory').value = eventData.event_type || 'other';
    }
    
    if (document.getElementById('eventLocation')) {
        document.getElementById('eventLocation').value = eventData.location || '';
    }
    
    if (document.getElementById('eventDescription')) {
        document.getElementById('eventDescription').value = eventData.description || '';
    }
    
    if (document.getElementById('eventStatus')) {
        document.getElementById('eventStatus').value = eventData.status || 'upcoming';
    }
}

// Handle form submission
async function handleFormSubmit(e) {
    e.preventDefault();
    
    // Get form data
    const formData = {
        title: document.getElementById('eventTitle').value.trim(),
        event_date: document.getElementById('eventDate').value,
        category: document.getElementById('eventCategory').value,
        location: document.getElementById('eventLocation').value.trim(),
        description: document.getElementById('eventDescription').value.trim(),
        status: document.getElementById('eventStatus').value
    };

    console.log('Form data:', formData);

    // Validation
    if (!formData.title) {
        showMessage('Please enter event title', 'error');
        return;
    }

    if (!formData.event_date) {
        showMessage('Please select event date and time', 'error');
        return;
    }

    try {
        let url, method;
        
        if (isEditMode && currentEventId) {
            // Update existing event
            url = '../eventManagement/update';
            method = 'POST';
            formData.id = currentEventId;
        } else {
            // Add new event
            url = '../eventManagement/add';
            method = 'POST';
        }

        console.log('Submitting to:', url);
        console.log('Method:', method);
        console.log('Data:', JSON.stringify(formData));

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            showMessage(data.message || 'Event saved successfully!', 'success');
            
            if (isEditMode && currentEventId) {
                // Update was successful - update the card in DOM immediately
                setTimeout(() => {
                    closeModal();
                    // Reload to show updated data
                    window.location.reload();
                }, 500);
            } else {
                // Add was successful - close modal and reload
                closeModal();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }
        } else {
            showMessage('Error: ' + (data.message || 'Failed to save event'), 'error');
        }
    } catch (error) {
        console.error('Error saving event:', error);
        showMessage('Error saving event: ' + error.message, 'error');
    }
}

// Delete event
async function deleteEvent(eventId) {
    // Confirm deletion
    if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
        return;
    }

    try {
        console.log('Deleting event ID:', eventId);

        const response = await fetch('../eventManagement/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: eventId })
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            // Remove the event card from DOM immediately
            const eventCard = document.querySelector(`.event-card[data-event-id="${eventId}"]`);
            if (eventCard) {
                eventCard.style.opacity = '0';
                eventCard.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    eventCard.remove();
                    
                    // Check if there are no more events, show empty state
                    const eventsGrid = document.querySelector('.events-grid');
                    if (eventsGrid && eventsGrid.children.length === 0) {
                        window.location.reload();
                    }
                }, 300);
            }
            
            showMessage(data.message || 'Event deleted successfully!', 'success');
        } else {
            showMessage('Error: ' + (data.message || 'Failed to delete event'), 'error');
        }
    } catch (error) {
        console.error('Error deleting event:', error);
        showMessage('Error deleting event: ' + error.message, 'error');
    }
}

// Show message
function showMessage(message, type = 'success') {
    // Check if message container exists in form
    let messageDiv = document.querySelector('#eventForm .message');
    
    if (!messageDiv) {
        // Create message div if it doesn't exist
        messageDiv = document.createElement('div');
        messageDiv.className = 'message';
        
        // Insert at the beginning of the form
        const form = document.getElementById('eventForm');
        if (form) {
            form.insertBefore(messageDiv, form.firstChild);
        }
    }
    
    // Set message content and type
    messageDiv.textContent = message;
    messageDiv.className = `message ${type} show`;
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        messageDiv.classList.remove('show');
    }, 5000);
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('en-US', options);
}

// Format date for datetime-local input
function formatDateTimeLocal(dateString) {
    const date = new Date(dateString);
    return date.toISOString().slice(0, 16);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape key to close modal
    if (e.key === 'Escape' && modal && modal.classList.contains('show')) {
        closeModal();
    }
});

console.log('Event Management script loaded successfully');
