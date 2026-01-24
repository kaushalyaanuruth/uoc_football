<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices - UOC Football</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/notices.css">
</head>

<body>
    <div class="notices-container">
        <!-- Header -->
        <header>
            <div class="logo-section">
                <img src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo ROOT; ?>/PlayerDashboard">Home</a>
                <a href="<?php echo ROOT; ?>/Schedule">Schedule</a>
                <a href="<?php echo ROOT; ?>/Analyze">Analyze</a>
                <a href="#" class="active">Notices</a>
                <a href="<?php echo ROOT; ?>/MealPlan">Meal Plan</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon">
                    <img src="<?php echo ROOT; ?>/assets/images/notification-bell.png" alt="Notifications"
                        style="width: 24px; cursor: pointer;">
                </div>
                <div class="user-profile">
                    <img src="<?php echo ROOT; ?>/assets/images/user-placeholder.jpg" alt="User Profile">
                </div>
            </div>
        </header>

        <!-- Search and Filters -->
        <div class="notices-controls">
            <div class="search-box">
                <input type="text" placeholder="Search here...">
            </div>
            <div class="filter-controls">
                <div class="filter-icon">
                    <span style="font-size: 1.2rem;">🔍</span>
                </div>
                <select class="category-select">
                    <option>All</option>
                    <option>Training</option>
                    <option>Matches</option>
                </select>
            </div>
        </div>

        <!-- Notices Grid -->
        <div class="notices-grid">
            <?php foreach ($data['notices'] as $notice): ?>
                <div class="notice-card">
                    <?php if ($notice['is_new']): ?>
                        <span class="new-badge">New</span>
                    <?php endif; ?>

                    <h4>
                        <?php echo $notice['title']; ?>
                    </h4>

                    <div class="notice-meta">
                        <span>👤
                            <?php echo $notice['author']; ?>
                        </span>
                        <span>📅
                            <?php echo $notice['date']; ?>
                        </span>
                    </div>

                    <p class="notice-content">
                        <?php echo $notice['content']; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>