<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>UOC_football - Coach Events</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/events-style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/coach">
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </a>
            </div>
            <nav class="nav-menu">
                <a href="<?php echo ROOT; ?>/coachDashboard" class="nav-link">Home</a>
                <a href="<?php echo ROOT; ?>/coachEvents" class="nav-link active">Events</a>
                <a href="<?php echo ROOT; ?>/coachMealPlan" class="nav-link">Meal Plan</a>
                <a href="<?php echo ROOT; ?>/coachPerformance" class="nav-link">Performance</a>
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link">Attendance</a>
                <a href="<?php echo ROOT; ?>/coachNotices" class="nav-link">Notices</a>
            </nav>
            <div class="right-section">
                <div class="bell-icon">
                    <i>🔔</i>
                </div>
                <img class="avatar" src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/avatar.jpg" alt="Coach Avatar">
            </div>
        </div>

        <!-- Filter and Add Button Section -->
        <div class="page-controls">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All Events</button>
                <button class="filter-btn" data-filter="matches">Matches Only</button>
                <button class="filter-btn" data-filter="training">Training Only</button>
            </div>
            <button class="add-event-btn" onclick="openAddEventModal()">+ Add Event</button>
        </div>

        <!-- Events Grid -->
        <div class="events-grid" id="eventsGrid">
            <!-- Training Session Card 1 -->
            <div class="event-card training" data-type="training">
                <div class="event-card-header">
                    <div class="event-icon-wrapper">
                        <span class="event-icon">🏃</span>
                    </div>
                    <div class="event-header-content">
                        <h3 class="event-title">Training Session</h3>
                        <p class="event-subtitle">Training Session</p>
                    </div>
                    <button class="edit-icon-btn" onclick="editEvent(1)">
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
                        <span>September 01 2025</span>
                    </div>
                    <div class="detail-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>8.00 am - 12.00 pm</span>
                    </div>
                    <div class="detail-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>University Ground</span>
                    </div>
                    <div class="detail-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Silva</span>
                    </div>
                </div>
            </div>

            <!-- Training Session Card 2 -->
            <div class="event-card training" data-type="training">
                <div class="event-card-header">
                    <div class="event-icon-wrapper">
                        <span class="event-icon">🏃</span>
                    </div>
                    <div class="event-header-content">
                        <h3 class="event-title">Training Session</h3>
                        <p class="event-subtitle">Training Session</p>
                    </div>
                    <button class="edit-icon-btn" onclick="editEvent(2)">
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
                        <span>September 01 2025</span>
                    </div>
                    <div class="detail-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>8.00 am - 12.00 pm</span>
                    </div>
                    <div class="detail-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>University Ground</span>
                    </div>
                    <div class="detail-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Silva</span>
                    </div>
                </div>
            </div>

            <!-- Training Session Card 3 -->
            <div class="event-card training" data-type="training">
                <div class="event-card-header">
                    <div class="event-icon-wrapper">
                        <span class="event-icon">🏃</span>
                    </div>
                    <div class="event-header-content">
                        <h3 class="event-title">Training Session</h3>
                        <p class="event-subtitle">Training Session</p>
                    </div>
                    <button class="edit-icon-btn" onclick="editEvent(3)">
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
                        <span>September 01 2025</span>
                    </div>
                    <div class="detail-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>8.00 am - 12.00 pm</span>
                    </div>
                    <div class="detail-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>University Ground</span>
                    </div>
                    <div class="detail-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Silva</span>
                    </div>
                </div>
            </div>

            <!-- More cards would be added dynamically -->
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="pagination-btn" disabled>Previous</button>
            <button class="pagination-btn page-number active">1</button>
            <button class="pagination-btn page-number">2</button>
            <button class="pagination-btn page-number">3</button>
            <button class="pagination-btn">Next</button>
        </div>
    </div>

    <!-- Add/Edit Event Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Event</h2>
                <button class="close-btn" onclick="closeEventModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="eventType">Event Type</label>
                            <select id="eventType" required>
                                <option value="">Select type</option>
                                <option value="training">Training Session</option>
                                <option value="match">Match</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="eventTitle">Event Title</label>
                            <input type="text" id="eventTitle" placeholder="Enter event title" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="eventDate">Date</label>
                            <input type="date" id="eventDate" required>
                        </div>
                        <div class="form-group">
                            <label for="eventTime">Time</label>
                            <input type="text" id="eventTime" placeholder="e.g., 8.00 am - 12.00 pm" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="eventLocation">Location</label>
                            <input type="text" id="eventLocation" placeholder="Enter location" required>
                        </div>
                        <div class="form-group">
                            <label for="eventCoach">Coach</label>
                            <input type="text" id="eventCoach" placeholder="Enter coach name" required>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" onclick="closeEventModal()">Cancel</button>
                        <button type="submit" class="btn-save">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/events-script.js"></script>
</body>
</html>