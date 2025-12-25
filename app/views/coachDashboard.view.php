<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOC Football - Coaching Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f7;
            color: #333;
        }

        /* Navigation */
        nav {
            background: white;
            padding: 15px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .nav-menu {
            display: flex;
            gap: 10px;
            list-style: none;
        }

        .nav-menu button {
            padding: 10px 25px;
            border: none;
            background: transparent;
            border-radius: 20px;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.3s;
            color: #666;
        }

        .nav-menu button.active {
            background: #4a1a5c;
            color: white;
        }

        .nav-menu button:hover {
            background: #f0f0f0;
        }

        .nav-menu button.active:hover {
            background: #4a1a5c;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f5f5f7;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            cursor: pointer;
        }

        .logout-btn {
            padding: 10px 20px;
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: #cc0000;
        }

        /* Page Container */
        .page {
            display: none;
            padding: 30px 40px;
            animation: fadeIn 0.3s;
        }

        .page.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Home Page */
        .welcome-banner {
            background: linear-gradient(135deg, #4a1a5c 0%, #6b2e8c 100%);
            color: white;
            padding: 50px;
            border-radius: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            right: -50px;
            top: -50px;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            right: 50px;
            bottom: -80px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .welcome-text h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .date-time {
            text-align: right;
        }

        .date-time h2 {
            font-size: 36px;
            margin-bottom: 5px;
        }

        .date-time p {
            font-size: 24px;
        }

        .home-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #4a1a5c;
        }

        .next-practice {
            text-align: center;
        }

        .next-practice .date {
            font-size: 24px;
            color: #666;
            margin: 15px 0;
        }

        .next-event h4 {
            color: #999;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .next-event h3 {
            color: #333;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .event-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
            color: #666;
        }

        .event-details div {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .countdown {
            text-align: center;
        }

        .countdown .number {
            font-size: 72px;
            color: #e74c3c;
            font-weight: bold;
            line-height: 1;
        }

        .countdown .days {
            font-size: 48px;
            color: #333;
            font-weight: bold;
        }

        .countdown .subtitle {
            font-size: 18px;
            color: #666;
            margin-top: 10px;
        }

        .countdown .slug {
            color: #4a1a5c;
            font-weight: bold;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .team-calendar h3 {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-icon {
            color: #4a1a5c;
        }

        .calendar-events {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .calendar-event {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-event .date {
            color: #666;
        }

        .event-badge {
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .event-badge.practice {
            background: #e3f2fd;
            color: #1976d2;
        }

        .event-badge.match {
            background: #ffebee;
            color: #d32f2f;
        }

        .event-badge.training {
            background: #e8f5e9;
            color: #388e3c;
        }

        .meal-plan h3 {
            margin-bottom: 20px;
        }

        .meal-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .meal-tabs button {
            padding: 8px 20px;
            border: none;
            background: #f5f5f7;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .meal-tabs button.active {
            background: #4a1a5c;
            color: white;
        }

        .meal-items {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .meal-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #666;
        }

        .meal-item:last-child {
            border-bottom: none;
        }

        .bottom-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        .bar-chart {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            height: 250px;
            padding: 20px 0;
        }

        .bar {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .bar-fill {
            width: 40px;
            background: #8b5cf6;
            border-radius: 5px 5px 0 0;
            transition: all 0.3s;
        }

        .bar:hover .bar-fill {
            opacity: 0.8;
        }

        .bar-label {
            color: #666;
            font-size: 12px;
        }

        .test-results {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .test-result-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .test-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .test-info {
            flex: 1;
        }

        .test-info .name {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .test-info .type {
            font-size: 12px;
            color: #999;
        }

        .test-score {
            font-weight: bold;
            font-size: 18px;
            color: #4a1a5c;
        }

        /* Schedule Page */
        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .filter-buttons button {
            padding: 10px 25px;
            border: none;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
        }

        .filter-buttons button.active {
            background: #4a1a5c;
            color: white;
        }

        .add-event-btn {
            padding: 12px 30px;
            background: #4a1a5c;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.3s;
        }

        .add-event-btn:hover {
            background: #6b2e8c;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .event-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: relative;
        }

        .event-card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .event-icon {
            width: 50px;
            height: 50px;
            background: #4a1a5c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .event-card h3 {
            font-size: 20px;
            color: #333;
        }

        .event-card h4 {
            font-size: 14px;
            color: #999;
            font-weight: normal;
        }

        .event-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }

        .event-info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #666;
        }

        .edit-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            cursor: pointer;
            color: #999;
        }

        .pagination {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination button {
            padding: 8px 16px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .pagination button.active {
            background: #4a1a5c;
            color: white;
            border-color: #4a1a5c;
        }

        .pagination button:hover {
            background: #f5f5f7;
        }

        .pagination button.active:hover {
            background: #6b2e8c;
        }

        /* Analysis Page */
        .analysis-filters {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 14px;
            color: #666;
        }

        .filter-group select,
        .filter-group input {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.success {
            background: #d4edda;
            color: #28a745;
        }

        .stat-icon.danger {
            background: #f8d7da;
            color: #dc3545;
        }

        .stat-icon.primary {
            background: #cfe2ff;
            color: #0d6efd;
        }

        .stat-icon.warning {
            background: #fff3cd;
            color: #ffc107;
        }

        .stat-icon.info {
            background: #d1ecf1;
            color: #0dcaf0;
        }

        .stat-icon.orange {
            background: #ffe5d0;
            color: #ff6b35;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .line-chart {
            height: 300px;
            padding: 20px;
            position: relative;
        }

        .chart-legend {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .legend-color {
            width: 20px;
            height: 3px;
            border-radius: 2px;
        }

        .pie-chart {
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 10px;
        }

        .pie-chart canvas {
            max-width: 100%;
            height: auto;
        }

        .comparison-chart {
            height: 300px;
            padding: 20px;
        }

        .comparison-bars {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            height: 250px;
            padding: 20px 0;
        }

        .comparison-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .bar-pair {
            display: flex;
            gap: 5px;
            align-items: flex-end;
        }

        .comparison-bar {
            width: 35px;
            border-radius: 5px 5px 0 0;
        }

        .comparison-bar.current {
            background: #8b5cf6;
        }

        .comparison-bar.previous {
            background: #14b8a6;
        }

        .player-comparison {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .player-stat {
            text-align: center;
        }

        .player-stat-value {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .player-stat-label {
            font-size: 13px;
            color: #666;
        }

        .player-stat-value.goals { color: #28a745; }
        .player-stat-value.assists { color: #0d6efd; }
        .player-stat-value.attendance { color: #8b5cf6; }
        .player-stat-value.stamina { color: #ffc107; }
        .player-stat-value.overall { color: #dc3545; }

        .match-breakdown {
            width: 100%;
            border-collapse: collapse;
        }

        .match-breakdown th,
        .match-breakdown td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .match-breakdown th {
            background: #f8f9fa;
            font-weight: 600;
            color: #666;
            font-size: 13px;
        }

        .match-breakdown td {
            color: #333;
        }

        .player-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .player-avatar-small {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .notes-section {
            margin-top: 20px;
        }

        .notes-textarea {
            width: 100%;
            min-height: 120px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
        }

        .save-notes-btn {
            margin-top: 15px;
            padding: 12px 30px;
            background: #4a1a5c;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            float: right;
        }

        /* Attendance Page */
        .attendance-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .attendance-table-container {
            overflow-x: auto;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .attendance-table th {
            background: #4a1a5c;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .attendance-table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .attendance-table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge.present {
            background: #d4edda;
            color: #28a745;
        }

        .status-badge.absent {
            background: #f8d7da;
            color: #dc3545;
        }

        .status-badge.late {
            background: #fff3cd;
            color: #ffc107;
        }

        .attendance-stats {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .stat-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
        }

        .stat-box h3 {
            font-size: 48px;
            color: #4a1a5c;
            margin-bottom: 10px;
        }

        .stat-box p {
            color: #666;
        }

        /* Notices Page */
        .notices-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .notice-filters {
            display: flex;
            gap: 10px;
        }

        .notice-filters button {
            padding: 10px 25px;
            border: none;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .notice-filters button.active {
            background: #4a1a5c;
            color: white;
        }

        .notices-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .notice-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #4a1a5c;
            transition: all 0.3s;
        }

        .notice-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .notice-card.important {
            border-left-color: #dc3545;
        }

        .notice-card.event {
            border-left-color: #ffc107;
        }

        .notice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .notice-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .notice-date {
            color: #999;
            font-size: 13px;
        }

        .notice-badge {
            padding: 5px 15px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .notice-badge.important {
            background: #f8d7da;
            color: #dc3545;
        }

        .notice-badge.general {
            background: #cfe2ff;
            color: #0d6efd;
        }

        .notice-badge.event {
            background: #fff3cd;
            color: #ffc107;
        }

        .notice-content {
            color: #666;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .home-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .events-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            nav {
                padding: 15px 20px;
            }

            .page {
                padding: 20px;
            }

            .home-grid {
                grid-template-columns: 1fr;
            }

            .events-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-row {
                grid-template-columns: 1fr;
            }

            .bottom-section {
                grid-template-columns: 1fr;
            }

            .attendance-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="logo">
            <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football">
        </div>
        <ul class="nav-menu">
            <li><button class="active" onclick="showPage('home')">Home</button></li>
            <li><button onclick="showPage('schedule')">Schedule</button></li>
            <li><button onclick="showPage('analyze')">Analyze</button></li>
            <li><button onclick="showPage('attendance')">Attendance</button></li>
            <li><button onclick="showPage('notices')">Notices</button></li>
            <li><button onclick="showPage('meal-plan')">Meal Plan</button></li>
        </ul>
        <div class="nav-right">
            <div class="notification-icon">🔔</div>
            <div class="user-avatar"></div>
            <button class="logout-btn" id="logout-btn">Logout</button>
        </div>
    </nav>

    <!-- Home Page -->
    <div id="home" class="page active">
        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Welcome,<br>Coach<br>Priyajan</h1>
            </div>
            <div class="date-time">
                <h2 id="current-date">15, August 2025</h2>
                <p id="current-time">19:25 P.M.</p>
            </div>
        </div>

        <div class="home-grid">
            <div class="card next-practice">
                <h3>Next Practice...</h3>
                <div class="date">
                    <strong>15, August,<br>morning,<br>6.00 A.M.</strong>
                </div>
            </div>

            <div class="card next-event">
                <h4>Practice Match</h4>
                <h3>UOC vs Old Bends</h3>
                <div class="event-details">
                    <div>📅 Monday, August 8 at 6 a.m.</div>
                    <div>📍 Uni Ground</div>
                </div>
            </div>

            <div class="card countdown">
                <div class="number">28</div>
                <div class="days">days</div>
                <div class="subtitle">more<br>to be<br><span class="slug">SLUG Champions</span></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div class="card">
                <h3>Attendance Overview</h3>
                <div class="chart-container">
                    <div class="bar-chart">
                        <div class="bar">
                            <div class="bar-fill" style="height: 180px;"></div>
                            <div class="bar-label">Mon</div>
                        </div>
                        <div class="bar">
                            <div class="bar-fill" style="height: 200px;"></div>
                            <div class="bar-label">Tue</div>
                        </div>
                        <div class="bar">
                            <div class="bar-fill" style="height: 190px;"></div>
                            <div class="bar-label">Wed</div>
                        </div>
                        <div class="bar">
                            <div class="bar-fill" style="height: 210px;"></div>
                            <div class="bar-label">Thu</div>
                        </div>
                        <div class="bar">
                            <div class="bar-fill" style="height: 170px;"></div>
                            <div class="bar-label">Fri</div>
                        </div>
                        <div class="bar">
                            <div class="bar-fill" style="height: 150px;"></div>
                            <div class="bar-label">Sat</div>
                        </div>
                        <div class="bar">
                            <div class="bar-fill" style="height: 165px;"></div>
                            <div class="bar-label">Sun</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sidebar">
                <div class="card">
                    <h3>Team Calendar <span class="calendar-icon">📅</span></h3>
                    <div class="calendar-events">
                        <div class="calendar-event">
                            <div class="date">Jan 21</div>
                            <div class="event-badge practice">Practice</div>
                        </div>
                        <div class="calendar-event">
                            <div class="date">Jan 24</div>
                            <div class="event-badge match">Match</div>
                        </div>
                        <div class="calendar-event">
                            <div class="date">Jan 26</div>
                            <div class="event-badge training">Training</div>
                        </div>
                    </div>
                </div>

                <div class="card meal-plan">
                    <h3>Meal Plan</h3>
                    <div class="meal-tabs">
                        <button class="active">Breakfast</button>
                        <button>Lunch</button>
                        <button>Dinner</button>
                    </div>
                    <div class="meal-items">
                        <div class="meal-item">Basmati or Red rice</div>
                        <div class="meal-item">Chicken, Egg, Fish</div>
                        <div class="meal-item">Vegetable (minimum 3)</div>
                        <div class="meal-item">Pala</div>
                        <div class="meal-item">Yogurt</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom-section">
            <div class="card">
                <h3>Recent Test Results</h3>
                <div class="test-results">
                    <div class="test-result-item">
                        <div class="test-avatar"></div>
                        <div class="test-info">
                            <div class="name">Alex Rivera</div>
                            <div class="type">Speed Test</div>
                        </div>
                        <div class="test-score">8.2s</div>
                    </div>
                    <div class="test-result-item">
                        <div class="test-avatar"></div>
                        <div class="test-info">
                            <div class="name">Jake Thompson</div>
                            <div class="type">Strength Test</div>
                        </div>
                        <div class="test-score">185kg</div>
                    </div>
                    <div class="test-result-item">
                        <div class="test-avatar"></div>
                        <div class="test-info">
                            <div class="name">Sam Wilson</div>
                            <div class="type">Endurance Test</div>
                        </div>
                        <div class="test-score">12:45</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Page -->
    <div id="schedule" class="page">
        <div class="schedule-header">
            <div class="filter-buttons">
                <button class="active">All Events</button>
                <button>Matches Only</button>
                <button>Training Only</button>
            </div>
            <button class="add-event-btn">+ Add Event</button>
        </div>

        <div class="events-grid">
            <div class="event-card">
                <div class="edit-icon">✏️</div>
                <div class="event-card-header">
                    <div class="event-icon">💪</div>
                    <div>
                        <h3>Training Session</h3>
                        <h4>Training Session</h4>
                    </div>
                </div>
                <div class="event-info">
                    <div class="event-info-item">📅 September 01 2025</div>
                    <div class="event-info-item">🕐 8.00 am - 12.00 pm</div>
                    <div class="event-info-item">📍 University Ground</div>
                    <div class="event-info-item">👤 Coach Silva</div>
                </div>
            </div>

            <div class="event-card">
                <div class="edit-icon">✏️</div>
                <div class="event-card-header">
                    <div class="event-icon">💪</div>
                    <div>
                        <h3>Training Session</h3>
                        <h4>Training Session</h4>
                    </div>
                </div>
                <div class="event-info">
                    <div class="event-info-item">📅 September 01 2025</div>
                    <div class="event-info-item">🕐 8.00 am - 12.00 pm</div>
                    <div class="event-info-item">📍 University Ground</div>
                    <div class="event-info-item">👤 Coach Silva</div>
                </div>
            </div>

            <div class="event-card">
                <div class="edit-icon">✏️</div>
                <div class="event-card-header">
                    <div class="event-icon">💪</div>
                    <div>
                        <h3>Training Session</h3>
                        <h4>Training Session</h4>
                    </div>
                </div>
                <div class="event-info">
                    <div class="event-info-item">📅 September 01 2025</div>
                    <div class="event-info-item">🕐 8.00 am - 12.00 pm</div>
                    <div class="event-info-item">📍 University Ground</div>
                    <div class="event-info-item">👤 Coach Silva</div>
                </div>
            </div>

            <div class="event-card">
                <div class="edit-icon">✏️</div>
                <div class="event-card-header">
                    <div class="event-icon">💪</div>
                    <div>
                        <h3>Training Session</h3>
                        <h4>Training Session</h4>
                    </div>
                </div>
                <div class="event-info">
                    <div class="event-info-item">📅 September 01 2025</div>
                    <div class="event-info-item">🕐 8.00 am - 12.00 pm</div>
                    <div class="event-info-item">📍 University Ground</div>
                    <div class="event-info-item">👤 Coach Silva</div>
                </div>
            </div>

            <div class="event-card">
                <div class="edit-icon">✏️</div>
                <div class="event-card-header">
                    <div class="event-icon">💪</div>
                    <div>
                        <h3>Training Session</h3>
                        <h4>Training Session</h4>
                    </div>
                </div>
                <div class="event-info">
                    <div class="event-info-item">📅 September 01 2025</div>
                    <div class="event-info-item">🕐 8.00 am - 12.00 pm</div>
                    <div class="event-info-item">📍 University Ground</div>
                    <div class="event-info-item">👤 Coach Silva</div>
                </div>
            </div>

            <div class="event-card">
                <div class="edit-icon">✏️</div>
                <div class="event-card-header">
                    <div class="event-icon">💪</div>
                    <div>
                        <h3>Training Session</h3>
                        <h4>Training Session</h4>
                    </div>
                </div>
                <div class="event-info">
                    <div class="event-info-item">📅 September 01 2025</div>
                    <div class="event-info-item">🕐 8.00 am - 12.00 pm</div>
                    <div class="event-info-item">📍 University Ground</div>
                    <div class="event-info-item">👤 Coach Silva</div>
                </div>
            </div>
        </div>

        <div class="pagination">
            <button>Previous</button>
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button>Next</button>
        </div>
    </div>

    <!-- Analysis Page -->
    <div id="analyze" class="page">
        <div class="analysis-filters">
            <div class="filter-group">
                <label>Analysis Type</label>
                <select>
                    <option>Select Player</option>
                    <option>Team Analysis</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Match</label>
                <select>
                    <option>vs Arsenal FC</option>
                    <option>vs Manchester</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Start Date</label>
                <input type="date" placeholder="mm/dd/yyyy">
            </div>
            <div class="filter-group">
                <label>End Date</label>
                <input type="date" placeholder="mm/dd/yyyy">
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon success">⚽</div>
                <div class="stat-value">12</div>
                <div class="stat-label">Goals Scored</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon danger">✖️</div>
                <div class="stat-value">8</div>
                <div class="stat-label">Goals Missed</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon primary">🎯</div>
                <div class="stat-value">68%</div>
                <div class="stat-label">Shots on Target</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">⚡</div>
                <div class="stat-value">62%</div>
                <div class="stat-label">Possession</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon info">✅</div>
                <div class="stat-value">84%</div>
                <div class="stat-label">Pass Accuracy</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">⚠️</div>
                <div class="stat-value">15/2</div>
                <div class="stat-label">Fouls/Cards</div>
            </div>
        </div>

        <div class="charts-row">
            <div class="card">
                <h3>Performance Trend</h3>
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: #8b5cf6;"></div>
                        <span>Fitness</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #14b8a6;"></div>
                        <span>Stamina</span>
                    </div>
                </div>
                <div class="line-chart">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>

            <div class="card">
                <h3>Match Stats Comparison</h3>
                <div class="comparison-chart">
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color" style="background: #8b5cf6; width: 15px; height: 15px;"></div>
                            <span>Current Match</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #14b8a6; width: 15px; height: 15px;"></div>
                            <span>Previous Match</span>
                        </div>
                    </div>
                    <div class="comparison-bars">
                        <div class="comparison-group">
                            <div class="bar-pair">
                                <div class="comparison-bar current" style="height: 120px;"></div>
                                <div class="comparison-bar previous" style="height: 100px;"></div>
                            </div>
                            <div class="bar-label">Goals Scored</div>
                        </div>
                        <div class="comparison-group">
                            <div class="bar-pair">
                                <div class="comparison-bar current" style="height: 80px;"></div>
                                <div class="comparison-bar previous" style="height: 130px;"></div>
                            </div>
                            <div class="bar-label">Goals Missed</div>
                        </div>
                        <div class="comparison-group">
                            <div class="bar-pair">
                                <div class="comparison-bar current" style="height: 150px;"></div>
                                <div class="comparison-bar previous" style="height: 120px;"></div>
                            </div>
                            <div class="bar-label">Assists</div>
                        </div>
                        <div class="comparison-group">
                            <div class="bar-pair">
                                <div class="comparison-bar current" style="height: 200px;"></div>
                                <div class="comparison-bar previous" style="height: 180px;"></div>
                            </div>
                            <div class="bar-label">Shots</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Match Outcomes</h3>
            <div class="pie-chart">
                <canvas id="pieChart" width="450" height="220"></canvas>
            </div>
        </div>

        <div class="card">
            <h3>Player Comparison</h3>
            <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                <select style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                    <option>Select Player 1</option>
                </select>
                <select style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                    <option>Select Player 2</option>
                </select>
            </div>
            <div class="player-comparison">
                <div class="player-stat">
                    <div class="player-stat-value goals">8</div>
                    <div class="player-stat-label">Goals</div>
                </div>
                <div class="player-stat">
                    <div class="player-stat-value assists">12</div>
                    <div class="player-stat-label">Assists</div>
                </div>
                <div class="player-stat">
                    <div class="player-stat-value attendance">92%</div>
                    <div class="player-stat-label">Attendance</div>
                </div>
                <div class="player-stat">
                    <div class="player-stat-value stamina">85%</div>
                    <div class="player-stat-label">Stamina</div>
                </div>
                <div class="player-stat">
                    <div class="player-stat-value overall">A+</div>
                    <div class="player-stat-label">Overall</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Detailed Match Breakdown</h3>
            <table class="match-breakdown">
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
                                <div class="player-avatar-small"></div>
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
                                <div class="player-avatar-small"></div>
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
                </tbody>
            </table>

            <div class="notes-section">
                <h3>Coach Notes & Insights</h3>
                <textarea class="notes-textarea" placeholder="Add your match analysis, strategy improvements, or player feedback..."></textarea>
                <button class="save-notes-btn">Save Notes</button>
            </div>
        </div>
    </div>

    <!-- Attendance Page -->
    <div id="attendance" class="page">
        <h2 style="margin-bottom: 30px; color: #4a1a5c;">Attendance Management</h2>
        
        <div class="attendance-grid">
            <div class="card">
                <h3>Player Attendance Records</h3>
                <div class="attendance-table-container">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>Player Name</th>
                                <th>Date</th>
                                <th>Session</th>
                                <th>Status</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Marcus Johnson</td>
                                <td>Aug 15, 2025</td>
                                <td>Morning Practice</td>
                                <td><span class="status-badge present">Present</span></td>
                                <td>5:55 AM</td>
                                <td>8:30 AM</td>
                            </tr>
                            <tr>
                                <td>David Wilson</td>
                                <td>Aug 15, 2025</td>
                                <td>Morning Practice</td>
                                <td><span class="status-badge present">Present</span></td>
                                <td>6:02 AM</td>
                                <td>8:28 AM</td>
                            </tr>
                            <tr>
                                <td>Alex Rivera</td>
                                <td>Aug 15, 2025</td>
                                <td>Morning Practice</td>
                                <td><span class="status-badge late">Late</span></td>
                                <td>6:15 AM</td>
                                <td>8:30 AM</td>
                            </tr>
                            <tr>
                                <td>Jake Thompson</td>
                                <td>Aug 15, 2025</td>
                                <td>Morning Practice</td>
                                <td><span class="status-badge present">Present</span></td>
                                <td>5:58 AM</td>
                                <td>8:32 AM</td>
                            </tr>
                            <tr>
                                <td>Sam Wilson</td>
                                <td>Aug 15, 2025</td>
                                <td>Morning Practice</td>
                                <td><span class="status-badge absent">Absent</span></td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Chris Brown</td>
                                <td>Aug 15, 2025</td>
                                <td>Morning Practice</td>
                                <td><span class="status-badge present">Present</span></td>
                                <td>6:00 AM</td>
                                <td>8:29 AM</td>
                            </tr>
                            <tr>
                                <td>Tom Martinez</td>
                                <td>Aug 15, 2025</td>
                                <td>Morning Practice</td>
                                <td><span class="status-badge present">Present</span></td>
                                <td>5:57 AM</td>
                                <td>8:31 AM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="attendance-stats">
                <div class="stat-box">
                    <h3>92%</h3>
                    <p>Overall Attendance Rate</p>
                </div>
                <div class="stat-box">
                    <h3>28</h3>
                    <p>Total Players</p>
                </div>
                <div class="stat-box">
                    <h3>26</h3>
                    <p>Present Today</p>
                </div>
                <div class="stat-box">
                    <h3>2</h3>
                    <p>Absent Today</p>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>Weekly Attendance Overview</h3>
            <div class="chart-container">
                <div class="bar-chart">
                    <div class="bar">
                        <div class="bar-fill" style="height: 180px;"></div>
                        <div class="bar-label">Mon</div>
                    </div>
                    <div class="bar">
                        <div class="bar-fill" style="height: 200px;"></div>
                        <div class="bar-label">Tue</div>
                    </div>
                    <div class="bar">
                        <div class="bar-fill" style="height: 190px;"></div>
                        <div class="bar-label">Wed</div>
                    </div>
                    <div class="bar">
                        <div class="bar-fill" style="height: 210px;"></div>
                        <div class="bar-label">Thu</div>
                    </div>
                    <div class="bar">
                        <div class="bar-fill" style="height: 170px;"></div>
                        <div class="bar-label">Fri</div>
                    </div>
                    <div class="bar">
                        <div class="bar-fill" style="height: 150px;"></div>
                        <div class="bar-label">Sat</div>
                    </div>
                    <div class="bar">
                        <div class="bar-fill" style="height: 165px;"></div>
                        <div class="bar-label">Sun</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notices Page -->
    <div id="notices" class="page">
        <div class="notices-header">
            <h2 style="color: #4a1a5c;">Team Notices & Announcements</h2>
            <div class="notice-filters">
                <button class="active">All</button>
                <button>Important</button>
                <button>General</button>
                <button>Events</button>
            </div>
        </div>

        <div class="notices-list">
            <div class="notice-card important">
                <div class="notice-header">
                    <div>
                        <div class="notice-title">🚨 Mandatory Team Meeting</div>
                        <div class="notice-date">August 20, 2025 • 10:00 AM</div>
                    </div>
                    <div class="notice-badge important">Important</div>
                </div>
                <div class="notice-content">
                    All team members are required to attend the strategy meeting this Friday at 10 AM in the main conference room. We'll be discussing our game plan for the upcoming championship match against Old Bends. Please be punctual and come prepared with your questions.
                </div>
            </div>

            <div class="notice-card event">
                <div class="notice-header">
                    <div>
                        <div class="notice-title">⚽ Championship Match Schedule Released</div>
                        <div class="notice-date">August 18, 2025 • 2:30 PM</div>
                    </div>
                    <div class="notice-badge event">Event</div>
                </div>
                <div class="notice-content">
                    The SLUG Championship tournament schedule has been officially released. Our first match is scheduled for September 15th at 3:00 PM at the University Ground. Please ensure you're available and have arranged your schedules accordingly. Training intensity will be increased starting next week.
                </div>
            </div>

            <div class="notice-card">
                <div class="notice-header">
                    <div>
                        <div class="notice-title">🏋️ New Fitness Training Program</div>
                        <div class="notice-date">August 16, 2025 • 9:00 AM</div>
                    </div>
                    <div class="notice-badge general">General</div>
                </div>
                <div class="notice-content">
                    We're implementing a new fitness training program designed by our sports physiologist. The program focuses on improving stamina and reducing injury risk. All players will receive personalized workout plans. First session starts Monday at 6:00 AM.
                </div>
            </div>

            <div class="notice-card">
                <div class="notice-header">
                    <div>
                        <div class="notice-title">📋 Updated Meal Plan Available</div>
                        <div class="notice-date">August 15, 2025 • 7:00 PM</div>
                    </div>
                    <div class="notice-badge general">General</div>
                </div>
                <div class="notice-content">
                    Our nutritionist has updated the team meal plan with new protein-rich options and better portion guidelines. Check the Meal Plan section for details. Please follow the plan strictly to optimize your performance and recovery.
                </div>
            </div>

            <div class="notice-card important">
                <div class="notice-header">
                    <div>
                        <div class="notice-title">🩺 Mandatory Health Checkups</div>
                        <div class="notice-date">August 14, 2025 • 11:00 AM</div>
                    </div>
                    <div class="notice-badge important">Important</div>
                </div>
                <div class="notice-content">
                    Annual health checkups for all team members are scheduled for next week. This includes cardiovascular assessment, flexibility tests, and injury screening. Please coordinate with the medical staff to book your slot. This is mandatory for all players.
                </div>
            </div>

            <div class="notice-card event">
                <div class="notice-header">
                    <div>
                        <div class="notice-title">🎉 Team Bonding Event - August 25th</div>
                        <div class="notice-date">August 12, 2025 • 3:00 PM</div>
                    </div>
                    <div class="notice-badge event">Event</div>
                </div>
                <div class="notice-content">
                    Join us for a team bonding barbecue on August 25th at 5:00 PM at the university recreation area. This is a great opportunity to relax and strengthen team chemistry before the intense championship preparation begins. Family members are welcome!
                </div>
            </div>

            <div class="notice-card">
                <div class="notice-header">
                    <div>
                        <div class="notice-title">👕 New Team Jerseys Arrived</div>
                        <div class="notice-date">August 10, 2025 • 1:00 PM</div>
                    </div>
                    <div class="notice-badge general">General</div>
                </div>
                <div class="notice-content">
                    The new team jerseys have arrived! Please collect yours from the equipment room this week. Make sure to try them on and report any sizing issues immediately. We'll be wearing these for all championship matches.
                </div>
            </div>

            <div class="notice-card">
                <div class="notice-header">
                    <div>
                        <div class="notice-title">📸 Team Photo Session - August 22nd</div>
                        <div class="notice-date">August 9, 2025 • 10:00 AM</div>
                    </div>
                    <div class="notice-badge event">Event</div>
                </div>
                <div class="notice-content">
                    Official team photos will be taken on August 22nd at 9:00 AM. Please wear your new jerseys and arrive 15 minutes early. These photos will be used for the championship program and team website.
                </div>
            </div>
        </div>
    </div>

    <!-- Meal Plan Page -->
    <div id="meal-plan" class="page">
        <h2 style="margin-bottom: 30px; color: #4a1a5c;">Nutrition & Meal Planning</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="card">
                <h3>🍳 Breakfast Plan</h3>
                <div style="margin-top: 20px;">
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Carbohydrates</strong>
                        <p style="margin-top: 8px; color: #666;">Basmati or Red rice, Whole grain bread, Oatmeal</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Proteins</strong>
                        <p style="margin-top: 8px; color: #666;">Eggs (2-3), Chicken breast, Greek yogurt</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Vegetables & Fruits</strong>
                        <p style="margin-top: 8px; color: #666;">Banana, Apple, Spinach, Tomatoes</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                        <strong style="color: #4a1a5c;">Hydration</strong>
                        <p style="margin-top: 8px; color: #666;">Fresh juice, Milk, Water (500ml)</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>🍽️ Lunch Plan</h3>
                <div style="margin-top: 20px;">
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Main Course</strong>
                        <p style="margin-top: 8px; color: #666;">Basmati or Red rice (200g), Chicken curry or Fish (150g)</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Vegetables</strong>
                        <p style="margin-top: 8px; color: #666;">Minimum 3 varieties - Beans, Carrots, Broccoli, Beetroot</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Side Dishes</strong>
                        <p style="margin-top: 8px; color: #666;">Dhal curry (Parippu), Salad, Pala</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                        <strong style="color: #4a1a5c;">Dairy</strong>
                        <p style="margin-top: 8px; color: #666;">Yogurt (150g), Buttermilk</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="card">
                <h3>🌙 Dinner Plan</h3>
                <div style="margin-top: 20px;">
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Light Carbs</strong>
                        <p style="margin-top: 8px; color: #666;">Red rice (150g) or Wheat bread (2 pieces)</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Lean Proteins</strong>
                        <p style="margin-top: 8px; color: #666;">Grilled fish, Chicken breast, Egg whites</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Light Vegetables</strong>
                        <p style="margin-top: 8px; color: #666;">Steamed vegetables, Green salad, Soup</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                        <strong style="color: #4a1a5c;">Evening Hydration</strong>
                        <p style="margin-top: 8px; color: #666;">Herbal tea, Warm milk</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>🥤 Snacks & Supplements</h3>
                <div style="margin-top: 20px;">
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Pre-Workout (30 mins before)</strong>
                        <p style="margin-top: 8px; color: #666;">Banana, Energy bar, Sports drink</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Post-Workout (within 30 mins)</strong>
                        <p style="margin-top: 8px; color: #666;">Protein shake, Chocolate milk, Recovery drink</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; margin-bottom: 10px;">
                        <strong style="color: #4a1a5c;">Healthy Snacks</strong>
                        <p style="margin-top: 8px; color: #666;">Nuts, Fruits, Protein bars, Yogurt</p>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                        <strong style="color: #4a1a5c;">Daily Supplements</strong>
                        <p style="margin-top: 8px; color: #666;">Multivitamin, Omega-3, Vitamin D</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>📊 Nutritional Guidelines & Tips</h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
                <div style="padding: 20px; background: #e3f2fd; border-radius: 10px;">
                    <h4 style="color: #1976d2; margin-bottom: 10px;">💧 Hydration</h4>
                    <p style="color: #666; font-size: 14px;">Drink minimum 3-4 liters of water daily. Increase intake during training sessions.</p>
                </div>
                <div style="padding: 20px; background: #e8f5e9; border-radius: 10px;">
                    <h4 style="color: #388e3c; margin-bottom: 10px;">⏰ Timing</h4>
                    <p style="color: #666; font-size: 14px;">Eat every 3-4 hours. Never skip breakfast. Avoid heavy meals 2 hours before practice.</p>
                </div>
                <div style="padding: 20px; background: #fff3e0; border-radius: 10px;">
                    <h4 style="color: #f57c00; margin-bottom: 10px;">🚫 Avoid</h4>
                    <p style="color: #666; font-size: 14px;">Fast food, sugary drinks, processed foods, excessive caffeine.</p>
                </div>
            </div>
            
            <div style="margin-top: 25px; padding: 20px; background: #f3e5f5; border-radius: 10px; border-left: 5px solid #4a1a5c;">
                <h4 style="color: #4a1a5c; margin-bottom: 10px;">🎯 Performance Goals</h4>
                <ul style="color: #666; margin-left: 20px; line-height: 1.8;">
                    <li>Maintain body fat percentage between 8-12%</li>
                    <li>Consume 1.6-2.2g protein per kg body weight daily</li>
                    <li>Complex carbs should make up 50-60% of total calories</li>
                    <li>Eat within 30 minutes post-workout for optimal recovery</li>
                    <li>Get adequate sleep (7-9 hours) for muscle recovery</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Page Navigation
        function showPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page').forEach(page => {
                page.classList.remove('active');
            });
            
            // Show selected page
            document.getElementById(pageId).classList.add('active');
            
            // Update navigation
            document.querySelectorAll('.nav-menu button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Initialize charts if analyzing page
            if (pageId === 'analyze') {
                setTimeout(initializeCharts, 100);
            }
        }

        // Update Date and Time
        function updateDateTime() {
            const now = new Date();
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const date = now.toLocaleDateString('en-US', options);
            const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            
            document.getElementById('current-date').textContent = date;
            document.getElementById('current-time').textContent = time;
        }

        // Initialize Charts
        function initializeCharts() {
            // Line Chart
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx && lineCtx.getContext) {
                const ctx = lineCtx.getContext('2d');
                const width = lineCtx.offsetWidth;
                const height = 250;
                lineCtx.width = width;
                lineCtx.height = height;

                // Draw grid
                ctx.strokeStyle = '#f0f0f0';
                ctx.lineWidth = 1;
                for (let i = 0; i <= 5; i++) {
                    ctx.beginPath();
                    ctx.moveTo(0, (height / 5) * i);
                    ctx.lineTo(width, (height / 5) * i);
                    ctx.stroke();
                }

                // Draw Fitness line
                ctx.strokeStyle = '#8b5cf6';
                ctx.lineWidth = 3;
                ctx.beginPath();
                const fitnessPoints = [
                    [width * 0.1, height * 0.7],
                    [width * 0.3, height * 0.5],
                    [width * 0.5, height * 0.6],
                    [width * 0.7, height * 0.4],
                    [width * 0.9, height * 0.3]
                ];
                ctx.moveTo(fitnessPoints[0][0], fitnessPoints[0][1]);
                fitnessPoints.forEach(point => ctx.lineTo(point[0], point[1]));
                ctx.stroke();

                // Draw Stamina line
                ctx.strokeStyle = '#14b8a6';
                ctx.lineWidth = 3;
                ctx.beginPath();
                const staminaPoints = [
                    [width * 0.1, height * 0.8],
                    [width * 0.3, height * 0.65],
                    [width * 0.5, height * 0.55],
                    [width * 0.7, height * 0.45],
                    [width * 0.9, height * 0.35]
                ];
                ctx.moveTo(staminaPoints[0][0], staminaPoints[0][1]);
                staminaPoints.forEach(point => ctx.lineTo(point[0], point[1]));
                ctx.stroke();
            }

            // Pie Chart
            const pieCtx = document.getElementById('pieChart');
            if (pieCtx && pieCtx.getContext) {
                const ctx = pieCtx.getContext('2d');
                const centerX = 225;
                const centerY = 110;
                const radius = 80;

                // Draw pie segments
                let currentAngle = -0.5 * Math.PI;
                
                // Wins (60%)
                ctx.fillStyle = '#10b981';
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + (Math.PI * 2 * 0.6));
                ctx.closePath();
                ctx.fill();
                currentAngle += Math.PI * 2 * 0.6;

                // Draws (25%)
                ctx.fillStyle = '#fbbf24';
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + (Math.PI * 2 * 0.25));
                ctx.closePath();
                ctx.fill();
                currentAngle += Math.PI * 2 * 0.25;

                // Losses (15%)
                ctx.fillStyle = '#ef4444';
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + (Math.PI * 2 * 0.15));
                ctx.closePath();
                ctx.fill();

                // Add labels
                ctx.fillStyle = '#333';
                ctx.font = 'bold 13px Arial';
                ctx.textAlign = 'left';
                ctx.fillText('Wins', centerX + 95, centerY);
                ctx.textAlign = 'right';
                ctx.fillText('Draws', centerX - 95, centerY + 50);
                ctx.fillText('Losses', centerX - 95, centerY - 50);
            }
        }

        // Initialize
        updateDateTime();
        setInterval(updateDateTime, 60000); // Update every minute

        // Meal Tab Switching
        document.querySelectorAll('.meal-tabs button').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.querySelectorAll('button').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Logout button handler
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                const ok = confirm('Are you sure you want to logout?');
                if (!ok) return;

                // Clear a common auth key if present and any other session data
                try {
                    localStorage.removeItem('authToken');
                    // add other cleanup here if needed
                } catch (e) {
                    // ignore
                }

                // Redirect to landing page
                window.location.href = '<?php echo ROOT; ?>/LandingPage';
            });
        }
    </script>
</body>
</html>