<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyze Performance - UOC Football</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/analyze.css">
</head>

<body>
    <div class="analyze-container">
        <!-- Header -->
        <header>
            <div class="logo-section">
                <img src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo ROOT; ?>/PlayerDashboard">Home</a>
                <a href="<?php echo ROOT; ?>/Schedule">Schedule</a>
                <a href="#" class="active">Analyze</a>
                <a href="<?php echo ROOT; ?>/Notices">Notices</a>
                <a href="<?php echo ROOT; ?>/MealPlan">Meal Plan</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo ROOT; ?>/uploads/players/notification.png" alt="Notifications"
                        style="width: 24px; cursor: pointer;">
                </div>
                <div class="user-profile">
                    <img src="<?php echo ROOT; ?>/assets/images/user-placeholder.jpg" alt="User Profile">
                </div>
            </div>
        </header>

        <!-- Performance Overview -->
        <div class="overview-card">
            <h3 style="margin-bottom: 15px; color: var(--primary-color);">Performance Overview</h3>
            <div class="overview-grid">
                <?php foreach ($data['performance'] as $index => $item): ?>
                    <div class="overview-item" style="transition-delay: <?php echo $index * 0.1; ?>s">
                        <div class="progress-circle">
                            <svg width="110" height="110">
                                <circle cx="55" cy="55" r="45" stroke="rgba(0,0,0,0.05)" stroke-width="10" fill="none" />
                                <circle cx="55" cy="55" r="45" stroke="<?php echo $item['color']; ?>" stroke-width="10"
                                    fill="none" stroke-dasharray="<?php echo (2 * pi() * 45); ?>"
                                    stroke-dashoffset="<?php echo (2 * pi() * 45 * (1 - $item['value'] / 100)); ?>"
                                    stroke-linecap="round" />
                            </svg>
                            <span class="value">
                                <?php echo $item['value']; ?>%
                            </span>
                        </div>
                        <h4>
                            <?php echo $item['label']; ?>
                        </h4>
                        <p>
                            <?php echo $item['status']; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mid-grid">
            <!-- Fitness Test Trends -->
            <div class="trends-card">
                <h3 style="margin-bottom: 25px; color: var(--primary-color);">Fitness Test Trends</h3>
                <div
                    style="height: 250px; background: rgba(255,255,255,0.4); border-radius: 16px; padding: 25px; border: 1px solid rgba(0,0,0,0.03);">
                    <!-- Line Chart SVG Placeholder -->
                    <svg width="100%" height="100%" viewBox="0 0 600 200">
                        <path d="M50,150 L150,140 L250,120 L350,110 L450,90 L550,80" fill="none" stroke="#a29bfe"
                            stroke-width="4" stroke-linecap="round" />
                        <path d="M50,165 L150,160 L250,145 L350,135 L450,120 L550,110" fill="none" stroke="#00b894"
                            stroke-width="4" stroke-linecap="round" />
                        <path d="M50,175 L150,170 L250,160 L350,150 L450,135 L550,125" fill="none" stroke="#fdcb6e"
                            stroke-width="4" stroke-linecap="round" />
                    </svg>
                    <div
                        style="display: flex; justify-content: space-between; padding: 0 40px; color: #999; font-size: 0.8rem; margin-top: 10px;">
                        <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span>
                    </div>
                    <div
                        style="display: flex; justify-content: center; gap: 25px; margin-top: 20px; font-size: 0.85rem; font-weight: 500;">
                        <span style="display: flex; align-items: center; gap: 8px;"><strong
                                style="color:#a29bfe; font-size: 1.2rem;">●</strong> Endurance</span>
                        <span style="display: flex; align-items: center; gap: 8px;"><strong
                                style="color:#00b894; font-size: 1.2rem;">●</strong> Sprint Speed</span>
                        <span style="display: flex; align-items: center; gap: 8px;"><strong
                                style="color:#fdcb6e; font-size: 1.2rem;">●</strong> Agility</span>
                    </div>
                </div>
            </div>

            <!-- Monthly Comparison -->
            <div class="comparison-card">
                <h3 style="margin-bottom: 25px; color: var(--primary-color);">Monthly Comparison</h3>
                <?php foreach ($data['comparison'] as $comp): ?>
                    <div class="comp-item">
                        <div class="comp-left">
                            <div class="comp-icon"
                                style="background: <?php echo $comp['label'] == 'Goals Scored' ? '#e0fcfc' : ($comp['label'] == 'Minutes Played' ? '#f0f0ff' : '#fffbeb'); ?>;">
                                <span>
                                    <?php echo $comp['label'] == 'Goals Scored' ? '⚽' : ($comp['label'] == 'Minutes Played' ? '🕒' : '🏃'); ?>
                                </span>
                            </div>
                            <div class="comp-info">
                                <h5>
                                    <?php echo $comp['label']; ?>
                                </h5>
                                <p>Progress vs Last Month</p>
                            </div>
                        </div>
                        <div class="comp-right">
                            <span class="percent"
                                style="color: <?php echo $comp['type'] == 'up' ? '#00b894' : '#e17055'; ?>;">
                                <?php echo $comp['value']; ?>
                            </span>
                            <div class="sub">
                                <?php echo $comp['sub']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Match Performance Stats -->
        <h3 style="margin: 40px 0 25px; font-size: 1.6rem; color: var(--primary-color); padding-left: 5px;">Season
            Statistics</h3>
        <div class="stats-grid">
            <?php foreach ($data['stats'] as $index => $stat): ?>
                <div class="stat-card" style="animation-delay: <?php echo 0.4 + ($index * 0.1); ?>s">
                    <div class="stat-icon" style="background: <?php echo $stat['color']; ?>;">
                        <?php echo $stat['icon']; ?>
                    </div>
                    <h2>
                        <?php echo $stat['value']; ?>
                    </h2>
                    <p>
                        <?php echo $stat['label']; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Notification Overlay -->
        <div class="notification-overlay" id="notificationOverlay" style="display: none;">
            <p style="font-size: 0.95rem; color: #333; font-weight: 600; margin-bottom: 12px;">Notifications</p>
            <div style="padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.05);">
                <p style="font-size: 0.85rem; color: #555; line-height: 1.5;">
                    New fitness test results have been uploaded. Your speed has improved by 4%! 🏃‍♂️
                </p>
                <p style="font-size: 0.75rem; color: #999; margin-top: 8px;">2 hours ago</p>
            </div>
        </div>
    </div>

    <script>
        // Notification Toggle (Consistent with other pages)
        const bell = document.getElementById('notificationBell');
        const overlay = document.getElementById('notificationOverlay');

        bell.addEventListener('click', (e) => {
            e.stopPropagation();
            overlay.style.display = overlay.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!overlay.contains(e.target) && e.target !== bell) {
                overlay.style.display = 'none';
            }
        });
    </script>
</body>

</html>