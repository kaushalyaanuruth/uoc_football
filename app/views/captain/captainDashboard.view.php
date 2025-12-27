<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>uoc_football</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/captainDashboard.css">
</head>

<body>


    <!-- ================= TOP NAVBAR ================= -->
    <header class="top-navbar">
        <div class="nav-left">
            <a href="<?php echo ROOT; ?>/captain/dashboard">
                <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png"
                    alt="UOC Football Logo">
            </a>
        </div>

        <nav class="nav-center">
            <a href="#" class="active">Home</a>
            <a href="#">Schedule</a>
            <a href="#">Analytics</a>
            <a href="<?= ROOT ?>/CaptainAttendance">Attendance</a>
            <a href="#">Inventory</a>
            <a href="#">Notices</a>
        </nav>
            <div class="nav-right">
                <button class="icon-btn">🔔</button>
                <div class="profile">
                    <img class="avatar" src="<?php echo ROOT; ?>../assets/images/adminDashboard/header/avatar.jpg"
                        alt="Admin Avatar">

                </div>
            </div>
    </header>

    <main class="content">

        <div class="container">

            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h1>Welcome back, <?= $captain_name ?? 'Captain' ?>!</h1>
                    <p class="welcome-date">
                        <?= date('l, jS F Y') ?>
                    </p>
                </div>
            </div>

            <!-- Dashboard Cards -->
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

            <!-- Sidebar Section -->
            <div class="sidebar-container">

                <div class="announcements">
                    <h3>📢 Latest Announcements</h3>

                    <div class="announcement-item">
                        <h4>Team Meeting This Friday</h4>
                        <p>Don't forget about our strategy meeting at 5 PM.</p>
                    </div>

                    <div class="announcement-item">
                        <h4>New Training Equipment</h4>
                        <p>New training equipment available at the facility.</p>
                    </div>
                </div>

                <div class="quick-links">
                    <h3>🔗 Quick Links</h3>

                    <div class="link-item">
                        <span class="link-icon">🍽️</span>
                        <span>Meal Plan</span>
                    </div>

                    <div class="link-item">
                        <span class="link-icon">📊</span>
                        <span>Performance Stats</span>
                    </div>
                </div>

            </div>

        </div>
    </main>
</body>

</html>