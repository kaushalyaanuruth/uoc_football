<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
    <title>UOC_football - Player Performance</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/performance-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Header -->
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
                <a href="<?php echo ROOT; ?>/coachPerformance" class="nav-link active">Performance</a>
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link">Attendance</a>
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
                <label>Analysis Type</label>
                <select class="filter-select" id="analysisType">
                    <option value="team">Team Analysis</option>
                    <option value="player" selected>Player Analysis</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Match</label>
                <select class="filter-select" id="matchSelect">
                    <option value="uc-arsenal">Vs Arsenal FC</option>
                    <option value="uc-chelsea">Vs Chelsea FC</option>
                    <option value="uc-liverpool">Vs Liverpool FC</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Start Date</label>
                <input type="date" class="filter-input" id="startDate" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="filter-group">
                <label>End Date</label>
                <input type="date" class="filter-input" id="endDate" value="<?php echo date('Y-m-d'); ?>">
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
                    <div class="stat-value">92M</div>
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
                    <div class="stat-value">23</div>
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
                    <div class="stat-value">68%</div>
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
                    <div class="stat-value">62%</div>
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
                    <div class="stat-value">84%</div>
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
                    <div class="stat-value">18/2</div>
                    <div class="stat-label">Tackles/Fouls</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <!-- Performance Trend Chart -->
            <div class="chart-card">
                <div class="card-header">
                    <h2>Performance Trend</h2>
                </div>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Match Stats Comparison Chart -->
            <div class="chart-card">
                <div class="card-header">
                    <h2>Match Stats Comparison</h2>
                </div>
                <div class="chart-container">
                    <canvas id="comparisonChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Match Outcomes Pie Chart -->
        <div class="pie-chart-section">
            <div class="chart-card wide">
                <div class="card-header">
                    <h2>Match Outcomes</h2>
                </div>
                <div class="chart-container pie-container">
                    <canvas id="outcomeChart"></canvas>
                    <div class="pie-legend">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #10b981;"></span>
                            <span class="legend-label">Wins</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #f59e0b;"></span>
                            <span class="legend-label">Draws</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #ef4444;"></span>
                            <span class="legend-label">Losses</span>
                        </div>
                    </div>
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
                        <option value="player1">Select Player 1</option>
                        <option value="john">John Doe</option>
                        <option value="jane">Jane Smith</option>
                    </select>
                    <select class="player-select" id="player2">
                        <option value="player2">Select Player 2</option>
                        <option value="mike">Mike Johnson</option>
                        <option value="sarah">Sarah Williams</option>
                    </select>
                </div>
                <div class="comparison-metrics">
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);">
                            <span>G</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value">8</div>
                            <div class="metric-label">Goals</div>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);">
                            <span>12</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value">12</div>
                            <div class="metric-label">Assists</div>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                            <span>92%</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value">92%</div>
                            <div class="metric-label">Pass Accuracy</div>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                            <span>86%</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value">86%</div>
                            <div class="metric-label">Stamina</div>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);">
                            <span>A+</span>
                        </div>
                        <div class="metric-info">
                            <div class="metric-value">A+</div>
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
                            <tr>
                                <td>
                                    <div class="player-cell">
                                        <img src="<?php echo ROOT; ?>/assets/images/players/avatar1.jpg" alt="Player" class="player-avatar">
                                        <span>Marcus Johnson</span>
                                    </div>
                                </td>
                                <td>90</td>
                                <td>2</td>
                                <td>1</td>
                                <td>45/52</td>
                                <td>6</td>
                                <td>8</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="player-cell">
                                        <img src="<?php echo ROOT; ?>/assets/images/players/avatar2.jpg" alt="Player" class="player-avatar">
                                        <span>David Wilson</span>
                                    </div>
                                </td>
                                <td>85</td>
                                <td>1</td>
                                <td>3</td>
                                <td>38/42</td>
                                <td>4</td>
                                <td>12</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="player-cell">
                                        <img src="<?php echo ROOT; ?>/assets/images/players/avatar3.jpg" alt="Player" class="player-avatar">
                                        <span>Alex Rodriguez</span>
                                    </div>
                                </td>
                                <td>90</td>
                                <td>0</td>
                                <td>2</td>
                                <td>52/58</td>
                                <td>2</td>
                                <td>15</td>
                            </tr>
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

    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/performance-script.js"></script>
</body>
</html>