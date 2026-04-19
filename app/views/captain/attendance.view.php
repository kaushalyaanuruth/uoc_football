<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    ?>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/attendance.css">

</head>

<body>


    <!-- ================= TOP NAVBAR ================= -->
    <header class="top-navbar">
        <div class="nav-left">
            <a href="<?php echo ROOT; ?>/captainDashboard">
                <img class="header-logo" src="<?php echo ROOT; ?>../assets/images/adminDashboard/header/uoclogo.png"
                    alt="UOC Football Logo">
            </a>
        </div>

        <nav class="nav-center">
            <a href="<?= ROOT ?>/captainDashboard">Home</a>
            <a href="<?= ROOT ?>/CaptainSchedule">Schedule</a>
            <a href="<?= ROOT ?>/CaptainAnalyze">Analyze</a>
            <a href="#" class="active">Attendance</a>
            <a href="<?= ROOT ?>/CaptainInventory">Inventory</a>
            <a href="<?= ROOT ?>/CaptainFinance">Finance</a>
            <a href="<?= ROOT ?>/CaptainMealPlan">Meal Plan</a>

        </nav>

        <div class="nav-right">
            <div class="notification-icon" id="captainNotificationBell">
                <img src="<?php echo ROOT; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
            </div>
            <a class="user-profile" href="<?= ROOT ?>/captainDashboard" title="Profile">
                <img src="<?php echo htmlspecialchars($data['captain_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Captain Avatar">
            </a>
            <a class="player-logout-btn" href="<?= ROOT ?>/login/logout">Logout</a>
        </div>
    </header>

    <main class="content">
        <input type="hidden" id="eventId" value="<?= $data['event_id'] ?>">
        <header class="page-header">
            <div>
                <h1>Attendance Dashboard</h1>
                <p>View and update player attendance for the selected session date</p>
            </div>

            <div class="filters">
                <select id="eventType">
                    <option <?= $data['selected_type'] == 'Practice' ? 'selected' : '' ?>>Practice</option>
                    <option <?= $data['selected_type'] == 'Match' ? 'selected' : '' ?>>Match</option>
                    <option <?= $data['selected_type'] == 'Training' ? 'selected' : '' ?>>Training</option>
                    <option <?= $data['selected_type'] == 'Fitness' ? 'selected' : '' ?>>Fitness</option>
                </select>
                <input type="date" id="attendanceDate" value="<?= htmlspecialchars($data['selected_date']) ?>" max="<?= date('Y-m-d') ?>" aria-label="Select attendance date">
                <select>
                    <option>All Teams</option>
                    <!-- <option value="">Team A</option>
                <option value="">Team B</option> -->
                </select>
                <button type="button" class="btn" id="applyFilters">Apply</button>
            </div>
        </header>

        <!-- Stats -->
        <section class="stats">
            <div class="stat-card">
                <h3>Total Players</h3>
                <span>
                    <span class="count"><?= $data['totalPlayers'] ?></span>
                    <div class="icon-container">
                        <img src="<?= ROOT ?>/assets/images/Captain/icons/teams.svg" class="action-icon">
                    </div>
                </span>

            </div>

            <div class="stat-card success">
                <h3>Present Players</h3>
                <span>
                    <span class="count"><?= $data['present'] ?></span>
                    <div class="icon-container">
                        <img src="<?= ROOT ?>/assets/images/Captain/icons/green-checkmark-icon.svg" class="action-icon">
                    </div>
                </span>
            </div>

            <div class="stat-card danger">
                <h3>Absent Players</h3>
                <span>
                    <span class="count"><?= $data['absent'] ?></span>
                    <div class="icon-container">
                        <img src="<?= ROOT ?>/assets/images/Captain/icons/red-x-icon.svg" class="action-icon">
                    </div>
                </span>
            </div>
        </section>

        <div class="dashboard-grid">

            <!-- Player Attendance -->
            <section class="attendance-section">
                <div class="section-header">
                    <h2>Player Attendance</h2>
                    <button class="btn ">Mark All Present</button>
                </div>

                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data['players'] as $player): ?>
                            <tr data-player-id="<?= $player->player_id ?>" data-original-status="<?= $player->status ?>">

                                <td>
                                    <strong><?= $player->name ?></strong><br>
                                    <small>#<?= $player->player_id ?></small>
                                </td>
                                <td><?= $player->position ?></td>
                                <td>
                                    <span class="status <?= strtolower($player->status) ?>">
                                        <?= $player->status ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <button class="icon success">✔</button>
                                    <button class="icon danger">✖</button>
                                    <button class="icon warning">🕒</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Right Panel -->
            <aside class="right-panel">

                <!-- Attendance Rate -->
                <section class="attendance-rate fitness-trends-card">
                    <h2>Attendance Rate</h2>

                    <div class="chart-container">
                        <svg class="chart-svg" viewBox="0 0 500 300" preserveAspectRatio="xMidYMid meet">
                            <!-- Grid lines -->
                            <line x1="50" y1="250" x2="370" y2="250" />
                            <line x1="50" y1="200" x2="370" y2="200" />
                            <line x1="50" y1="150" x2="370" y2="150" />
                            <line x1="50" y1="100" x2="370" y2="100" />
                            <line x1="50" y1="50" x2="370" y2="50" />

                            <!-- Y-axis labels -->
                            <text x="30" y="255">0</text>
                            <text x="30" y="205">25</text>
                            <text x="30" y="155">50</text>
                            <text x="30" y="105">75</text>
                            <text x="25" y="55">100</text>

                            <!-- X-axis labels -->
                            <text x="50" y="270">Mon</text>
                            <text x="100" y="270">Tue</text>
                            <text x="150" y="270">Wed</text>
                            <text x="200" y="270">Thu</text>
                            <text x="250" y="270">Fri</text>
                            <text x="300" y="270">Sat</text>
                            <text x="350" y="270">Sun</text>

                            <!-- Attendance line -->
                            <polyline points="50,80 100,70 150,95 200,60 250,75 300,74 350,71"
                                class="attendance-line" />

                            <!-- Data points -->
                            <circle cx="50" cy="80" r="4" />
                            <circle cx="100" cy="70" r="4" />
                            <circle cx="150" cy="95" r="4" />
                            <circle cx="200" cy="60" r="4" />
                            <circle cx="250" cy="75" r="4" />
                            <circle cx="300" cy="74" r="4" />
                            <circle cx="350" cy="71" r="4" />
                        </svg>
                    </div>
                </section>


                <section class="quick-actions">
                    <h2>Quick Actions</h2>
                    <div class="action-btns">
                        <button class="btn save-attendance">Save Attendance</button>
                        <button class="btn reset-changes">Reset Changes</button>
                        <button class="btn exportreport">Export Report</button>
                    </div>
                </section>

            </aside>
        </div>

    </main>

    <div id="captainNotificationOverlay" style="display:none; position:fixed; top:88px; right:32px; width:340px; max-height:420px; overflow:auto; background:#fff; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,0.18); padding:14px; z-index:1200; border:1px solid #ece7f3;">
        <?php foreach (array_slice($data['notices'] ?? [], 0, 5) as $notice): ?>
            <div style="padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid #eee;">
                <p style="font-size:0.86rem; color:#4a1150; font-weight:600; margin-bottom:4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                <p style="font-size:0.82rem; color:#4b5563; line-height:1.35;"><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        window.HEADER_PROFILE_MODAL_CONFIG = {
            fetchUrl: '<?= ROOT ?>/captainDashboard/profileData',
            updateUrl: '<?= ROOT ?>/captainDashboard/updateProfile',
            triggerSelector: '.user-profile'
        };

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

    <script src="<?= ROOT ?>/assets/js/common/headerProfileModal.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="<?= ROOT ?>/assets/js/captain/attendance.js"></script>
</body>

</html>