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
                <div class="notification-icon">
                    <img src="<?php echo ROOT; ?>/assets/images/notification-bell.png" alt="Notifications"
                        style="width: 24px; cursor: pointer;">
                </div>
                <div class="user-profile">
                    <img src="<?php echo ROOT; ?>/assets/images/user-placeholder.jpg" alt="User Profile">
                </div>
            </div>
        </header>

        <!-- Performance Overview -->
        <div class="overview-card">
            <h3 style="margin-bottom: 20px;">Performance Overview</h3>
            <div class="overview-grid">
                <?php foreach ($data['performance'] as $item): ?>
                    <div class="overview-item">
                        <div class="progress-circle">
                            <svg width="100" height="100">
                                <circle cx="50" cy="50" r="40" stroke="#f0f0f0" stroke-width="8" fill="none" />
                                <circle cx="50" cy="50" r="40" stroke="<?php echo $item['color']; ?>" stroke-width="8"
                                    fill="none" stroke-dasharray="<?php echo (2 * pi() * 40); ?>"
                                    stroke-dashoffset="<?php echo (2 * pi() * 40 * (1 - $item['value'] / 100)); ?>"
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
                <h3 style="margin-bottom: 20px;">Fitness Test Trends</h3>
                <div style="height: 250px; background: #fafafa; border-radius: 12px; padding: 20px;">
                    <!-- Line Chart SVG Placeholder -->
                    <svg width="100%" height="100%" viewBox="0 0 600 200">
                        <path d="M50,150 L150,140 L250,120 L350,110 L450,90 L550,80" fill="none" stroke="#a29bfe"
                            stroke-width="3" />
                        <path d="M50,165 L150,160 L250,145 L350,135 L450,120 L550,110" fill="none" stroke="#00b894"
                            stroke-width="3" />
                        <path d="M50,175 L150,170 L250,160 L350,150 L450,135 L550,125" fill="none" stroke="#fdcb6e"
                            stroke-width="3" />
                    </svg>
                    <div
                        style="display: flex; justify-content: space-between; padding: 0 50px; color: #999; font-size: 0.8rem;">
                        <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span>
                    </div>
                    <div
                        style="display: flex; justify-content: center; gap: 20px; margin-top: 15px; font-size: 0.8rem;">
                        <span style="display: flex; align-items: center; gap: 5px;"><strong
                                style="color:#a29bfe;">●</strong> Endurance</span>
                        <span style="display: flex; align-items: center; gap: 5px;"><strong
                                style="color:#00b894;">●</strong> Sprint Speed</span>
                        <span style="display: flex; align-items: center; gap: 5px;"><strong
                                style="color:#fdcb6e;">●</strong> Agility</span>
                    </div>
                </div>
            </div>

            <!-- Monthly Comparison -->
            <div class="comparison-card">
                <h3 style="margin-bottom: 20px;">Monthly Comparison</h3>
                <?php foreach ($data['comparison'] as $comp): ?>
                    <div class="comp-item">
                        <div class="comp-left">
                            <div class="comp-icon"
                                style="background: <?php echo $comp['label'] == 'Goals Scored' ? '#dff9fb' : ($comp['label'] == 'Minutes Played' ? '#e0e0ff' : '#fff9e6'); ?>;">
                                <span style="font-size: 1.2rem;">
                                    <?php echo $comp['label'] == 'Goals Scored' ? '⬆️' : ($comp['label'] == 'Minutes Played' ? '🕒' : '🏃'); ?>
                                </span>
                            </div>
                            <div class="comp-info">
                                <h5>
                                    <?php echo $comp['label']; ?>
                                </h5>
                                <p>This month vs last month</p>
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
        <h3 style="margin-bottom: 20px; font-size: 1.5rem;">Match Performance Stats</h3>
        <div class="stats-grid">
            <?php foreach ($data['stats'] as $stat): ?>
                <div class="stat-card" style="background: <?php echo $stat['color']; ?>10;">
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
    </div>
</body>

</html>