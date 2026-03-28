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
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo ROOT; ?>/uploads/players/notification.png" alt="Notifications"
                        style="width: 24px; cursor: pointer;">
                </div>
                <div class="user-profile">
                    <img src="<?php echo ROOT; ?>/assets/images/user-placeholder.jpg" alt="User Profile">
                </div>
            </div>
        </header>

        <!-- Filters -->
        <div class="filters">
            <button class="filter-btn active" onclick="filterEvents('all', this)">All Events</button>
            <button class="filter-btn" onclick="filterEvents('match', this)">Matches Only</button>
            <button class="filter-btn" onclick="filterEvents('training', this)">Training Only</button>
        </div>

        <!-- Event Grid -->
        <div class="event-grid" id="eventGrid">
            <?php foreach ($data['events'] as $index => $event): ?>
                <div class="event-card" data-type="<?php echo $event['type']; ?>" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <div class="event-icon">
                        <?php if ($event['type'] == 'training'): ?>
                            <span style="font-size: 28px; color: white;">🏋️</span>
                        <?php else: ?>
                            <span style="font-size: 28px; color: white;">🏆</span>
                        <?php endif; ?>
                    </div>
                    <div class="event-info">
                        <span class="subtitle">
                            <?php echo $event['subtitle']; ?>
                        </span>
                        <h3>
                            <?php echo $event['title']; ?>
                        </h3>

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
            <button class="page-num" onclick="alert('Page 2 is empty for now!')">2</button>
            <button class="page-num" onclick="alert('Page 3 is empty for now!')">3</button>
            <button class="page-btn">Next</button>
        </div>

        <!-- Notification Overlay -->
        <div class="notification-overlay" id="notificationOverlay" style="display: none;">
            <p style="font-size: 0.9rem; color: #333; line-height: 1.5; font-weight: 500;">
                📢 Recent Updates:
            </p>
            <hr style="margin: 10px 0; border: none; border-top: 1px solid #eee;">
            <p style="font-size: 0.85rem; color: #666; line-height: 1.4;">
                Tomorrow's practice has been moved to 7:00 AM instead of 6:00 AM. Please check your schedule.
            </p>
        </div>
    </div>

    <script>
        // Notification Toggle logic (Consistent with Dashboard)
        const bell = document.getElementById('notificationBell');
        const overlay = document.getElementById('notificationOverlay');

        bell.addEventListener('click', (e) => {
            e.stopPropagation();
            overlay.style.display = overlay.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!overlay.contains(e.target) && e.target !== bell) {
                overlay.style.display = 'none';
            }
        });

        // Event Filtering Logic
        function filterEvents(type, btn) {
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cards = document.querySelectorAll('.event-card');
            cards.forEach((card, index) => {
                const cardType = card.getAttribute('data-type');
                if (type === 'all' || cardType === type) {
                    card.style.display = 'flex';
                    card.style.animation = 'none';
                    // Re-trigger animation
                    setTimeout(() => {
                        card.style.animation = `cardEntrance 0.6s ease-out backwards ${index * 0.05}s`;
                    }, 10);
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>
