<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices - UOC Football</title>
    <?php
    $base = rtrim(ROOT, '/');
    $commonFile = __DIR__ . '/../../public/assets/css/playerCommon.css';
    $commonVersion = file_exists($commonFile) ? filemtime($commonFile) : time();
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/notices.css">
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
                <a href="<?php echo $base; ?>/Schedule">Schedule</a>
                <a href="<?php echo $base; ?>/Analyze">Analyze</a>
                <a href="<?php echo $base; ?>/Notices" class="active">Notices</a>
                <a href="<?php echo $base; ?>/MealPlan">Meal Plan</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                </div>
                <div class="user-profile">
                    <img src="<?php echo htmlspecialchars($data['player_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="User Profile">
                </div>
            </div>
        </header>

        <div class="page-header">
            <h1>Notices</h1>
        </div>

        <div class="search-filter-section">
            <div class="search-box">
                <input type="text" placeholder="Search here..." id="searchInput">
            </div>
            <div class="filter-dropdown">
                <button class="filter-btn">🔽 All</button>
            </div>
        </div>

        <div class="notices-grid" id="noticesGrid">
            <?php if (!empty($data['notices'])): ?>
                <?php foreach ($data['notices'] as $notice): ?>
                    <div class="notice-card">
                        <div class="notice-header">
                            <h3><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></h3>
                            <?php if (!empty($notice['is_new'])): ?>
                                <span class="badge-new">NEW</span>
                            <?php endif; ?>
                        </div>
                        <div class="notice-meta">
                            <span class="author">👤 <?php echo htmlspecialchars($notice['author'] ?? 'Admin'); ?></span>
                            <span class="date">📅 <?php echo htmlspecialchars($notice['date'] ?? ''); ?></span>
                        </div>
                        <p class="notice-content"><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="notice-card">
                    <div class="notice-header">
                        <h3>No notices yet</h3>
                    </div>
                    <p class="notice-content">Admin notices for present team members will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const filter = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.notice-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(filter) ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>
