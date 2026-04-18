<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
    <title>UOC_football - Notices</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/notices-style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/coach">
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </a>
            </div>
            <nav class="nav-menu">
                <a href="<?php echo ROOT; ?>/coachDashboard" class="nav-link">Home</a>
                <a href="<?php echo ROOT; ?>/coachEvents" class="nav-link">Events</a>
                <a href="<?php echo ROOT; ?>/coachMealPlan" class="nav-link">Meal Plan</a>
                <a href="<?php echo ROOT; ?>/coachPerformance" class="nav-link">Performance</a>
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link">Attendance</a>
                <a href="<?php echo ROOT; ?>/coachNotices" class="nav-link active">Notices</a>
            </nav>
            <div class="right-section">
                <img class="avatar" src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/avatar.jpg" alt="Coach Avatar">
                <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="search-filter-bar">
            <div class="search-box">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input type="text" class="search-input" placeholder="Search here..." id="searchInput">
            </div>
            <div class="filter-actions">
                <button class="filter-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                </button>
                <button class="add-notice-btn">+ Add</button>
            </div>
        </div>

        <!-- Notices Grid -->
        <div class="notices-grid">
            <!-- Notice Card 1 -->
            <div class="notice-card">
                <div class="notice-header">
                    <h3 class="notice-title">Notice</h3>
                    <span class="new-badge">New</span>
                </div>
                <div class="notice-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Mathews</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Jan 15, 2024</span>
                    </div>
                </div>
                <p class="notice-description">
                    Tomorrow's (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                </p>
            </div>

            <!-- Notice Card 2 -->
            <div class="notice-card">
                <div class="notice-header">
                    <h3 class="notice-title">Notice</h3>
                    <span class="new-badge">New</span>
                </div>
                <div class="notice-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Williams</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Jan 15, 2024</span>
                    </div>
                </div>
                <p class="notice-description">
                    Tomorrow's (26th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                </p>
            </div>

            <!-- Notice Card 3 -->
            <div class="notice-card">
                <div class="notice-header">
                    <h3 class="notice-title">Notice</h3>
                </div>
                <div class="notice-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Mathews</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Jan 14, 2024</span>
                    </div>
                </div>
                <p class="notice-description">
                    Tomorrow's (20th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                </p>
            </div>

            <!-- Notice Card 4 -->
            <div class="notice-card">
                <div class="notice-header">
                    <h3 class="notice-title">Notice</h3>
                </div>
                <div class="notice-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Williams</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Jan 14, 2024</span>
                    </div>
                </div>
                <p class="notice-description">
                    Tomorrow's (23th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                </p>
            </div>

            <!-- Notice Card 5 -->
            <div class="notice-card">
                <div class="notice-header">
                    <h3 class="notice-title">Notice</h3>
                </div>
                <div class="notice-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Mathews</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Jan 12, 2024</span>
                    </div>
                </div>
                <p class="notice-description">
                    Tomorrow's (20th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                </p>
            </div>

            <!-- Notice Card 6 -->
            <div class="notice-card">
                <div class="notice-header">
                    <h3 class="notice-title">Notice</h3>
                </div>
                <div class="notice-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Williams</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Jan 12, 2024</span>
                    </div>
                </div>
                <p class="notice-description">
                    Tomorrow's (22th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                </p>
            </div>

            <!-- Notice Card 7 -->
            <div class="notice-card">
                <div class="notice-header">
                    <h3 class="notice-title">Notice</h3>
                </div>
                <div class="notice-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Mathews</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Jan 10, 2024</span>
                    </div>
                </div>
                <p class="notice-description">
                    Tomorrow's (18th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                </p>
            </div>

            <!-- Notice Card 8 -->
            <div class="notice-card">
                <div class="notice-header">
                    <h3 class="notice-title">Notice</h3>
                </div>
                <div class="notice-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Coach Williams</span>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Jan 10, 2024</span>
                    </div>
                </div>
                <p class="notice-description">
                    Tomorrow's (15th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                </p>
            </div>
        </div>
    </div>

    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/notices-script.js"></script>
</body>
</html>