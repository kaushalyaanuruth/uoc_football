<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400" rel="stylesheet">
    <title>UOC_football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/adminDashboard/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/header.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/page.css">
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
                <a class="action-card icon-purple" href="<?php echo ROOT; ?>/teamManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/teams.svg" alt="team icon" class="action-icon">
                    </div>
                    <p class="action-label">Teams</p>
                </a>
                <a class="action-card icon-blue" href="<?php echo ROOT; ?>/teamResult">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/test.svg" alt="results icon" class="action-icon">
                    </div>
                    <p class="action-label">Results</p>
                </a>
                <a class="action-card icon-green" href="<?php echo ROOT; ?>/eventManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/event.svg" alt="event icon" class="action-icon">
                    </div>
                    <p class="action-label">Events</p>
                </a>
                <a class="action-card icon-red" href="<?php echo ROOT; ?>/inventoryManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/inventory.svg" alt="inventory icon" class="action-icon">
                    </div>
                    <p class="action-label">Inventory</p>
                </a>
                <a class="action-card icon-pink" href="<?php echo ROOT; ?>/galleryManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/gallery.svg" alt="gallery icon" class="action-icon">
                    </div>
                    <p class="action-label">Gallery</p>
                </a>
                <a class="action-card icon-indigo" href="<?php echo ROOT; ?>/newsManagement">
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
                <a class="action-card icon-orange" href="<?php echo ROOT; ?>/budgetManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/budget.svg" alt="budget icon" class="action-icon">
                    </div>
                    <p class="action-label">Budget</p>
                </a>
            </div>
        </div>
        <div class="welcome-banner">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome back,</br> Admin!</h2>
                <div class="welcome-datetime">
                    <p class="date"><?php echo date('l, F j, Y'); ?></p>
                    <p class="time"><?php echo date('h:i A'); ?></p>
                </div>
            </div>
        </div>
        <div class="main-grid">
            <div class="notices card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Notices</h2>
                    </div>
                    <button class="add-btn" id="openNoticeModal">+ Add</button>
                </div>
                <div class="card-body">
                    <ul class="notices-list">
                        <?php if (!empty($data['notices'])): ?>
                            <?php foreach ($data['notices'] as $notice): ?>
                                <li class="notice-item">
                                    <h3 class="notice-title"><?php echo htmlspecialchars($notice->title ?? 'Notice'); ?></h3>
                                    <p class="notice-date"><?php echo date('F j, Y g:i A', strtotime($notice->created_at)); ?></p>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="notice-item">
                                <h3 class="notice-title">No notices yet</h3>
                                <p class="notice-date">Click + Add to create a notice</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="nextEvent card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>What is next?</h2>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="event-list">
                        <?php
                        $events = $data['upcomingEvents'] ?? [];
                        $eventIcons = [
                            'match' => 'match.svg',
                            'training' => 'training.svg',
                            'tournament' => 'event.svg',
                            'meeting' => 'event.svg',
                            'social' => 'event.svg',
                            'other' => 'event.svg',
                        ];
                        ?>

                        <?php if (!empty($events)): ?>
                            <?php foreach ($events as $event): ?>
                                <?php
                                $eventType = strtolower(trim((string)($event->event_type ?? 'other')));
                                $iconFile = $eventIcons[$eventType] ?? 'event.svg';

                                $dateRaw = $event->date ?? null;
                                $timeRaw = $event->event_time ?? null;
                                $formattedDate = 'Date not set';
                                if (!empty($dateRaw)) {
                                    $eventTs = strtotime((string)$dateRaw . (!empty($timeRaw) ? (' ' . (string)$timeRaw) : ''));
                                    if ($eventTs !== false) {
                                        $formattedDate = date('F j, Y g:i A', $eventTs);
                                    }
                                }
                                ?>
                                <li class="event-item">
                                    <div class="event-detail">
                                        <h3 class="event-title"><?php echo htmlspecialchars($event->title ?? 'UOC Football Event'); ?></h3>
                                        <p class="event-date"><?php echo htmlspecialchars($formattedDate); ?></p>
                                    </div>
                                    <div class="icon-container">
                                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/<?php echo htmlspecialchars($iconFile); ?>" alt="<?php echo htmlspecialchars($eventType); ?> icon" class="action-icon">
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="event-item">
                                <div class="event-detail">
                                    <h3 class="event-title">No upcoming events</h3>
                                    <p class="event-date">Add events from Event Management</p>
                                </div>
                                <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/event.svg" alt="event icon" class="action-icon">
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="inventory card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Inventory Status</h2>
                    </div>
                </div>
                <div class="inventory-list">
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <span class="material-symbols-outlined inventory-symbol" aria-hidden="true">checkroom</span>
                                </div>
                            <span class="inventory-name">Bibs</span>
                        </div>
                        <span class="inventory-stock stock-high">45 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <span class="material-symbols-outlined inventory-symbol" aria-hidden="true">sports_soccer</span>
                                </div>
                            <span class="inventory-name">Footballs</span>
                        </div>
                        <span class="inventory-stock stock-medium">12 in stock</span>
                    </div>

                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <span class="material-symbols-outlined inventory-symbol" aria-hidden="true">inventory_2</span>
                                </div>
                            <span class="inventory-name">Markers</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <span class="material-symbols-outlined inventory-symbol" aria-hidden="true">inventory_2</span>
                                </div>
                            <span class="inventory-name">Cones</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <span class="material-symbols-outlined inventory-symbol" aria-hidden="true">fitness_center</span>
                                </div>
                            <span class="inventory-name">Resistant band</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <span class="material-symbols-outlined inventory-symbol" aria-hidden="true">sports_bar</span>
                                </div>
                            <span class="inventory-name">Bottles</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>

                </div>
            </div>
        </div>    
    </div>

    <div id="noticeModal" style="display:none; position:fixed; inset:0; background:rgba(17,24,39,0.45); z-index:1200; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:14px; width:100%; max-width:520px; padding:18px; box-shadow:0 18px 36px rgba(17,24,39,0.25);">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <h3 style="margin:0; color:#4A1150;">Add Notice</h3>
                <button type="button" id="closeNoticeModal" style="border:none; background:transparent; font-size:22px; cursor:pointer;">&times;</button>
            </div>

            <form id="adminNoticeForm">
                <label for="noticeTitle" style="display:block; margin-bottom:6px; font-size:14px; color:#4b5563;">Title</label>
                <input id="noticeTitle" type="text" required style="width:100%; height:40px; border:1px solid #d1d5db; border-radius:10px; padding:0 12px; margin-bottom:12px;">

                <label for="noticeContent" style="display:block; margin-bottom:6px; font-size:14px; color:#4b5563;">Content</label>
                <textarea id="noticeContent" rows="5" required style="width:100%; border:1px solid #d1d5db; border-radius:10px; padding:10px 12px; margin-bottom:14px; resize:vertical;"></textarea>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" id="cancelNoticeBtn" style="height:38px; border:none; background:#e5e7eb; color:#374151; border-radius:10px; padding:0 14px; cursor:pointer;">Cancel</button>
                    <button type="submit" style="height:38px; border:none; background:#4A1150; color:#fff; border-radius:10px; padding:0 16px; cursor:pointer;">Save Notice</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const noticeModal = document.getElementById('noticeModal');
        const openNoticeModal = document.getElementById('openNoticeModal');
        const closeNoticeModal = document.getElementById('closeNoticeModal');
        const cancelNoticeBtn = document.getElementById('cancelNoticeBtn');
        const adminNoticeForm = document.getElementById('adminNoticeForm');

        function toggleNoticeModal(show) {
            noticeModal.style.display = show ? 'flex' : 'none';
        }

        openNoticeModal.addEventListener('click', () => toggleNoticeModal(true));
        closeNoticeModal.addEventListener('click', () => toggleNoticeModal(false));
        cancelNoticeBtn.addEventListener('click', () => toggleNoticeModal(false));
        noticeModal.addEventListener('click', (e) => {
            if (e.target === noticeModal) {
                toggleNoticeModal(false);
            }
        });

        adminNoticeForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const title = document.getElementById('noticeTitle').value.trim();
            const content = document.getElementById('noticeContent').value.trim();

            const response = await fetch('<?php echo ROOT; ?>/adminDashboard/addNotice', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, content })
            });

            const result = await response.json();
            if (!result.success) {
                alert(result.message || 'Failed to add notice');
                return;
            }

            window.location.reload();
        });
    </script>
</body>
</html>