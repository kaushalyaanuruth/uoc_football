<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <?php
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    ?>

    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/Captaininventory.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <!-- ================= TOP NAVBAR ================= -->
    <header class="top-navbar">
        <div class="nav-left">
            <a href="<?php echo ROOT; ?>/captainDashboard">
                <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png"
                    alt="UOC Football Logo">
            </a>
        </div>

        <nav class="nav-center">
            <a href="<?= ROOT ?>/captainDashboard">Home</a>
            <a href="<?= ROOT ?>/CaptainSchedule">Schedule</a>
            <a href="<?= ROOT ?>/CaptainAnalyze">Analyze</a>
            <a href="<?= ROOT ?>/CaptainAttendance">Attendance</a>
            <a href="#" class="active">Inventory</a>
            <a href="<?= ROOT ?>/CaptainFinance">Finance</a>
        </nav>

        <div class="nav-right">
            <a class="player-logout-btn" href="<?= ROOT ?>/login/logout">Logout</a>
            <div class="notification-icon" id="captainNotificationBell">
                <img src="<?php echo ROOT; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
            </div>
            <a class="user-profile" href="<?= ROOT ?>/captainDashboard" title="Profile">
                <img src="<?php echo htmlspecialchars($data['captain_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Captain Avatar">
            </a>
        </div>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="content">

        <!-- Page Header -->
        <header class="page-header">
            <div>
                <h1>Inventory Management</h1>
                <p>Manage team equipment and supplies</p>
            </div>
        </header>

        <!-- ================= STATS ================= -->
        <section class="stats">
            <div class="stat-card">
                <h3>Total Items</h3>
                <div class="stat-value">
                    <span><?= $data['total'] ?></span>
                    <div class="stat-icon purple"></div>
                </div>
            </div>
            <div class="stat-card warning">
                <h3>Items In Use</h3>
                <div class="stat-value">
                    <span><?= $data['in_use'] ?></span>
                    <div class="stat-icon orange"></div>
                </div>
            </div>

            <div class="stat-card success">
                <h3>Available Items</h3>
                <div class="stat-value">

                    <span>
                        <?= $data['available'] ?></span>
                    <div class="stat-icon green"></div>

                </div>
            </div>

            <div class="stat-card danger">
                <h3>Damaged Items</h3>
                <div class="stat-value">

                    <span>
                        <?= $data['damaged'] ?> </span>
                    <div class="stat-icon red"></div>

                </div>
            </div>
        </section>


        <!-- ================= CHARTS ================= -->
        <div class="dashboard-grid">
            <section class="chart-card">
                <h2>Equipment Usage</h2>
                <canvas id="equipmentUsageChart"></canvas>
            </section>

            <section class="chart-card">
                <h2>Item Status Distribution</h2>
                <canvas id="statusDistributionChart"></canvas>
            </section>
        </div>

        <!-- ================= INVENTORY TABLE ================= -->
        <section class="inventory-section">
            <div class="section-header">
                <h2>Inventory Items
                    <button class="btn-add" id="addItem">Add New Item</button>
                </h2>
                <select id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="kits">Kits</option>
                    <option value="balls">Balls</option>
                    <option value="equipment">Equipment</option>
                    <option value="accessories">Accessories</option>
                </select>

            </div>

            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($data['inventory'] as $item): ?>
                        <tr data-id="<?= $item->item_id ?>">
                            <td><?= $item->item_name ?></td>
                            <td><?= $item->category ?></td>
                            <td><?= $item->total_count ?></td>
                            <td>
                                <span class="status <?= strtolower(str_replace(' ', '', $item->status)) ?>">
                                    <?= $item->status ?>
                                </span>
                            </td>
                            <td><?= $item->last_updated ?></td>
                            <td class="actions">
                                <button class="btn-edit">Edit</button>
                                <button class="btn-delete">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <!-- ================= EDIT INVENTORY MODAL ================= -->
        <div class="modal" id="inventoryModal">
            <div class="modal-content">
                <div class="modal-header">

                    <span>Edit Inventory Item</span>
                    <span id="close">×</span>
                </div>

                <form id="inventoryForm" method="POST" action="<?= ROOT ?>/CaptainInventory/store"> <input type="hidden"
                        id="item_id">

                    <label>Item Name</label>
                    <input type="text" id="item_name" name="item_name" required>

                    <label>Category</label>
                    <select id="category" name="category">
                        <option>Kits</option>
                        <option>Balls</option>
                        <option>Equipment</option>
                        <option>Accessories</option>
                    </select>

                    <label>Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="0">

                    <label>Status</label>
                    <select id="status" name="status">
                        <option>Available</option>
                        <option>In Use</option>
                        <option>Damaged</option>
                    </select>

                    <div class="modal-actions">
                        <button type="button" id="closeModal">Cancel</button>
                        <button type="submit" class="save">Save</button>
                    </div>
                </form>
            </div>

        </div>
        <div id="toast"></div>
        <!-- SUCCESS MODAL -->
        <div class="modal" id="successModal">
            <div class="modal-content" style="width:300px;text-align:center">
                <h3>Success</h3>
                <p id="centerToastMessage">User details saved successfully!</p>
                <button class="save" id="closeSuccess">OK</button>
            </div>
        </div>

        <div id="captainNotificationOverlay" style="display:none; position:fixed; top:88px; right:32px; width:340px; max-height:420px; overflow:auto; background:#fff; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,0.18); padding:14px; z-index:1200; border:1px solid #ece7f3;">
            <?php foreach (array_slice($data['notices'] ?? [], 0, 5) as $notice): ?>
                <div style="padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid #eee;">
                    <p style="font-size:0.86rem; color:#4a1150; font-weight:600; margin-bottom:4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                    <p style="font-size:0.82rem; color:#4b5563; line-height:1.35;"><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

    </main>

    <script>
        const captainBell = document.getElementById('captainNotificationBell');
        const captainOverlay = document.getElementById('captainNotificationOverlay');

        captainBell.addEventListener('click', (e) => {
            e.stopPropagation();
            captainOverlay.style.display = captainOverlay.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!captainOverlay.contains(e.target) && e.target !== captainBell && !captainBell.contains(e.target)) {
                captainOverlay.style.display = 'none';
            }
        });
    </script>

    <script src="<?= ROOT ?>/assets/js/captain/Captaininventory.js" defer></script>
</body>

</html>