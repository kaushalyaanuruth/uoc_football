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
    $notices = is_array($data['notices'] ?? null) ? $data['notices'] : [];
    $noticeCount = count($notices);
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
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
                <a href="<?php echo $base; ?>/PlayerInventory">Inventory</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                    <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
                </div>
                <div class="user-profile">
                    <img src="<?php echo htmlspecialchars($data['player_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="User Profile">
                </div>
                <a class="player-logout-btn" href="<?php echo $base; ?>/login/logout">Logout</a>
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
            <?php if (!empty($data['events'])): ?>
                <?php foreach ($data['events'] as $event): ?>
                    <?php
                        $typeKey = strtolower((string)($event['type_key'] ?? $event['type'] ?? 'other'));
                        $cardType = $typeKey === 'match' ? 'match' : ($typeKey === 'training' ? 'training' : 'other');
                        $iconText = $cardType === 'match' ? 'M' : ($cardType === 'training' ? 'T' : 'E');
                    ?>
                    <div class="event-card" data-type="<?php echo htmlspecialchars($cardType); ?>">
                        <div class="event-icon <?php echo htmlspecialchars($cardType); ?>">
                            <?php echo $iconText; ?>
                        </div>
                        <div class="event-content">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="event-type"><?php echo htmlspecialchars($event['type']); ?></p>
                            <div class="event-details">
                                <span><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></span>
                                <span><strong>Time:</strong> <?php echo htmlspecialchars($event['time']); ?></span>
                                <span><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No upcoming events available right now.</p>
            <?php endif; ?>
        </div>

        <div class="notification-overlay" id="notificationOverlay" style="display: none;">
            <p style="font-size: 0.82rem; color: #4a1150; font-weight: 700; margin-bottom: 10px;">Latest Notices</p>
            <?php if (!empty($notices)): ?>
                <?php foreach (array_slice($notices, 0, 5) as $notice): ?>
                    <div style="padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid #eee;">
                        <p style="font-size:0.8rem; color:#4a1150; font-weight:600; margin-bottom:4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                        <p style="font-size:0.82rem; color:#4b5563; line-height:1.35;"><?php echo htmlspecialchars($notice['content'] ?? ($notice['text'] ?? '')); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="font-size:0.82rem; color:#6b7280;">No notices available.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        window.HEADER_PROFILE_MODAL_CONFIG = {
            fetchUrl: '<?php echo $base; ?>/PlayerDashboard/profileData',
            updateUrl: '<?php echo $base; ?>/PlayerDashboard/updateProfile',
            triggerSelector: '.user-profile'
        };

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

        const bell = document.getElementById('notificationBell');
        const overlay = document.getElementById('notificationOverlay');

        bell?.addEventListener('click', (e) => {
            e.stopPropagation();
            overlay.style.display = overlay.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (overlay && overlay.style.display === 'block' && !overlay.contains(e.target) && !bell.contains(e.target)) {
                overlay.style.display = 'none';
            }
        });
    </script>
    <script src="<?php echo $base; ?>/assets/js/common/headerProfileModal.js"></script>
</body>
</html>
