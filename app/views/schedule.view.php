<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - UOC Football</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/schedule.css">
</head>

<body>
    <div class="schedule-container">
        <!-- Header -->
        <header>
            <div class="logo-section">
                <img src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo ROOT; ?>/PlayerDashboard">Home</a>
                <a href="#" class="active">Schedule</a>
                <a href="<?php echo ROOT; ?>/Analyze">Analyze</a>
                <a href="<?php echo ROOT; ?>/Notices">Notices</a>
                <a href="<?php echo ROOT; ?>/MealPlan">Meal Plan</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon">
                    <img src="<?php echo ROOT; ?>/assets/images/notification-bell.png" alt="Notifications"
                        style="width: 24px; cursor: pointer;">
                </div>
                <div class="user-profile">
                    <img src="<?php echo ROOT; ?>/assets/images/user-placeholder.jpg" alt="User Profile"
                        style="width: 40px; border-radius: 50%;">
                </div>
            </div>
        </header>

        <!-- Filters -->
        <div class="filters">
            <button class="filter-btn active">All Events</button>
            <button class="filter-btn">Matches Only</button>
            <button class="filter-btn">Training Only</button>
        </div>

        <!-- Event Grid -->
        <div class="event-grid">
            <?php foreach ($data['events'] as $event): ?>
                <div class="event-card">
                    <div class="event-icon">
                        <?php if ($event['type'] == 'training'): ?>
                            <!-- Using a simple dumbbell emoji/SVG placeholder for now -->
                            <span style="font-size: 24px; color: white;">🏋️</span>
                        <?php else: ?>
                            <span style="font-size: 24px; color: white;">🏆</span>
                        <?php endif; ?>
                    </div>
                    <div class="event-info">
                        <h3>
                            <?php echo $event['title']; ?>
                        </h3>
                        <p class="subtitle">
                            <?php echo $event['subtitle']; ?>
                        </p>

                        <div class="event-detail">
                            <span class="icon">📅</span>
                            <span>
                                <?php echo $event['date']; ?>
                            </span>
                        </div>
                        <div class="event-detail">
                            <span class="icon">🕒</span>
                            <span>
                                <?php echo $event['time']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="page-btn">Previous</button>
            <button class="page-num active">1</button>
            <button class="page-num">2</button>
            <button class="page-num">3</button>
            <button class="page-btn">Next</button>
        </div>
    </div>
</body>

</html>