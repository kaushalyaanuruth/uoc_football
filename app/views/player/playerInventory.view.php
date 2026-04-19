<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Inventory - UOC Football</title>
    <?php
    $base = rtrim(ROOT, '/');
    $commonFile = __DIR__ . '/../../../public/assets/css/playerCommon.css';
    $commonVersion = file_exists($commonFile) ? filemtime($commonFile) : time();
    $cssFile = __DIR__ . '/../../../public/assets/css/playerInventory.css';
    $cssVersion = file_exists($cssFile) ? filemtime($cssFile) : time();
    $stats = $data['stats'] ?? ['total' => 0, 'in_use' => 0, 'available' => 0, 'damaged' => 0];
    $notices = is_array($data['notices'] ?? null) ? $data['notices'] : [];
    $noticeCount = count($notices);
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerCommon.css?v=<?php echo $commonVersion; ?>">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerInventory.css?v=<?php echo $cssVersion; ?>">
</head>
<body>
    <div class="dashboard-container player-inventory-page">
        <header class="player-header">
            <div class="logo-section">
                <img src="<?php echo $base; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo $base; ?>/PlayerDashboard">Home</a>
                <a href="<?php echo $base; ?>/Schedule">Schedule</a>
                <a href="<?php echo $base; ?>/Analyze">Analyze</a>
                <a href="<?php echo $base; ?>/Notices">Notices</a>
                <a href="<?php echo $base; ?>/MealPlan">Meal Plan</a>
                <a href="<?php echo $base; ?>/PlayerInventory" class="active">Inventory</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                    <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
                </div>
                <div class="user-profile">
                    <img src="<?php echo htmlspecialchars($data['player_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Player Profile">
                </div>
                <a class="player-logout-btn" href="<?php echo $base; ?>/login/logout">Logout</a>
            </div>
        </header>

        <section class="page-header">
            <div>
                <h1>Inventory Access</h1>
                <p>Take and return team items with live stock updates.</p>
            </div>
        </section>

        <?php if (!empty($data['error'])): ?>
            <section class="error-banner"><?php echo htmlspecialchars((string) $data['error']); ?></section>
        <?php endif; ?>

        <section class="stat-grid">
            <article class="stat-card">
                <h3>Total Items</h3>
                <p id="statTotal"><?php echo (int) ($stats['total'] ?? 0); ?></p>
            </article>
            <article class="stat-card success">
                <h3>Available</h3>
                <p id="statAvailable"><?php echo (int) ($stats['available'] ?? 0); ?></p>
            </article>
            <article class="stat-card warning">
                <h3>In Use</h3>
                <p id="statInUse"><?php echo (int) ($stats['in_use'] ?? 0); ?></p>
            </article>
            <article class="stat-card danger">
                <h3>Damaged</h3>
                <p id="statDamaged"><?php echo (int) ($stats['damaged'] ?? 0); ?></p>
            </article>
        </section>

        <section class="panel">
            <div class="panel-header">
                <h2>Available Inventory</h2>
            </div>
            <div class="table-wrap">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Total</th>
                            <th>Available</th>
                            <th>Status</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <?php foreach (($data['inventory'] ?? []) as $item): ?>
                            <tr data-item-id="<?php echo (int) ($item['item_id'] ?? 0); ?>">
                                <td><?php echo htmlspecialchars((string) ($item['item_name'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string) ($item['category'] ?? 'General')); ?></td>
                                <td><?php echo (int) ($item['total_count'] ?? 0); ?></td>
                                <td><?php echo (int) ($item['available_count'] ?? 0); ?></td>
                                <td><span class="status-badge <?php echo htmlspecialchars((string) ($item['status_key'] ?? 'available')); ?>"><?php echo htmlspecialchars((string) ($item['status'] ?? 'Available')); ?></span></td>
                                <td>
                                    <input class="qty-input" type="number" min="1" max="<?php echo max(1, (int) ($item['available_count'] ?? 0)); ?>" value="1" <?php echo !empty($item['can_take']) ? '' : 'disabled'; ?>>
                                </td>
                                <td>
                                    <button class="btn-take" <?php echo !empty($item['can_take']) ? '' : 'disabled'; ?>>Take</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="two-col">
            <section class="panel">
                <div class="panel-header">
                    <h2>Currently Borrowed</h2>
                </div>
                <div class="table-wrap compact">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Taken Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="openLogsTableBody">
                            <?php foreach (($data['open_logs'] ?? []) as $log): ?>
                                <tr data-log-id="<?php echo (int) ($log['log_id'] ?? 0); ?>">
                                    <td><?php echo htmlspecialchars((string) ($log['item_name'] ?? '')); ?></td>
                                    <td><?php echo (int) ($log['quantity'] ?? 0); ?> <?php echo htmlspecialchars((string) ($log['unit'] ?? 'pcs')); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($log['taken_date'] ?? '')); ?></td>
                                    <td><button class="btn-return">Return</button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <h2>Recent Returns</h2>
                </div>
                <div class="table-wrap compact">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Returned</th>
                            </tr>
                        </thead>
                        <tbody id="historyLogsTableBody">
                            <?php foreach (($data['history_logs'] ?? []) as $log): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string) ($log['item_name'] ?? '')); ?></td>
                                    <td><?php echo (int) ($log['quantity'] ?? 0); ?> <?php echo htmlspecialchars((string) ($log['unit'] ?? 'pcs')); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($log['return_date'] ?? '')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
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

    <div id="inventoryToast" class="toast"></div>

    <script>
        window.PLAYER_INVENTORY_CONFIG = {
            root: '<?php echo $base; ?>',
            pollMs: 15000
        };
        window.HEADER_PROFILE_MODAL_CONFIG = {
            fetchUrl: '<?php echo $base; ?>/PlayerDashboard/profileData',
            updateUrl: '<?php echo $base; ?>/PlayerDashboard/updateProfile',
            triggerSelector: '.user-profile'
        };

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
    <script src="<?php echo $base; ?>/assets/js/playerInventory.js" defer></script>
</body>
</html>
