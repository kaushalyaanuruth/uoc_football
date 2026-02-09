<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>UOC_football - Coach Dashboard</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/style.css">
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
                <a href="<?php echo ROOT; ?>/coachDashboard" class="nav-link active">Home</a>
                <a href="<?php echo ROOT; ?>/coachEvents" class="nav-link">Events</a>
                <a href="<?php echo ROOT; ?>/coachMealPlan" class="nav-link">Meal Plan</a>
                <a href="<?php echo ROOT; ?>/coachPerformance" class="nav-link">Performance</a>
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link">Attendance</a>
                <a href="<?php echo ROOT; ?>/coachNotices" class="nav-link">Notices</a>
            </nav>
            <div class="right-section">
                <img class="avatar" src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/avatar.jpg" alt="Coach Avatar">
                <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="welcome-banner">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome,<br>Coach Priyajan!</h1>
                <div class="welcome-datetime">
                    <p class="date"><?php echo date('l, F j, Y'); ?></p>
                    <p class="time"><?php echo date('h:i A'); ?></p>
                </div>
            </div>
        </div>

        <div class="main-grid">
            <!-- Next Event Card -->
            <div class="nextEvent card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>What is next?</h2>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="event-list">
                        <li class="event-item">
                            <div class="event-detail">
                                <h3 class="event-title">Next Practice</h3>
                                <p class="event-date">15, August, morning, 6.00 A.M.</p>
                            </div>
                            <div class="icon-container">
                                <img src="<?php echo ROOT; ?>/assets/images/coachDashboard/icons/practice.svg" alt="practice icon" class="action-icon">
                            </div>
                        </li>
                        <li class="event-item">
                            <div class="event-detail">
                                <h3 class="event-title">Practice Match</h3>
                                <p class="event-date">UOC vs Old Bends - Monday, August 5 at 6 a.m</p>
                            </div>
                            <div class="icon-container">
                                <img src="<?php echo ROOT; ?>/assets/images/coachDashboard/icons/match.svg" alt="match icon" class="action-icon">
                            </div>
                        </li>
                        <li class="event-item">
                            <div class="event-detail">
                                <h3 class="event-title">Fitness Training</h3>
                                <p class="event-date">Wednesday, August 7 at 5 p.m</p>
                            </div>
                            <div class="icon-container">
                                <img src="<?php echo ROOT; ?>/assets/images/coachDashboard/icons/training.svg" alt="training icon" class="action-icon">
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Meal Plan Card -->
            <div class="mealPlan card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Meal Plan</h2>
                    </div>
                    <button class="add-btn">+ Add</button>
                </div>
                <div class="card-body">
                    <div class="meal-tabs">
                        <button class="meal-tab active" data-meal="breakfast">Breakfast</button>
                        <button class="meal-tab" data-meal="lunch">Lunch</button>
                        <button class="meal-tab" data-meal="dinner">Dinner</button>
                    </div>
                    <div class="meal-items" id="mealItems">
                        <div class="meal-item">
                            <span>Basmati or Red Rice</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                        <div class="meal-item">
                            <span>Chicken, Egg, Fish</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                        <div class="meal-item">
                            <span>Vegetable(Minimum 3)</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                        <div class="meal-item">
                            <span>Paip</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                        <div class="meal-item">
                            <span>Yogurt</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notices Card -->
            <div class="notices card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Notices</h2>
                    </div>
                    <button class="add-btn">+ Add</button>
                </div>
                <div class="card-body">
                    <ul class="notices-list">
                        <li class="notice-item">
                            <h3 class="notice-title">Team Meeting Tomorrow</h3>
                            <p class="notice-date">August 16, 2024</p>
                        </li>
                        <li class="notice-item">
                            <h3 class="notice-title">Updated Training Schedule</h3>
                            <p class="notice-date">August 14, 2024</p>
                        </li>
                        <li class="notice-item">
                            <h3 class="notice-title">Medical Checkup Required</h3>
                            <p class="notice-date">August 12, 2024</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/script.js"></script>
</body>
</html>