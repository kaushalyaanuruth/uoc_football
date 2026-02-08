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
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo ROOT; ?>/uploads/players/notification.png" alt="Notifications"
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
                <input type="text" id="noticeSearch" placeholder="Search notices by title or content..."
                    onkeyup="filterNotices()">
            </div>
            <div class="filter-controls">
                <select class="category-select" id="categoryFilter" onchange="filterNotices()">
                    <option value="All">All Categories</option>
                    <option value="Training">Training</option>
                    <option value="Matches">Matches</option>
                    <option value="General">General</option>
                </select>
            </div>
        </div>

        <!-- Notices Grid -->
        <div class="notices-grid" id="noticesGrid">
            <?php foreach ($data['notices'] as $index => $notice): ?>
                <div class="notice-card" data-category="<?php echo $notice['category']; ?>"
                    style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <?php if ($notice['is_new']): ?>
                        <span class="new-badge">New</span>
                    <?php endif; ?>

                    <span class="card-category"><?php echo $notice['category']; ?></span>

                    <h4 class="notice-title">
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

        <!-- Notification Overlay -->
        <div class="notification-overlay" id="notificationOverlay" style="display: none;">
            <p style="font-size: 0.95rem; color: #333; font-weight: 600; margin-bottom: 12px;">Team Notifications</p>
            <div style="padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.05);">
                <p style="font-size: 0.85rem; color: #555; line-height: 1.5;">
                    Stay updated with the latest notices here. Important announcements will be highlighted.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Notification Toggle
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

        // Real-time Search and Filter Logic
        function filterNotices() {
            const searchQuery = document.getElementById('noticeSearch').value.toLowerCase();
            const categoryValue = document.getElementById('categoryFilter').value;
            const cards = document.querySelectorAll('.notice-card');

            cards.forEach(card => {
                const title = card.querySelector('.notice-title').textContent.toLowerCase();
                const content = card.querySelector('.notice-content').textContent.toLowerCase();
                const category = card.getAttribute('data-category');

                const matchesSearch = title.includes(searchQuery) || content.includes(searchQuery);
                const matchesCategory = categoryValue === 'All' || category === categoryValue;

                if (matchesSearch && matchesCategory) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>