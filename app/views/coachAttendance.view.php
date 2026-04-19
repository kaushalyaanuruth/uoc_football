<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $base = rtrim(ROOT, '/');
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
    <title>UOC_football - Attendance</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/attendance-style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/common.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <input type="hidden" id="eventId" value="<?php echo (int) ($data['event_id'] ?? 0); ?>">
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
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link active">Attendance</a>
                <a href="<?php echo ROOT; ?>/coachNotices" class="nav-link">Notices</a>
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
        

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon team">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="23" y1="21" x2="23" y2="15"></line>
                        <line x1="20" y1="18" x2="26" y2="18"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Season</p>
                    <h3 class="stat-value team-name" id="seasonStatValue"><?php echo htmlspecialchars($data['season'] ?? 'N/A'); ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon overall">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Overall Attendance</p>
                    <h3 class="stat-value"><span id="overallAttendanceValue"><?php echo htmlspecialchars(number_format((float) ($data['overall_attendance'] ?? 0), 1)); ?></span><span class="stat-unit">%</span></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon sessions">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Total Sessions</p>
                    <h3 class="stat-value" id="totalSessionsValue"><?php echo (int) ($data['total_sessions'] ?? 0); ?></h3>
                </div>
            </div>
        </div>

        <div class="stats-grid" style="margin-top:-8px;">
            <div class="stat-card">
                <div class="stat-icon overall">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Total Players</p>
                    <h3 class="stat-value" id="totalPlayersValue"><?php echo (int) ($data['totalPlayers'] ?? 0); ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon team">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Present</p>
                    <h3 class="stat-value" id="presentPlayersValue"><?php echo (int) ($data['present'] ?? 0); ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon lowest">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Absent</p>
                    <h3 class="stat-value" id="absentPlayersValue"><?php echo (int) ($data['absent'] ?? 0); ?></h3>
                </div>
            </div>
        </div>


        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-group">
                <label>Select Player</label>
                <select class="filter-select" id="playerFilter">
                    <option value="all">All Players</option>
                    <?php foreach (($data['player_options'] ?? []) as $playerOption): ?>
                        <option value="<?php echo (int) ($playerOption['id'] ?? 0); ?>"><?php echo htmlspecialchars($playerOption['name'] ?? 'Player'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Session Type</label>
                <select class="filter-select" id="seasonFilter">
                    <?php $selectedType = (string) ($data['selected_type'] ?? 'Practice'); ?>
                    <option value="Practice" <?php echo $selectedType === 'Practice' ? 'selected' : ''; ?>>Practice</option>
                    <option value="Match" <?php echo $selectedType === 'Match' ? 'selected' : ''; ?>>Match</option>
                    <option value="Training" <?php echo $selectedType === 'Training' ? 'selected' : ''; ?>>Training</option>
                    <option value="Fitness" <?php echo $selectedType === 'Fitness' ? 'selected' : ''; ?>>Fitness</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Attendance Date</label>
                <input type="date" class="filter-input" id="dateFilter" value="<?php echo htmlspecialchars($data['selected_date'] ?? date('Y-m-d')); ?>" max="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        

        <!-- Main Content Grid -->
        <div class="main-content-grid">
            <!-- Attendance Records -->
            <div class="attendance-records card">
                <div class="card-header">
                    <h2 class="card-title">Attendance Records</h2>
                    <div class="card-actions">
                        <button class="mark-btn" id="markAllPresentBtn" type="button">Mark All Present</button>
                        <button class="save-attendance" type="button">Save Attendance</button>
                        <button class="reset-changes" type="button">Reset Changes</button>
                        <button class="exportreport" type="button">Export Report</button>
                        <button class="mark-btn" id="applyFiltersBtn" type="button">Apply Filters</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['players'])): ?>
                                    <?php foreach ($data['players'] as $player): ?>
                                        <?php
                                            $playerName = (string) ($player->name ?? 'Player');
                                            $playerStatus = (string) ($player->status ?? 'Absent');
                                            $statusClass = strtolower($playerStatus);
                                            $hasAvatar = !empty($player->image);
                                            $avatar = $hasAvatar
                                                ? (ROOT . '/' . ltrim(str_replace('\\', '/', (string) $player->image), '/'))
                                                : '';
                                            $avatarInitial = strtoupper(substr(trim($playerName), 0, 1));
                                            if ($avatarInitial === '') {
                                                $avatarInitial = 'P';
                                            }
                                        ?>
                                        <tr data-player-id="<?php echo (int) ($player->player_id ?? 0); ?>" data-player-name="<?php echo htmlspecialchars($playerName); ?>" data-original-status="<?php echo htmlspecialchars($playerStatus); ?>">
                                            <td>
                                                <div class="player-info">
                                                    <?php if ($hasAvatar): ?>
                                                        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="<?php echo htmlspecialchars($playerName); ?>" class="player-avatar">
                                                    <?php else: ?>
                                                        <div class="player-avatar player-avatar-empty" aria-label="No profile image"><?php echo htmlspecialchars($avatarInitial); ?></div>
                                                    <?php endif; ?>
                                                    <span><?php echo htmlspecialchars($playerName); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($player->position ?? '-'); ?></td>
                                            <td><span class="status-badge <?php echo htmlspecialchars($statusClass); ?>"><?php echo htmlspecialchars($playerStatus); ?></span></td>
                                            <td>
                                                <div class="status-actions">
                                                    <button type="button" class="status-action-btn present" data-status="Present" title="Mark Present">P</button>
                                                    <button type="button" class="status-action-btn absent" data-status="Absent" title="Mark Absent">A</button>
                                                    <button type="button" class="status-action-btn late" data-status="Late" title="Mark Late">L</button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No team players found for attendance.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="player-history card" id="playerHistoryCard" style="display:none;">
                <div class="card-header">
                    <h2 class="card-title" id="playerHistoryTitle">Attendance History</h2>
                </div>
                <div class="card-body">
                    <div id="playerHistoryStatus" style="font-size:14px; color:#6b7280; margin-bottom:10px;"></div>
                    <div class="table-wrapper">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Session</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="playerHistoryBody">
                                <tr>
                                    <td colspan="4" style="text-align:center; color:#6b7280;">Select a player to view full attendance history.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <!-- Attendance Trend Chart -->
            <div class="chart-card card">
                <div class="card-header">
                    <h2 class="card-title">Attendance Trend</h2>
                </div>
                <div class="card-body">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>

            <!-- Attendance Distribution Chart -->
            <div class="chart-card card">
                <div class="card-header">
                    <h2 class="card-title">Attendance Distribution</h2>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="attendanceDistributionChart"></canvas>
                        <div class="distribution-legend">
                            <div class="legend-item">
                                <span class="legend-color" style="background: #10b981;"></span>
                                <span class="legend-label">Present</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #ef4444;"></span>
                                <span class="legend-label">Absent</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #f59e0b;"></span>
                                <span class="legend-label">Late</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #3b82f6;"></span>
                                <span class="legend-label">Excused</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

    <script>
        window.COACH_ATTENDANCE_CONFIG = {
            baseUrl: "<?php echo ROOT; ?>/coachAttendance",
            historyUrl: "<?php echo ROOT; ?>/coachAttendance/playerHistory",
            selectedDate: "<?php echo htmlspecialchars($data['selected_date'] ?? date('Y-m-d')); ?>",
            selectedType: "<?php echo htmlspecialchars($data['selected_type'] ?? 'Practice'); ?>",
            trendLabels: <?php echo json_encode($data['trend_labels'] ?? ['No Data']); ?>,
            trendValues: <?php echo json_encode($data['trend_values'] ?? [0]); ?>,
            distributionLabels: <?php echo json_encode($data['distribution_labels'] ?? ['Present', 'Absent', 'Late', 'Excused']); ?>,
            distributionValues: <?php echo json_encode($data['distribution_values'] ?? [0, 0, 0, 0]); ?>
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
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/attendance-script.js"></script>
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