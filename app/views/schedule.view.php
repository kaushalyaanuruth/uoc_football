<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - UOC Football</title>
    <?php
    $base = rtrim(ROOT, '/');
    $cssFile = __DIR__ . '/../../public/assets/css/schedule.css';
    $cssVersion = file_exists($cssFile) ? filemtime($cssFile) : time();
    $commonFile = __DIR__ . '/../../public/assets/css/playerCommon.css';
    $commonVersion = file_exists($commonFile) ? filemtime($commonFile) : time();
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/schedule.css?v=<?php echo $cssVersion; ?>">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerCommon.css?v=<?php echo $commonVersion; ?>">
</head>
<body>
    <div class="dashboard-container">
        <header class="player-header">
            <div class="logo-section">
                <img src="<?php echo $base; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo $base; ?>/PlayerDashboard">Home</a>
                <a href="<?php echo $base; ?>/Schedule" class="active">Schedule</a>
                <a href="<?php echo $base; ?>/Analyze">Analyze</a>
                <a href="<?php echo $base; ?>/Notices">Notices</a>
                <a href="<?php echo $base; ?>/MealPlan">Meal Plan</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                </div>
                <div class="user-profile">
                    <img src="<?php echo $base; ?>/assets/images/user-placeholder.jpg" alt="User Profile">
                </div>
            </div>
        </header>

        <div class="page-header">
            <h1>Schedule</h1>
        </div>

        <div class="filters">
            <button class="filter-btn active" onclick="filterEvents('all', this)">All Events</button>
            <button class="filter-btn" onclick="filterEvents('matches', this)">Matches Only</button>
            <button class="filter-btn" onclick="filterEvents('training', this)">Training Only</button>
        </div>

        <div class="events-grid" id="eventsGrid">
            <?php foreach ($data['events'] as $event): ?>
                <div class="event-card" data-type="<?php echo strtolower($event['type']); ?>">
                    <div class="event-icon <?php echo strtolower($event['type']); ?>">
                        <?php echo $event['type'] === 'Match' ? 'M' : 'T'; ?>
                    </div>
                    <div class="event-content">
                        <h3><?php echo $event['title']; ?></h3>
                        <p class="event-type"><?php echo $event['type']; ?></p>
                        <div class="event-details">
                            <span><strong>Date:</strong> <?php echo $event['date']; ?></span>
                            <span><strong>Time:</strong> <?php echo $event['time']; ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <button class="pagination-btn">Previous</button>
            <button class="pagination-num active">1</button>
            <button class="pagination-num">2</button>
            <button class="pagination-num">3</button>
            <button class="pagination-btn">Next</button>
        </div>
    </div>

    <script>
        function filterEvents(type, button) {
            const cards = document.querySelectorAll('.event-card');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Remove active class from all buttons
            buttons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            button.classList.add('active');
            
            // Filter cards based on type
            cards.forEach(card => {
                if (type === 'all') {
                    card.style.display = '';
                } else if (type === 'matches') {
                    card.style.display = card.dataset.type === 'match' ? '' : 'none';
                } else if (type === 'training') {
                    card.style.display = card.dataset.type === 'training' ? '' : 'none';
                }
            });
        }
    </script>
</body>
</html>
