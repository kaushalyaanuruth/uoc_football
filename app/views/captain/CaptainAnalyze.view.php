<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/CaptainAnalyze.css">

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
            <a href="<?= ROOT ?>/Captaindashboard">Home</a>
            <a href="<?= ROOT ?>/dashboard">Schedule</a>
            <a href=""class="active">Analyze</a>
            <a href="<?= ROOT ?>/CaptainAttendance">Attendance</a> <a href="<?= ROOT ?>/CaptainInventory">Inventory</a>
            <a href="<?= ROOT ?>/CaptainFinance">Finance</a>

            <!-- <a href="<?= ROOT ?>/CaptainNotices">Notices</a> -->
            <!-- <a href="<?= ROOT ?>/CaptainMealPlan">Meal Plan</a> -->
        </nav>
        <div class="nav-right">
            <button class="icon-btn">🔔</button>
            <div class="profile">
                <img class="avatar" src="<?php echo ROOT; ?>../assets/images/adminDashboard/header/avatar.jpg"
                    alt="Admin Avatar">

            </div>
        </div>
    </header>
<div id="analyze-page" class="page-content">
            <div class="page-header">
                <h1>Performance Analysis</h1>
                <p>Track your progress and performance metrics</p>
            </div>

            <div class="performance-overview">
                <h2>Performance Overview</h2>
                <div class="performance-metrics">
                    <div class="metric-circle">
                        <div class="circle-wrapper purple">
                            <div class="circle-bg">85%</div>
                        </div>
                        <div class="metric-label">Stamina</div>
                        <div class="metric-status">Excellent</div>
                    </div>
                    <div class="metric-circle">
                        <div class="circle-wrapper blue">
                            <div class="circle-bg">78%</div>
                        </div>
                        <div class="metric-label">Speed</div>
                        <div class="metric-status">Good</div>
                    </div>
                    <div class="metric-circle">
                        <div class="circle-wrapper green">
                            <div class="circle-bg">92%</div>
                        </div>
                        <div class="metric-label">Accuracy</div>
                        <div class="metric-status">Excellent</div>
                    </div>
                    <div class="metric-circle">
                        <div class="circle-wrapper orange">
                            <div class="circle-bg">81%</div>
                        </div>
                        <div class="metric-label">Overall</div>
                        <div class="metric-status">Good</div>
                    </div>
                </div>
            </div>

            <div class="analysis-grid">
                <div class="fitness-trends-card">
                    <h3>Fitness Test Trends</h3>
                    <div class="chart-container">
                        <svg class="chart-svg" viewBox="0 0 500 300">
                            <!-- Grid lines -->
                            <line x1="50" y1="250" x2="450" y2="250" stroke="#e0e0e0" stroke-width="1"/>
                            <line x1="50" y1="200" x2="450" y2="200" stroke="#e0e0e0" stroke-width="1"/>
                            <line x1="50" y1="150" x2="450" y2="150" stroke="#e0e0e0" stroke-width="1"/>
                            <line x1="50" y1="100" x2="450" y2="100" stroke="#e0e0e0" stroke-width="1"/>
                            <line x1="50" y1="50" x2="450" y2="50" stroke="#e0e0e0" stroke-width="1"/>
                            
                            <!-- Y-axis labels -->
                            <text x="30" y="255" font-size="12" fill="#666">60</text>
                            <text x="30" y="205" font-size="12" fill="#666">70</text>
                            <text x="30" y="155" font-size="12" fill="#666">80</text>
                            <text x="30" y="105" font-size="12" fill="#666">90</text>
                            <text x="25" y="55" font-size="12" fill="#666">100</text>
                            
                            <!-- X-axis labels -->
                            <text x="50" y="270" font-size="12" fill="#666">Jan</text>
                            <text x="120" y="270" font-size="12" fill="#666">Feb</text>
                            <text x="190" y="270" font-size="12" fill="#666">Mar</text>
                            <text x="260" y="270" font-size="12" fill="#666">Apr</text>
                            <text x="330" y="270" font-size="12" fill="#666">May</text>
                            <text x="400" y="270" font-size="12" fill="#666">Jun</text>
                            
                            <!-- Endurance line (purple) -->
                            <polyline points="60,220 130,200 200,180 270,160 340,150 410,130" 
                                      fill="none" stroke="#9c27b0" stroke-width="2"/>
                            <circle cx="60" cy="220" r="4" fill="#9c27b0"/>
                            <circle cx="130" cy="200" r="4" fill="#9c27b0"/>
                            <circle cx="200" cy="180" r="4" fill="#9c27b0"/>
                            <circle cx="270" cy="160" r="4" fill="#9c27b0"/>
                            <circle cx="340" cy="150" r="4" fill="#9c27b0"/>
                            <circle cx="410" cy="130" r="4" fill="#9c27b0"/>
                            
                            <!-- Sprint Speed line (green) -->
                            <polyline points="60,230 130,215 200,205 270,185 340,170 410,155" 
                                      fill="none" stroke="#4caf50" stroke-width="2"/>
                            <circle cx="60" cy="230" r="4" fill="#4caf50"/>
                            <circle cx="130" cy="215" r="4" fill="#4caf50"/>
                            <circle cx="200" cy="205" r="4" fill="#4caf50"/>
                            <circle cx="270" cy="185" r="4" fill="#4caf50"/>
                            <circle cx="340" cy="170" r="4" fill="#4caf50"/>
                            <circle cx="410" cy="155" r="4" fill="#4caf50"/>
                            
                            <!-- Agility line (orange) -->
                            <polyline points="60,235 130,220 200,210 270,195 340,180 410,165" 
                                      fill="none" stroke="#ff9800" stroke-width="2"/>
                            <circle cx="60" cy="235" r="4" fill="#ff9800"/>
                            <circle cx="130" cy="220" r="4" fill="#ff9800"/>
                            <circle cx="200" cy="210" r="4" fill="#ff9800"/>
                            <circle cx="270" cy="195" r="4" fill="#ff9800"/>
                            <circle cx="340" cy="180" r="4" fill="#ff9800"/>
                            <circle cx="410" cy="165" r="4" fill="#ff9800"/>
                        </svg>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color purple"></div>
                            <span>Endurance</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color green"></div>
                            <span>Sprint Speed</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color orange"></div>
                            <span>Agility</span>
                        </div>
                    </div>
                </div>

                <div class="monthly-comparison-card">
                    <h3>Monthly Comparison</h3>
                    <div class="comparison-list">
                        <div class="comparison-item green-bg">
                            <div class="comparison-info">
                                <div class="comparison-icon green">⚽</div>
                                <div class="comparison-text">
                                    <h4>Goals Scored</h4>
                                    <p>This month vs last month</p>
                                </div>
                            </div>
                            <div class="comparison-stats">
                                <div class="comparison-percentage positive">+25%</div>
                                <div class="comparison-values">8 vs 6</div>
                            </div>
                        </div>

                        <div class="comparison-item blue-bg">
                            <div class="comparison-info">
                                <div class="comparison-icon blue">⏱️</div>
                                <div class="comparison-text">
                                    <h4>Minutes Played</h4>
                                    <p>This month vs last month</p>
                                </div>
                            </div>
                            <div class="comparison-stats">
                                <div class="comparison-percentage positive">+12%</div>
                                <div class="comparison-values">450 vs 402</div>
                            </div>
                        </div>

                        <div class="comparison-item yellow-bg">
                            <div class="comparison-info">
                                <div class="comparison-icon yellow">⚡</div>
                                <div class="comparison-text">
                                    <h4>Sprint Speed</h4>
                                    <p>This month vs last month</p>
                                </div>
                            </div>
                            <div class="comparison-stats">
                                <div class="comparison-percentage positive">+8%</div>
                                <div class="comparison-values">28.5 vs 26.4 km/h</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="match-performance-card">
                <h3>Match Performance Stats</h3>
                <div class="match-stats-grid">
                    <div class="stat-box purple-bg">
                        <div class="stat-icon purple">⚽</div>
                        <div class="stat-number">12</div>
                        <div class="stat-label">Goals</div>
                    </div>
                    <div class="stat-box blue-bg">
                        <div class="stat-icon blue">🎯</div>
                        <div class="stat-number">8</div>
                        <div class="stat-label">Assists</div>
                    </div>
                    <div class="stat-box green-bg">
                        <div class="stat-icon green">⚡</div>
                        <div class="stat-number">456</div>
                        <div class="stat-label">Passes</div>
                    </div>
                    <div class="stat-box orange-bg">
                        <div class="stat-icon orange">⏱️</div>
                        <div class="stat-number">890</div>
                        <div class="stat-label">Minutes</div>
                    </div>
                </div>
            </div>
        </div>

    <script>
            window.APP_ROOT = "<?= ROOT ?>";
        </script>

        <script src="<?= ROOT ?>/assets/js/captain/CaptainAnalyze.js"></script>
    </div>
</body>
</html>