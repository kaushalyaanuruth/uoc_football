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
            <a href="<?php echo ROOT; ?>/captainDashboard">
                <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png"
                    alt="UOC Football Logo">
            </a>
        </div>

        <nav class="nav-center">
            <a href="#" class="active">Home</a>
            <a href="<?= ROOT ?>/dashboard">Schedule</a>
            <a href="<?= ROOT ?>/CaptainAnalyze">Analyze</a>
            <a href="<?= ROOT ?>/CaptainAttendance">Attendance</a> <a href="<?= ROOT ?>/CaptainInventory">Inventory</a>
            <a href="<?= ROOT ?>/CaptainFinance">Finance</a>
        </nav>
        <div class="nav-right">
            <button class="icon-btn" id="captainNotificationBell">🔔</button>
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
                    <?php foreach (array_slice($data['notices'] ?? [], 0, 3) as $notice): ?>
                        <div class="announcement-item">
                            <h4><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></h4>
                            <p><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="quick-links">
                    <h3>🔗 Quick Links</h3>

                    <a href="<?= ROOT ?>/CaptainMealPlan" class="link-item">
                        <!-- <div class="link-item" > -->
                        <span class="link-icon">🍽️</span>
                        <span>Meal Plan</span>
                    </a>

                    <a href="<?= ROOT ?>/CaptainAnalyze" class="link-item">
                        <span class="link-icon">📊</span>
                        <span>Performance Stats</span>
                    </a>
                </div>

            </div>

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
        const captainBell = document.getElementById('captainNotificationBell');
        const captainOverlay = document.getElementById('captainNotificationOverlay');

        captainBell.addEventListener('click', (e) => {
            e.stopPropagation();
            captainOverlay.style.display = captainOverlay.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!captainOverlay.contains(e.target) && e.target !== captainBell) {
                captainOverlay.style.display = 'none';
            }
        });
    </script>
</body>

</html>