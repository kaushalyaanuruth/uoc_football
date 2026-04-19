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
    $noticesForHeader = is_array($data['notices'] ?? null) ? $data['notices'] : [];
    $noticeCount = count($noticesForHeader);
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
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
            <h1>Notices</h1>
        </div>

        <div class="search-filter-section">
            <div class="search-box">
                <input type="text" placeholder="Search here..." id="searchInput">
            </div>
            <div class="filter-dropdown">
                <button class="filter-btn"> All</button>
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

        <div class="notification-overlay" id="notificationOverlay" style="display: none;">
            <p style="font-size: 0.82rem; color: #4a1150; font-weight: 700; margin-bottom: 10px;">Latest Notices</p>
            <?php if (!empty($noticesForHeader)): ?>
                <?php foreach (array_slice($noticesForHeader, 0, 5) as $notice): ?>
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

        document.getElementById('searchInput').addEventListener('input', function(e) {
            const filter = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.notice-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(filter) ? 'block' : 'none';
            });
        });

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
