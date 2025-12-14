<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/test.svg" alt="test icon" class="action-icon">
                    </div>
                    <p class="action-label">Test</p>
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
    </div>
</body>
</html>