<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $base = rtrim(ROOT, '/');
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    $performancePayload = $data['performance_payload'] ?? [];
    $summary = $performancePayload['summary'] ?? ['wins' => 0, 'losses' => 0, 'draws' => 0];
    $stats = $performancePayload['stats'] ?? [
        'goals_scored' => 0,
        'fouls' => 0,
        'passing_target' => 0,
        'possession' => 0,
        'pass_accuracy' => 0,
        'tackles_fouls' => '0/0',
    ];
    $matchOptions = $performancePayload['match_options'] ?? [];
    $playerOptions = $performancePayload['player_options'] ?? [];
    $tableRows = $performancePayload['table_rows'] ?? [];
    $comparisonRows = $performancePayload['comparison_rows'] ?? [];
    $selectedMatchId = (int) ($performancePayload['selected_match_id'] ?? 0);
    $selectedPlayerId = (int) ($performancePayload['selected_player_id'] ?? 0);
    $trend = $performancePayload['trend'] ?? ['labels' => [], 'datasets' => []];
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
    <title>UOC_football - Player Performance</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/performance-style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/common.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
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
                <a href="<?php echo ROOT; ?>/coachPerformance" class="nav-link active">Performance</a>
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link">Attendance</a>
                <a href="<?php echo ROOT; ?>/coachNotices" class="nav-link">Notices</a>
            </nav>
            <div class="right-section">
                <a class="player-logout-btn" href="<?php echo $base; ?>/login/logout">Logout</a>
                <div class="notification-icon" id="coachNotificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                    <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
                </div>
                <a class="user-profile" href="<?php echo $base; ?>/coachDashboard#profile" title="Profile">
                    <img src="<?php echo htmlspecialchars($data['coach_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Coach Avatar">
                </a>
            </div>
        </div>

       <!-- Match Result Summary -->
        <div class="match-result-row">
            <div class="match-result-card wins">
                <div class="match-result-value"><?php echo (int) ($summary['wins'] ?? 0); ?></div>
                <div class="match-result-label">Won Matches</div>
            </div>
            <div class="match-result-card losses">
                <div class="match-result-value"><?php echo (int) ($summary['losses'] ?? 0); ?></div>
                <div class="match-result-label">Lost Matches</div>
            </div>
            <div class="match-result-card draws">
                <div class="match-result-value"><?php echo (int) ($summary['draws'] ?? 0); ?></div>
                <div class="match-result-label">Draw Matches</div>
            </div>
        </div>

        
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-group">
                <label>Match</label>
                <select class="filter-select" id="matchSelect">
                    <option value="0">All Matches</option>
                    <?php foreach ($matchOptions as $match): ?>
                        <?php $matchId = (int) ($match->result_id ?? 0); ?>
                        <option value="<?php echo $matchId; ?>" <?php echo $matchId === $selectedMatchId ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars((string) ($match->opponent_team ?? 'Opponent')); ?>
                            <?php if (!empty($match->date)): ?>
                                - <?php echo htmlspecialchars(date('M d, Y', strtotime((string) $match->date))); ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Player</label>
                <select class="filter-select" id="playerFilter">
                    <option value="0">All Players</option>
                    <?php foreach ($playerOptions as $player): ?>
                        <?php $playerId = (int) ($player->player_id ?? 0); ?>
                        <option value="<?php echo $playerId; ?>" <?php echo $playerId === $selectedPlayerId ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(trim((string) ($player->player_name ?? 'Player'))); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

 
        <!-- Stats Cards Row -->
        <div class="stats-row">
            <div class="stat-card green">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo (int) ($stats['goals_scored'] ?? 0); ?></div>
                    <div class="stat-label">Goals Scored</div>
                </div>
            </div>

            <div class="stat-card red">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo (int) ($stats['fouls'] ?? 0); ?></div>
                    <div class="stat-label">Fouls/Faults</div>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo (int) ($stats['passing_target'] ?? 0); ?>%</div>
                    <div class="stat-label">Passing Target</div>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                        <line x1="7" y1="7" x2="7.01" y2="7"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo (int) ($stats['possession'] ?? 0); ?>%</div>
                    <div class="stat-label">Possession</div>
                </div>
            </div>

            <div class="stat-card indigo">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo (int) ($stats['pass_accuracy'] ?? 0); ?>%</div>
                    <div class="stat-label">Pass Accuracy</div>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 20V10M12 20V4M6 20v-6"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo htmlspecialchars((string) ($stats['tackles_fouls'] ?? '0/0')); ?></div>
                    <div class="stat-label">Tackles/Fouls</div>
                </div>
            </div>
        </div>

        
        <!-- Player Comparison Section -->
        <div class="comparison-section">
            <div class="comparison-card">
                <div class="card-header">
                    <h2>Player Comparison</h2>
                </div>
                <div class="comparison-selects">
                    <select class="player-select" id="player1">
                        <option value="">Select Player 1</option>
                        <?php foreach ($comparisonRows as $row): ?>
                            <option value="<?php echo (int) ($row->player_id ?? 0); ?>"><?php echo htmlspecialchars(trim((string) ($row->player_name ?? 'Player'))); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="player-select" id="player2">
                        <option value="">Select Player 2</option>
                        <?php foreach ($comparisonRows as $row): ?>
                            <option value="<?php echo (int) ($row->player_id ?? 0); ?>"><?php echo htmlspecialchars(trim((string) ($row->player_name ?? 'Player'))); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="comparison-metrics">
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);">
                            <span>G</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value" id="cmpGoals">0 / 0</div>
                            <div class="metric-label">Goals</div>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);">
                            <span>A</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value" id="cmpAssists">0 / 0</div>
                            <div class="metric-label">Assists</div>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                            <span>%</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value" id="cmpPassAccuracy">0% / 0%</div>
                            <div class="metric-label">Pass Accuracy</div>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                            <span>S</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value" id="cmpStamina">0% / 0%</div>
                            <div class="metric-label">Stamina</div>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);">
                            <span>R</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value" id="cmpOverall">0 / 0</div>
                            <div class="metric-label">Overall Rating</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Detailed Match Breakdown Table -->
        <div class="table-section">
            <div class="table-card">
                <div class="card-header">
                    <h2>Detailed Match Breakdown</h2>
                </div>
                <div class="table-container">
                    <table class="performance-table">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Minutes</th>
                                <th>Goals</th>
                                <th>Assists</th>
                                <th>Passes</th>
                                <th>Shots</th>
                                <th>Defense</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tableRows)): ?>
                                <?php foreach ($tableRows as $row): ?>
                                    <?php
                                    $name = trim((string) ($row->player_name ?? 'Player'));
                                    $avatar = !empty($row->player_image)
                                        ? (ROOT . '/' . ltrim(str_replace('\\', '/', (string) $row->player_image), '/'))
                                        : '';
                                    $initial = strtoupper(substr($name !== '' ? $name : 'P', 0, 1));
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="player-cell">
                                                <?php if ($avatar !== ''): ?>
                                                    <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Player" class="player-avatar">
                                                <?php else: ?>
                                                    <span class="player-avatar-fallback"><?php echo htmlspecialchars($initial); ?></span>
                                                <?php endif; ?>
                                                <span><?php echo htmlspecialchars(trim((string) ($row->player_name ?? 'Player'))); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo (int) ($row->minutes_played ?? 0); ?></td>
                                        <td><?php echo (int) ($row->goals ?? 0); ?></td>
                                        <td><?php echo (int) ($row->assists ?? 0); ?></td>
                                        <td><?php echo (int) ($row->passes ?? 0); ?></td>
                                        <td><?php echo (int) ($row->shots ?? 0); ?></td>
                                        <td><?php echo (int) ($row->defense ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; color:#6b7280;">No player performance records found for the selected filters.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Coach Notes Section -->
        <div class="notes-section">
            <div class="notes-card">
                <div class="card-header">
                    <h2>Coach Notes & Insights</h2>
                    <button class="save-btn">Save Notes</button>
                </div>
                <div class="notes-content">
                    <textarea class="notes-textarea" placeholder="Add your notes, strategy improvements, or quick feedback..."></textarea>
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
        window.COACH_PERFORMANCE_DATA = <?php echo json_encode([
            'trend' => $trend,
            'comparisonRows' => $comparisonRows,
            'selected_match_id' => $selectedMatchId,
            'selected_player_id' => $selectedPlayerId,
        ]); ?>;
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/performance-script.js"></script>
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