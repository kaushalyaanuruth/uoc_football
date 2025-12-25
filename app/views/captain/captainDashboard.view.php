<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOC Football - Captain Dashboard</title>

        <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/captainDashboard.css">
</head>
<body>
     <nav class="navbar">
        <div class="logo">
             <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="Football Player">
        </div>
        <ul class="nav-menu">
            <li><a href="#" class="nav-link active" data-page="home">Home</a></li>
            <li><a href="#" class="nav-link" data-page="schedule">Schedule</a></li>
            <li><a href="#" class="nav-link" data-page="analyze">Analyze</a></li>
            <li><a href="#" class="nav-link" data-page="budget">Budget</a></li>
            <li><a href="#" class="nav-link" data-page="attendance">Attendance</a></li>
            <li><a href="#" class="nav-link" data-page="notices">Notices</a></li>
        </ul>
        <div class="user-section">
            <div class="notification-icon">🔔</div>
            <div class="user-avatar"></div>
            <button class="logout-btn" id="logout-btn">Logout</button>
        </div>
    </nav>

    <div class="container">
        <!-- Home Page -->
        <div id="home-page" class="page-content active">
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h1>Welcome back, Sanjula!</h1>
                    <p class="welcome-date">Monday, 5th August 2025</p>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <h3>📅 Upcoming Training</h3>
                    <div class="event-info">
                        <p><strong>Morning Practice Session</strong></p>
                        <p>📍 Uni Ground</p>
                        <p>⏰ 6:00 AM - 8:00 AM</p>
                    </div>
                </div>

                <div class="card">
                    <h3>⚽ Next Match</h3>
                    <div class="event-info">
                        <p><strong>UOC vs Old Bens</strong></p>
                        <p>📍 Uni Ground</p>
                        <p>⏰ Monday 8th August - 6:00 AM</p>
                    </div>
                </div>

                <div class="card countdown">
                    <h3>⏱️ Days Until Next Match</h3>
                    <div class="countdown-number">3</div>
                    <div class="countdown-text">Days</div>
                    <p class="countdown-subtitle">UOC vs Old Bens</p>
                </div>
            </div>

            <div class="sidebar-container">
                <div class="announcements">
                    <h3>📢 Latest Announcements</h3>
                    <div class="announcement-item">
                        <h4>Team Meeting This Friday</h4>
                        <p>Don't forget about our strategy meeting at 5 PM in the conference room.</p>
                    </div>
                    <div class="announcement-item">
                        <h4>New Training Equipment</h4>
                        <p>We've received new training equipment. Please check it out at the facility.</p>
                    </div>
                    <div class="announcement-item">
                        <h4>Match Results</h4>
                        <p>Great win last weekend! 3-1 victory. Keep up the excellent work!</p>
                    </div>
                </div>

                <div class="quick-links">
                    <h3>🔗 Quick Links</h3>
                    <div class="link-item" id="meal-plan-preview">
                        <span class="link-icon">🍽️</span>
                        <span>Meal Plan</span>
                    </div>
                    <div class="link-item" id="performance-preview">
                        <span class="link-icon">📊</span>
                        <span>Performance Stats</span>
                    </div>
                    <!-- Removed Fitness Tests and Training Notes quick-links per request -->
                </div>
            </div>
        </div>

        <!-- Meal Plan Page (Weekly View) -->
        <div id="meal-plan-page" class="page-content">
            <div class="page-header">
                <h1>Weekly Meal Plan</h1>
                <p>Your personalized nutrition plan for optimal performance</p>
            </div>

            <div class="meal-plan-header">
                <div class="meal-plan-tabs">
                    <button class="meal-plan-tab active" data-meal="breakfast">Breakfast</button>
                    <button class="meal-plan-tab" data-meal="lunch">Lunch</button>
                    <button class="meal-plan-tab" data-meal="dinner">Dinner</button>
                </div>
            </div>

            <!-- calendar removed per UX request (kept quick-access only) -->

            <div class="meals-grid" id="meals-grid">
                <div class="meal-card" data-meal="breakfast">
                    <h3>🌅 Breakfast</h3>
                    <ul>
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable (minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>

                <div class="meal-card" data-meal="lunch">
                    <h3>🍽️ Lunch</h3>
                    <ul>
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable (minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>

                <div class="meal-card" data-meal="dinner">
                    <h3>🌙 Dinner</h3>
                    <ul>
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable (minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Simple Meal Plan Page -->
        <div id="simple-meal-page" class="page-content">
            <div class="page-header">
                <h1>Daily Meal Plan</h1>
                <p>Your complete nutrition guide for the day</p>
            </div>

            <div class="meal-tabs-container">
                <div class="meal-tabs">
                    <button class="meal-tab active">🌅 Breakfast</button>
                    <button class="meal-tab">🍽️ Lunch</button>
                    <button class="meal-tab">🌙 Dinner</button>
                </div>
            </div>

            <div class="meal-categories-grid">
                <div class="meal-category-card">
                    <div class="meal-category-header">
                        <div class="meal-icon">🌅</div>
                        <h2>Breakfast</h2>
                    </div>
                    <ul class="meal-category-list">
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable(minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>

                <div class="meal-category-card">
                    <div class="meal-category-header">
                        <div class="meal-icon">🍽️</div>
                        <h2>Lunch</h2>
                    </div>
                    <ul class="meal-category-list">
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable(minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>

                <div class="meal-category-card">
                    <div class="meal-category-header">
                        <div class="meal-icon">🌙</div>
                        <h2>Dinner</h2>
                    </div>
                    <ul class="meal-category-list">
                        <li>Basmati or Red rice</li>
                        <li>Chicken, Egg, Fish</li>
                        <li>Vegetable(minimum 3)</li>
                        <li>Pala</li>
                        <li>Yogurt</li>
                        <li>Fruits</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Schedule Page -->
        <div id="schedule-page" class="page-content">
            <div class="page-header">
                <h1>Schedule</h1>
                <p>View your practice sessions, matches, and training schedule</p>
            </div>

            <div class="schedule-container">
                <div class="schedule-filters">
                    <button class="filter-btn active" data-filter="all">All Events</button>
                    <button class="filter-btn" data-filter="practice">Practice</button>
                    <button class="filter-btn" data-filter="match">Matches</button>
                    <button class="filter-btn" data-filter="training">Training</button>
                </div>

                <div class="schedule-list">
                    <div class="schedule-item" data-type="practice">
                        <div class="schedule-header">
                            <div class="schedule-title">Morning Practice Session</div>
                            <div class="schedule-type practice">Practice</div>
                        </div>
                        <div class="schedule-details">
                            <div class="schedule-detail">📅 Monday, August 15, 2025</div>
                            <div class="schedule-detail">⏰ 6:00 AM - 8:00 AM</div>
                            <div class="schedule-detail">📍 Uni Ground</div>
                        </div>
                    </div>

                    <div class="schedule-item" data-type="match">
                        <div class="schedule-header">
                            <div class="schedule-title">UOC vs Old Bends - Practice Match</div>
                            <div class="schedule-type match">Match</div>
                        </div>
                        <div class="schedule-details">
                            <div class="schedule-detail">📅 Monday, August 8, 2025</div>
                            <div class="schedule-detail">⏰ 6:00 AM - 9:00 AM</div>
                            <div class="schedule-detail">📍 Uni Ground</div>
                        </div>
                    </div>

                    <div class="schedule-item" data-type="training">
                        <div class="schedule-header">
                            <div class="schedule-title">Strength & Conditioning</div>
                            <div class="schedule-type training">Training</div>
                        </div>
                        <div class="schedule-details">
                            <div class="schedule-detail">📅 Wednesday, August 17, 2025</div>
                            <div class="schedule-detail">⏰ 4:00 PM - 6:00 PM</div>
                            <div class="schedule-detail">📍 Gym Facility</div>
                        </div>
                    </div>

                    <div class="schedule-item" data-type="practice">
                        <div class="schedule-header">
                            <div class="schedule-title">Evening Practice - Tactical Training</div>
                            <div class="schedule-type practice">Practice</div>
                        </div>
                        <div class="schedule-details">
                            <div class="schedule-detail">📅 Thursday, August 18, 2025</div>
                            <div class="schedule-detail">⏰ 5:00 PM - 7:00 PM</div>
                            <div class="schedule-detail">📍 Uni Ground</div>
                        </div>
                    </div>

                    <div class="schedule-item" data-type="match">
                        <div class="schedule-header">
                            <div class="schedule-title">SLUG Championship - Quarter Finals</div>
                            <div class="schedule-type match">Match</div>
                        </div>
                        <div class="schedule-details">
                            <div class="schedule-detail">📅 Saturday, August 20, 2025</div>
                            <div class="schedule-detail">⏰ 3:00 PM - 5:00 PM</div>
                            <div class="schedule-detail">📍 City Stadium</div>
                        </div>
                    </div>

                    <div class="schedule-item" data-type="training">
                        <div class="schedule-header">
                            <div class="schedule-title">Recovery & Flexibility Session</div>
                            <div class="schedule-type training">Training</div>
                        </div>
                        <div class="schedule-details">
                            <div class="schedule-detail">📅 Sunday, August 21, 2025</div>
                            <div class="schedule-detail">⏰ 9:00 AM - 10:30 AM</div>
                            <div class="schedule-detail">📍 Training Center</div>
                        </div>
                    </div>

                    <div class="schedule-item" data-type="practice">
                        <div class="schedule-header">
                            <div class="schedule-title">Set Piece Training</div>
                            <div class="schedule-type practice">Practice</div>
                        </div>
                        <div class="schedule-details">
                            <div class="schedule-detail">📅 Tuesday, August 23, 2025</div>
                            <div class="schedule-detail">⏰ 6:00 AM - 8:00 AM</div>
                            <div class="schedule-detail">📍 Uni Ground</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Page -->
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


        <!-- Budget Page -->
        <div id="budget-page" class="page-content">
            <div class="page-header">
                <h1>Budget Management</h1>
                <p>Manage team expenses, funds, and overall budget</p>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <h3>💰 Total Budget</h3>
                    <div class="event-info">
                        <p style="font-size: 2rem; font-weight: bold; color: #4a1a4a;">LKR 45,250</p>
                        <p style="color: #4caf50;">↑ +8% from last month</p>
                    </div>
                </div>

                <div class="card">
                    <h3>💸 Total Expenses</h3>
                    <div class="event-info">
                        <p style="font-size: 2rem; font-weight: bold; color: #ff4444;">LKR 32,180</p>
                        <p style="color: #ff4444;">↑ +12% from last month</p>
                    </div>
                </div>

                <div class="card">
                    <h3>💵 Balance</h3>
                    <div class="event-info">
                        <p style="font-size: 2rem; font-weight: bold; color: #4caf50;">LKR 13,070</p>
                        <p style="color: #4caf50;">↑ +15% from last month</p>
                    </div>
                </div>
            </div>

            <div class="performance-overview" style="margin-top: 2rem;">
                <h2>Recent Transactions</h2>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e0e0e0;">
                                <th style="text-align: left; padding: 1rem; color: #666;">Date</th>
                                <th style="text-align: left; padding: 1rem; color: #666;">Description</th>
                                <th style="text-align: left; padding: 1rem; color: #666;">Category</th>
                                <th style="text-align: right; padding: 1rem; color: #666;">Amount</th>
                                <th style="text-align: center; padding: 1rem; color: #666;">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem;">Jan 15, 2024</td>
                                <td style="padding: 1rem;">Training Equipment</td>
                                <td style="padding: 1rem;">Equipment</td>
                                <td style="padding: 1rem; text-align: right; color: #ff4444; font-weight: 600;">-LKR 1,200</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #fee2e2; color: #dc2626; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Expense</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem;">Jan 12, 2024</td>
                                <td style="padding: 1rem;">Sponsor Payment</td>
                                <td style="padding: 1rem;">Income</td>
                                <td style="padding: 1rem; text-align: right; color: #4caf50; font-weight: 600;">+LKR 5,000</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Income</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem;">Jan 10, 2024</td>
                                <td style="padding: 1rem;">Travel Expenses</td>
                                <td style="padding: 1rem;">Transport</td>
                                <td style="padding: 1rem; text-align: right; color: #ff4444; font-weight: 600;">-LKR 800</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #fee2e2; color: #dc2626; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Expense</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem;">Jan 8, 2024</td>
                                <td style="padding: 1rem;">Match Registration Fees</td>
                                <td style="padding: 1rem;">Fees</td>
                                <td style="padding: 1rem; text-align: right; color: #ff4444; font-weight: 600;">-LKR 450</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #fee2e2; color: #dc2626; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Expense</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem;">Jan 5, 2024</td>
                                <td style="padding: 1rem;">Fundraising Event</td>
                                <td style="padding: 1rem;">Income</td>
                                <td style="padding: 1rem; text-align: right; color: #4caf50; font-weight: 600;">+LKR 2,500</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Income</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Attendance Page -->
        <div id="attendance-page" class="page-content">
            <div class="page-header">
                <h1>Attendance Management</h1>
                <p>Track and manage player attendance for training sessions and matches</p>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <h3>👥 Total Players</h3>
                    <div class="event-info">
                        <p style="font-size: 2.5rem; font-weight: bold; color: #4a1a4a;">24</p>
                        <p style="color: #666;">Registered Players</p>
                    </div>
                </div>

                <div class="card">
                    <h3>✅ Present Today</h3>
                    <div class="event-info">
                        <p style="font-size: 2.5rem; font-weight: bold; color: #4caf50;">18</p>
                        <p style="color: #4caf50;">75% Attendance Rate</p>
                    </div>
                </div>

                <div class="card">
                    <h3>❌ Absent Today</h3>
                    <div class="event-info">
                        <p style="font-size: 2.5rem; font-weight: bold; color: #ff4444;">6</p>
                        <p style="color: #ff4444;">25% Absence Rate</p>
                    </div>
                </div>
            </div>

            <div class="performance-overview" style="margin-top: 2rem;">
                <h2>Today's Attendance - <span style="color: #666; font-size: 1.2rem;">January 15, 2024</span></h2>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e0e0e0;">
                                <th style="text-align: left; padding: 1rem; color: #666;">Player Name</th>
                                <th style="text-align: left; padding: 1rem; color: #666;">Position</th>
                                <th style="text-align: center; padding: 1rem; color: #666;">Status</th>
                                <th style="text-align: left; padding: 1rem; color: #666;">Time</th>
                                <th style="text-align: left; padding: 1rem; color: #666;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem; font-weight: 500;">John Silva</td>
                                <td style="padding: 1rem;">Forward</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Present</span></td>
                                <td style="padding: 1rem;">6:00 AM</td>
                                <td style="padding: 1rem;">On time</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem; font-weight: 500;">Mike Perera</td>
                                <td style="padding: 1rem;">Midfielder</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Present</span></td>
                                <td style="padding: 1rem;">6:05 AM</td>
                                <td style="padding: 1rem;">5 min late</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem; font-weight: 500;">David Fernando</td>
                                <td style="padding: 1rem;">Defender</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #fee2e2; color: #dc2626; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Absent</span></td>
                                <td style="padding: 1rem;">-</td>
                                <td style="padding: 1rem;">Medical leave</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem; font-weight: 500;">Alex Kumar</td>
                                <td style="padding: 1rem;">Goalkeeper</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Present</span></td>
                                <td style="padding: 1rem;">5:55 AM</td>
                                <td style="padding: 1rem;">Early arrival</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem; font-weight: 500;">Chris Jayawardena</td>
                                <td style="padding: 1rem;">Forward</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Present</span></td>
                                <td style="padding: 1rem;">6:02 AM</td>
                                <td style="padding: 1rem;">On time</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem; font-weight: 500;">Sam Wickramasinghe</td>
                                <td style="padding: 1rem;">Midfielder</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #fee2e2; color: #dc2626; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Absent</span></td>
                                <td style="padding: 1rem;">-</td>
                                <td style="padding: 1rem;">Personal reasons</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem; font-weight: 500;">Tom Rajapakse</td>
                                <td style="padding: 1rem;">Defender</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Present</span></td>
                                <td style="padding: 1rem;">6:00 AM</td>
                                <td style="padding: 1rem;">On time</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem; font-weight: 500;">Ryan De Silva</td>
                                <td style="padding: 1rem;">Forward</td>
                                <td style="padding: 1rem; text-align: center;"><span style="background: #d1fae5; color: #059669; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.85rem;">Present</span></td>
                                <td style="padding: 1rem;">6:10 AM</td>
                                <td style="padding: 1rem;">10 min late</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="analysis-grid" style="margin-top: 2rem;">
                <div class="fitness-trends-card">
                    <h3>Weekly Attendance Trend</h3>
                    <div class="chart-container">
                        <svg class="chart-svg" viewBox="0 0 500 300">
                            <!-- Grid lines -->
                            <line x1="50" y1="250" x2="450" y2="250" stroke="#e0e0e0" stroke-width="1"/>
                            <line x1="50" y1="200" x2="450" y2="200" stroke="#e0e0e0" stroke-width="1"/>
                            <line x1="50" y1="150" x2="450" y2="150" stroke="#e0e0e0" stroke-width="1"/>
                            <line x1="50" y1="100" x2="450" y2="100" stroke="#e0e0e0" stroke-width="1"/>
                            <line x1="50" y1="50" x2="450" y2="50" stroke="#e0e0e0" stroke-width="1"/>
                            
                            <!-- Y-axis labels -->
                            <text x="30" y="255" font-size="12" fill="#666">0</text>
                            <text x="25" y="205" font-size="12" fill="#666">25</text>
                            <text x="25" y="155" font-size="12" fill="#666">50</text>
                            <text x="25" y="105" font-size="12" fill="#666">75</text>
                            <text x="20" y="55" font-size="12" fill="#666">100</text>
                            
                            <!-- X-axis labels -->
                            <text x="50" y="270" font-size="12" fill="#666">Mon</text>
                            <text x="110" y="270" font-size="12" fill="#666">Tue</text>
                            <text x="170" y="270" font-size="12" fill="#666">Wed</text>
                            <text x="230" y="270" font-size="12" fill="#666">Thu</text>
                            <text x="295" y="270" font-size="12" fill="#666">Fri</text>
                            <text x="355" y="270" font-size="12" fill="#666">Sat</text>
                            <text x="415" y="270" font-size="12" fill="#666">Sun</text>
                            
                            <!-- Attendance line -->
                            <polyline points="60,100 120,110 180,90 240,105 300,95 360,115 420,105" 
                                      fill="none" stroke="#4a1a4a" stroke-width="3"/>
                            <circle cx="60" cy="100" r="5" fill="#4a1a4a"/>
                            <circle cx="120" cy="110" r="5" fill="#4a1a4a"/>
                            <circle cx="180" cy="90" r="5" fill="#4a1a4a"/>
                            <circle cx="240" cy="105" r="5" fill="#4a1a4a"/>
                            <circle cx="300" cy="95" r="5" fill="#4a1a4a"/>
                            <circle cx="360" cy="115" r="5" fill="#4a1a4a"/>
                            <circle cx="420" cy="105" r="5" fill="#4a1a4a"/>
                        </svg>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color" style="background: #4a1a4a; width: 20px; height: 3px; border-radius: 2px;"></div>
                            <span>Attendance %</span>
                        </div>
                    </div>
                </div>

                <div class="monthly-comparison-card">
                    <h3>Attendance Summary</h3>
                    <div class="comparison-list">
                        <div class="comparison-item green-bg">
                            <div class="comparison-info">
                                <div class="comparison-icon green">✓</div>
                                <div class="comparison-text">
                                    <h4>Average Attendance</h4>
                                    <p>Last 30 days</p>
                                </div>
                            </div>
                            <div class="comparison-stats">
                                <div class="comparison-percentage positive">82%</div>
                                <div class="comparison-values">19.7/24 players</div>
                            </div>
                        </div>

                        <div class="comparison-item blue-bg">
                            <div class="comparison-info">
                                <div class="comparison-icon blue">👥</div>
                                <div class="comparison-text">
                                    <h4>Best Attendance Day</h4>
                                    <p>This month</p>
                                </div>
                            </div>
                            <div class="comparison-stats">
                                <div class="comparison-percentage positive">96%</div>
                                <div class="comparison-values">23/24 players</div>
                            </div>
                        </div>

                        <div class="comparison-item yellow-bg">
                            <div class="comparison-info">
                                <div class="comparison-icon yellow">📊</div>
                                <div class="comparison-text">
                                    <h4>Consistent Players</h4>
                                    <p>100% attendance</p>
                                </div>
                            </div>
                            <div class="comparison-stats">
                                <div class="comparison-percentage positive">45%</div>
                                <div class="comparison-values">11/24 players</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notices Page -->
        <div id="notices-page" class="page-content">
            <div class="page-header">
                <h1>Notices</h1>
                <p>Stay updated with the latest team announcements</p>
            </div>

            <div class="notices-header">
                <div class="search-filter-container">
                    <div class="search-box">
                        <input type="text" placeholder="Search here...">
                    </div>
                    <div class="filter-dropdown">
                        <span>🔽</span>
                        <span>All</span>
                    </div>
                </div>
            </div>

            <div class="notices-grid">
                <div class="notice-card">
                    <div class="notice-badge">NEW</div>
                    <div class="notice-header-info">
                        <span>👤 Team Captain</span>
                        <span>📅 Jan 13, 2024</span>
                    </div>
                    <div class="notice-title">Notice</div>
                    <div class="notice-content">
                        Tomorrow's (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                    </div>
                </div>

                <div class="notice-card">
                    <div class="notice-header-info">
                        <span>👤 Team Captain</span>
                        <span>📅 Jan 13, 2024</span>
                    </div>
                    <div class="notice-title">Notice</div>
                    <div class="notice-content">
                        Tomorrow's (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                    </div>
                </div>

                <div class="notice-card">
                    <div class="notice-badge">NEW</div>
                    <div class="notice-header-info">
                        <span>👤 Coach Martinez</span>
                        <span>📅 Jan 14, 2024</span>
                    </div>
                    <div class="notice-title">Notice</div>
                    <div class="notice-content">
                        Tomorrow's (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                    </div>
                </div>

                <div class="notice-card">
                    <div class="notice-header-info">
                        <span>👤 Coach Martinez</span>
                        <span>📅 Jan 14, 2024</span>
                    </div>
                    <div class="notice-title">Notice</div>
                    <div class="notice-content">
                        Tomorrow's (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                    </div>
                </div>

                <div class="notice-card">
                    <div class="notice-header-info">
                        <span>👤 Coach Martinez</span>
                        <span>📅 Jan 14, 2024</span>
                    </div>
                    <div class="notice-title">Notice</div>
                    <div class="notice-content">
                        Tomorrow's (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                    </div>
                </div>

                <div class="notice-card">
                    <div class="notice-header-info">
                        <span>👤 Team Captain</span>
                        <span>📅 Jan 13, 2024</span>
                    </div>
                    <div class="notice-title">Notice</div>
                    <div class="notice-content">
                        Tomorrow's (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                    </div>
                </div>

                <div class="notice-card">
                    <div class="notice-header-info">
                        <span>👤 Coach Martinez</span>
                        <span>📅 Jan 14, 2024</span>
                    </div>
                    <div class="notice-title">Notice</div>
                    <div class="notice-content">
                        Tomorrow's (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                    </div>
                </div>

                <div class="notice-card">
                    <div class="notice-header-info">
                        <span>👤 Coach Martinez</span>
                        <span>📅 Jan 14, 2024</span>
                    </div>
                    <div class="notice-title">Notice</div>
                    <div class="notice-content">
                        Tomorrow's (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.
                    </div>
                </div>
            </div>
        </div>
    </div>

    
<script src="<?= ROOT ?>/assets/js/captain/captainDashboard.js"></script>
</body>
