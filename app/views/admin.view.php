<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>UOC_football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/adminDashboard/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
                <div class="left-section">
                    <a href="<?php echo ROOT; ?>/admin">
                        <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                    </a>
                </div>
                <div class="right-section">
                    <img class="avatar" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/avatar.jpg" alt="Admin Avatar">
                    <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
                </div>
        </div>
        <div class="quickActions">
            <h2 class="section-title">Quick Actions</h2>
            <div class="actions-grid">
                <a class="action-card icon-purple" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/teams.svg" alt="team icon" class="action-icon">
                    </div>
                    <p class="action-label">Teams</p>
                </a>
                <a class="action-card icon-blue" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/test.svg" alt="results icon" class="action-icon">
                    </div>
                    <p class="action-label">Results</p>
                </a>
                <a class="action-card icon-green" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/event.svg" alt="event icon" class="action-icon">
                    </div>
                    <p class="action-label">Events</p>
                </a>
                <a class="action-card icon-red" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/inventory.svg" alt="inventory icon" class="action-icon">
                    </div>
                    <p class="action-label">Inventory</p>
                </a>
                <a class="action-card icon-pink" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/gallery.svg" alt="gallery icon" class="action-icon">
                    </div>
                    <p class="action-label">Gallery</p>
                </a>
                <a class="action-card icon-indigo" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/news.svg" alt="news icon" class="action-icon">
                    </div>
                    <p class="action-label">News</p>
                </a>
                <a class="action-card icon-yellow" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/store.svg" alt="store icon" class="action-icon">
                    </div>
                    <p class="action-label">Store</p>
                </a>
                <a class="action-card icon-orange" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/budget.svg" alt="budget icon" class="action-icon">
                    </div>
                    <p class="action-label">Budget</p>
                </a>
            </div>
        </div>
        <div class="welcome-banner">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome back,</br> Admin!</h2>
                <div class="welcome-datetime">
                    <p class="date"><?php echo date('l, F j, Y'); ?></p>
                    <p class="time"><?php echo date('h:i A'); ?></p>
                </div>
            </div>
        </div>
        <div class="main-grid">
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
                            <h3 class="notice-title">Upcoming Match Schedule</h3>
                            <p class="notice-date">August 15, 2024</p>
                        </li>
                        <li class="notice-item">
                            <h3 class="notice-title">New Training Sessions</h3>
                            <p class="notice-date">August 10, 2024</p>
                        </li>
                        <li class="notice-item">
                            <h3 class="notice-title">Inventory Update Required</h3>
                            <p class="notice-date">August 5, 2024</p>
                        </li>
                    </ul>
                </div>
            </div>
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
                                    <h3 class="event-title">Upcoming Match Schedule</h3>
                                    <p class="event-date">August 15, 2024</p>
                                </div>
                                <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                                
                            </li>
                            <li class="event-item">
                                <div class="event-detail">
                                    <h3 class="event-title">New Training Sessions</h3>
                                    <p class="event-date">August 10, 2024</p>
                                </div>
                                <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/training.svg" alt="training icon" class="action-icon">
                                </div>
                            </li>
                        </ul>
                </div>
            </div>
            <div class="inventory card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Inventory Status</h2>
                    </div>
                </div>
                <div class="inventory-list">
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Bibs</span>
                        </div>
                        <span class="inventory-stock stock-high">45 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Footballs</span>
                        </div>
                        <span class="inventory-stock stock-medium">12 in stock</span>
                    </div>

                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Markers</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Cones</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Resistant band</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Bottles</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>

                </div>
            </div>
        </div>    
    </div>
</body>
</html>