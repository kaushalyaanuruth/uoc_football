<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
    <title>UOC_football - Attendance</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/attendance-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/coach">
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/uoclogo.png" alt="UOC Football Logo">
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
                <img class="avatar" src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/avatar.jpg" alt="Coach Avatar">
                <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-group">
                <label>Select Player</label>
                <select class="filter-select" id="playerFilter">
                    <option value="all">All Players</option>
                    <option value="john">John Smith</option>
                    <option value="mike">Mike Johnson</option>
                    <option value="alex">Alex Brown</option>
                    <option value="david">David Wilson</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Type</label>
                <select class="filter-select" id="typeFilter">
                    <option value="first">First Team</option>
                    <option value="second">Second Team</option>
                    <option value="youth">Youth Team</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Date Range</label>
                <input type="date" class="filter-input" id="dateFilter" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="filter-group">
                <label>Session</label>
                <select class="filter-select" id="sessionFilter">
                    <option value="all">All Sessions</option>
                    <option value="morning">Morning</option>
                    <option value="evening">Evening</option>
                </select>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
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
                    <h3 class="stat-value">92.5<span class="stat-unit">%</span></h3>
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
                    <h3 class="stat-value">148</h3>
                </div>
            </div>

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
                    <p class="stat-label">Team</p>
                    <h3 class="stat-value team-name">John<span class="team-sub">95 min</span></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon lowest">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Lowest Attendance</p>
                    <h3 class="stat-value team-name">Alex Brown<span class="team-sub">78.3%</span></h3>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="main-content-grid">
            <!-- Attendance Records -->
            <div class="attendance-records card">
                <div class="card-header">
                    <h2 class="card-title">Attendance Records</h2>
                    <button class="mark-btn">+ Mark</button>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Session</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="player-info">
                                            <img src="<?php echo ROOT; ?>/assets/images/coachDashboard/players/player1.jpg" alt="John Smith" class="player-avatar">
                                            <span>John Smith</span>
                                        </div>
                                    </td>
                                    <td>2024-01-15</td>
                                    <td>Training</td>
                                    <td><span class="status-badge present">Present</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="player-info">
                                            <img src="<?php echo ROOT; ?>/assets/images/coachDashboard/players/player2.jpg" alt="Mike Johnson" class="player-avatar">
                                            <span>Mike Johnson</span>
                                        </div>
                                    </td>
                                    <td>2024-01-15</td>
                                    <td>Training</td>
                                    <td><span class="status-badge absent">Absent</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="player-info">
                                            <img src="<?php echo ROOT; ?>/assets/images/coachDashboard/players/player3.jpg" alt="Alex Brown" class="player-avatar">
                                            <span>Alex Brown</span>
                                        </div>
                                    </td>
                                    <td>2024-01-14</td>
                                    <td>Training</td>
                                    <td><span class="status-badge late">Late</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="player-info">
                                            <img src="<?php echo ROOT; ?>/assets/images/coachDashboard/players/player4.jpg" alt="David Wilson" class="player-avatar">
                                            <span>David Wilson</span>
                                        </div>
                                    </td>
                                    <td>2024-01-14</td>
                                    <td>Training</td>
                                    <td><span class="status-badge excused">Excused</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Coach Notes -->
            <div class="coach-notes card">
                <div class="card-header">
                    <h2 class="card-title">Coach Notes</h2>
                    <button class="add-note-btn">+ Add Note</button>
                </div>
                <div class="card-body">
                    <ul class="notes-list">
                        <li class="note-item">
                            <div class="note-header">
                                <span class="note-player">Mike Johnson</span>
                                <span class="note-date">Jan 15, 2024</span>
                            </div>
                            <p class="note-text">Missed 2 consecutive training sessions. Need to check if everything is okay.</p>
                        </li>
                        <li class="note-item">
                            <div class="note-header">
                                <span class="note-player">Alex Brown</span>
                                <span class="note-date">Jan 14, 2024</span>
                            </div>
                            <p class="note-text">Came late due to car trouble. Showed commitment by staying extra.</p>
                        </li>
                        <li class="note-item">
                            <div class="note-header">
                                <span class="note-player">David Wilson</span>
                                <span class="note-date">Jan 12, 2024</span>
                            </div>
                            <p class="note-text">Family emergency - excused absence. Will catch up next week.</p>
                        </li>
                    </ul>
                    <button class="load-more-btn">Load more...</button>
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

    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/attendance-script.js"></script>
</body>
</html>