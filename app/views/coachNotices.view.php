<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $base = rtrim(ROOT, '/');
    $notices = is_array($data['notices'] ?? null) ? $data['notices'] : [];
    $hasRealNotices = !empty($notices) && !(count($notices) === 1 && (($notices[0]['title'] ?? '') === 'No notices yet'));
    $noticeCount = $hasRealNotices ? count($notices) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
    <title>UOC_football - Notices</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/notices-style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/common.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/coachDashboard">
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </a>
            </div>
            <nav class="nav-menu">
                <a href="<?php echo ROOT; ?>/coachDashboard" class="nav-link">Home</a>
                <a href="<?php echo ROOT; ?>/coachEvents" class="nav-link">Events</a>
                <a href="<?php echo ROOT; ?>/coachMealPlan" class="nav-link">Meal Plan</a>
                <a href="<?php echo ROOT; ?>/coachPerformance" class="nav-link">Performance</a>
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link">Attendance</a>
                <a href="<?php echo ROOT; ?>/coachNotices" class="nav-link active">Notices</a>
            </nav>
            <div class="right-section">
                <div class="notification-icon" id="coachNotificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                    <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
                </div>
                <a class="user-profile" href="<?php echo $base; ?>/coachDashboard#profile" title="Profile">
                    <img src="<?php echo htmlspecialchars($data['coach_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Coach Avatar">
                </a>
                <a class="player-logout-btn" href="<?php echo $base; ?>/login/logout">Logout</a>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="search-filter-bar">
            <div class="search-box">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <input type="text" class="search-input" placeholder="Search here..." id="searchInput">
            </div>
            <div class="filter-actions">
                <button class="filter-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                </button>
                <button class="add-notice-btn">+ Add</button>
            </div>
        </div>

        <!-- Notices Grid -->
        <div class="notices-grid">
            <?php if ($hasRealNotices): ?>
                <?php foreach ($notices as $index => $notice): ?>
                    <div class="notice-card" data-notice-id="<?php echo (int) ($notice['id'] ?? 0); ?>">
                        <div class="notice-header">
                            <h3 class="notice-title"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></h3>
                            <?php if ($index < 3): ?>
                                <span class="new-badge">New</span>
                            <?php endif; ?>
                        </div>
                        <div class="notice-meta">
                            <div class="meta-item">
                                <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span><?php echo htmlspecialchars($notice['author'] ?? 'System'); ?></span>
                            </div>
                            <div class="meta-item">
                                <svg class="meta-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span><?php echo htmlspecialchars($notice['date'] ?? ''); ?></span>
                            </div>
                        </div>
                        <p class="notice-description"><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
                        <div class="notice-actions">
                            <button type="button" class="notice-action-btn notice-edit-btn">Edit</button>
                            <button type="button" class="notice-action-btn notice-delete-btn">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="notice-empty-state">
                    <h3>No notices yet</h3>
                    <p>Create a new notice to share updates with admin and your team.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="coachNoticeModal" class="notice-modal" style="display:none;">
        <div class="notice-modal-card">
            <div class="notice-modal-header">
                <h3 id="coachNoticeModalTitle">Add Notice</h3>
                <button type="button" id="closeCoachNoticeModal" class="notice-modal-close" aria-label="Close">&times;</button>
            </div>
            <form id="coachNoticeForm" class="notice-modal-form">
                <input id="coachNoticeId" name="notice_id" type="hidden" value="">
                <label for="coachNoticeTitle">Title</label>
                <input id="coachNoticeTitle" name="title" type="text" maxlength="255" required>

                <label for="coachNoticeContent">Content</label>
                <textarea id="coachNoticeContent" name="content" rows="5" required></textarea>

                <div class="notice-modal-actions">
                    <button type="button" id="cancelCoachNotice" class="notice-modal-cancel">Cancel</button>
                    <button type="submit" id="submitCoachNotice" class="notice-modal-submit">Post Notice</button>
                </div>
            </form>
        </div>
    </div>

    <div class="notification-overlay" id="coachNotificationOverlay" style="display: none;">
        <?php foreach (array_slice($data['notices'] ?? [], 0, 5) as $notice): ?>
            <div style="padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid #eee;">
                <p style="font-size:0.86rem; color:#4a1150; font-weight:600; margin-bottom:4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                <p style="font-size:0.82rem; color:#4b5563; line-height:1.35;"><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/notices-script.js"></script>
    <script>
        window.COACH_NOTICE_CONFIG = {
            addNoticeUrl: '<?php echo ROOT; ?>/coachNotices/addNotice',
            updateNoticeUrl: '<?php echo ROOT; ?>/coachNotices/updateNotice',
            deleteNoticeUrl: '<?php echo ROOT; ?>/coachNotices/deleteNotice'
        };
    </script>
    <script>
        window.HEADER_PROFILE_MODAL_CONFIG = {
            fetchUrl: '<?php echo $base; ?>/coachDashboard/profileData',
            updateUrl: '<?php echo $base; ?>/coachDashboard/updateProfile',
            triggerSelector: '.user-profile'
        };
    </script>
    <script src="<?php echo $base; ?>/assets/js/common/headerProfileModal.js"></script>
    <script>
        const coachBell = document.getElementById('coachNotificationBell');
        const coachOverlay = document.getElementById('coachNotificationOverlay');

        coachBell.addEventListener('click', (e) => {
            e.stopPropagation();
            coachOverlay.style.display = coachOverlay.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (
                coachOverlay.style.display === 'block' &&
                !coachOverlay.contains(e.target) &&
                !coachBell.contains(e.target)
            ) {
                coachOverlay.style.display = 'none';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                coachOverlay.style.display = 'none';
            }
        });
    </script>
</body>
</html>