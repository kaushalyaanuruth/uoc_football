<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captain Analyze Performance - UOC Football</title>
    <?php
    $base = rtrim(ROOT, '/');
    $cssFile = __DIR__ . '/../../../public/assets/css/analyze.css';
    $cssVersion = file_exists($cssFile) ? filemtime($cssFile) : time();
    $commonFile = __DIR__ . '/../../../public/assets/css/playerCommon.css';
    $commonVersion = file_exists($commonFile) ? filemtime($commonFile) : time();
    $trendLabels = $data['trends']['labels'] ?? [];
    $trendDatasets = $data['trends']['datasets'] ?? [];
    $testResults = $data['test_results'] ?? [];
    $matchHistory = $data['match_history'] ?? [];
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    $buildTrendPath = function ($values) {
        $points = is_array($values) ? array_values($values) : [];
        if (empty($points)) {
            return 'M50,180 L550,180';
        }

        $count = count($points);
        if ($count === 1) {
            $x = 50;
            $y = 180 - (max(0, min(100, (float) $points[0])) * 1.4);
            return 'M' . $x . ',' . round($y, 2) . ' L550,' . round($y, 2);
        }

        $path = '';
        for ($i = 0; $i < $count; $i++) {
            $x = 50 + (500 * $i / ($count - 1));
            $value = max(0, min(100, (float) $points[$i]));
            $y = 180 - ($value * 1.4);
            $path .= ($i === 0 ? 'M' : ' L') . round($x, 2) . ',' . round($y, 2);
        }

        return $path;
    };
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/analyze.css?v=<?php echo $cssVersion; ?>">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerCommon.css?v=<?php echo $commonVersion; ?>">
</head>
<body class="analyze-page captain-page">
    <div class="analyze-container">
        <header class="player-header analyze-header">
            <div class="logo-section">
                <img src="<?php echo $base; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>

            <nav class="nav-links">
                <a href="<?php echo $base; ?>/captainDashboard">Home</a>
                <a href="<?php echo $base; ?>/CaptainSchedule">Schedule</a>
                <a href="<?php echo $base; ?>/CaptainAnalyze" class="active">Analyze</a>
                <a href="<?php echo $base; ?>/CaptainAttendance">Attendance</a>
                <a href="<?php echo $base; ?>/CaptainInventory">Inventory</a>
                <a href="<?php echo $base; ?>/CaptainFinance">Finance</a>
                <a href="<?php echo $base; ?>/CaptainMealPlan">Meal Plan</a>
            </nav>

            <div class="user-section">
                <button class="notification-icon" id="notificationBell" type="button" aria-label="Show notifications">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications">
                    <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
                </button>
                <a class="user-profile" href="<?php echo $base; ?>/captainDashboard" title="Open profile">
                    <img src="<?php echo htmlspecialchars($data['captain_image'] ?? $data['player_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="User Profile">
                </a>
                <a class="player-logout-btn" href="<?php echo $base; ?>/login/logout">Logout</a>
            </div>
        </header>

        <main class="analyze-main">
            <section class="section-card overview-card">
                <h2 class="section-title">Performance Overview</h2>

                <div class="overview-grid">
                    <?php foreach ($data['performance'] as $item): ?>
                        <article class="overview-item">
                            <div class="progress-circle">
                                <svg width="110" height="110" viewBox="0 0 110 110" aria-hidden="true">
                                    <circle cx="55" cy="55" r="45" class="circle-bg"></circle>
                                    <circle
                                        cx="55"
                                        cy="55"
                                        r="45"
                                        class="circle-value"
                                        style="
                                            stroke: <?php echo $item['color']; ?>;
                                            stroke-dasharray: <?php echo (2 * pi() * 45); ?>;
                                            stroke-dashoffset: <?php echo (2 * pi() * 45 * (1 - $item['value'] / 100)); ?>;
                                        "
                                    ></circle>
                                </svg>
                                <span class="progress-value"><?php echo $item['value']; ?>%</span>
                            </div>
                            <h3><?php echo $item['label']; ?></h3>
                            <p><?php echo $item['status']; ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="mid-grid">
                <section class="section-card trends-card">
                    <h2 class="section-title">Fitness Test Trends</h2>

                    <div class="chart-shell">
                        <svg class="trend-svg" viewBox="0 0 600 200" aria-label="Performance trend chart">
                            <?php foreach ($trendDatasets as $set): ?>
                                <path
                                    d="<?php echo htmlspecialchars($buildTrendPath($set['data'] ?? [])); ?>"
                                    fill="none"
                                    stroke="<?php echo htmlspecialchars($set['color'] ?? '#a29bfe'); ?>"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                ></path>
                            <?php endforeach; ?>
                        </svg>

                        <div class="chart-labels">
                            <?php foreach ($trendLabels as $label): ?>
                                <span><?php echo $label; ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="chart-legend">
                            <?php foreach ($trendDatasets as $set): ?>
                                <span class="legend-item">
                                    <span class="legend-dot" style="background: <?php echo $set['color']; ?>;"></span>
                                    <?php echo $set['label']; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <section class="section-card comparison-card">
                    <h2 class="section-title">Monthly Comparison</h2>

                    <div class="comparison-list">
                        <?php foreach ($data['comparison'] as $comp): ?>
                            <article class="comp-item">
                                <div class="comp-left">
                                    <div class="comp-icon"><?php echo strtoupper(substr($comp['label'], 0, 1)); ?></div>
                                    <div class="comp-info">
                                        <h5><?php echo $comp['label']; ?></h5>
                                        <p>Progress vs Last Month</p>
                                    </div>
                                </div>
                                <div class="comp-right">
                                    <span class="percent <?php echo $comp['type'] === 'up' ? 'up' : 'down'; ?>"><?php echo $comp['value']; ?></span>
                                    <span class="sub"><?php echo $comp['sub']; ?></span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <section class="season-section">
                <h2 class="section-title">Season Statistics</h2>
                <div class="stats-grid">
                    <?php foreach ($data['stats'] as $stat): ?>
                        <article class="stat-card">
                            <div class="stat-icon" style="background: <?php echo $stat['color']; ?>;">
                                <?php echo $stat['icon']; ?>
                            </div>
                            <h3><?php echo $stat['value']; ?></h3>
                            <p><?php echo $stat['label']; ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="section-card personal-history-card">
                <h2 class="section-title">My Recent Test Results</h2>
                <div class="history-table-wrap">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Test Type</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($testResults)): ?>
                                <?php foreach ($testResults as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars((string) ($row->date ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars((string) ($row->test_type ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars((string) ($row->score ?? '')); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="history-empty">No personal test results found yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="section-card personal-history-card">
                <h2 class="section-title">My Match Performances</h2>
                <div class="history-table-wrap">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Opponent</th>
                                <th>Result</th>
                                <th>Min</th>
                                <th>G</th>
                                <th>A</th>
                                <th>Passes</th>
                                <th>Defense</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($matchHistory)): ?>
                                <?php foreach ($matchHistory as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars((string) ($row->date ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars((string) ($row->opponent_team ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars((string) ($row->result ?? '')); ?></td>
                                        <td><?php echo (int) ($row->minutes_played ?? 0); ?></td>
                                        <td><?php echo (int) ($row->goals_scored ?? 0); ?></td>
                                        <td><?php echo (int) ($row->assists ?? 0); ?></td>
                                        <td><?php echo (int) ($row->completed_passes ?? 0); ?></td>
                                        <td><?php echo (int) ($row->defensive_actions ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="history-empty">No personal match performance rows found yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <div class="notification-overlay" id="notificationOverlay" hidden>
            <p class="notify-title">Latest Notices</p>
            <?php foreach (array_slice($data['notices'] ?? [], 0, 5) as $notice): ?>
                <div class="notify-item" style="padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid #eee;">
                    <p style="font-size:0.8rem; color:#4a1150; font-weight:600; margin-bottom:4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                    <p style="font-size:0.82rem; color:#4b5563; line-height:1.35;"><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
                </div>
            <?php endforeach; ?>
            <?php if (empty($data['notices'])): ?>
                <p style="font-size:0.82rem; color:#6b7280;">No notices available.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        window.HEADER_PROFILE_MODAL_CONFIG = {
            fetchUrl: '<?php echo $base; ?>/captainDashboard/profileData',
            updateUrl: '<?php echo $base; ?>/captainDashboard/updateProfile',
            triggerSelector: '.user-profile'
        };

        const bell = document.getElementById('notificationBell');
        const overlay = document.getElementById('notificationOverlay');

        bell.addEventListener('click', function (e) {
            e.stopPropagation();
            overlay.hidden = !overlay.hidden;
        });

        document.addEventListener('click', function (e) {
            if (!overlay.contains(e.target) && !bell.contains(e.target)) {
                overlay.hidden = true;
            }
        });
    </script>
    <script src="<?php echo $base; ?>/assets/js/common/headerProfileModal.js"></script>
</body>
</html>
