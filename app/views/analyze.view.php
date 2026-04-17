<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyze Performance - UOC Football</title>
    <?php
    $base = rtrim(ROOT, '/');
    $cssFile = __DIR__ . '/../../public/assets/css/analyze.css';
    $cssVersion = file_exists($cssFile) ? filemtime($cssFile) : time();
    $commonFile = __DIR__ . '/../../public/assets/css/playerCommon.css';
    $commonVersion = file_exists($commonFile) ? filemtime($commonFile) : time();
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/analyze.css?v=<?php echo $cssVersion; ?>">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerCommon.css?v=<?php echo $commonVersion; ?>">
</head>
<body class="analyze-page">
    <div class="analyze-container">
        <header class="player-header analyze-header">
            <div class="logo-section">
                <img src="<?php echo $base; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>

            <nav class="nav-links">
                <a href="<?php echo $base; ?>/PlayerDashboard">Home</a>
                <a href="<?php echo $base; ?>/Schedule">Schedule</a>
                <a href="<?php echo $base; ?>/Analyze" class="active">Analyze</a>
                <a href="<?php echo $base; ?>/Notices">Notices</a>
                <a href="<?php echo $base; ?>/MealPlan">Meal Plan</a>
            </nav>

            <div class="user-section">
                <button class="notification-icon" id="notificationBell" type="button" aria-label="Show notifications">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications">
                </button>
                <div class="user-profile">
                    <img src="<?php echo $base; ?>/assets/images/user-placeholder.jpg" alt="User Profile">
                </div>
            </div>
        </header>

        <main class="analyze-main">
            <section class="section-card overview-card">
                <h2 class="section-title">Performance Overview</h2>

                <div class="overview-grid">
                    <?php foreach ($data['performance'] as $item): ?>
                        <article class="overview-item">
                            <div class="progress-circle">
                                <svg width="110" height="110" viewBox="0 0 110 110" aria-hidden="true">
                                    <circle cx="55" cy="55" r="45" class="circle-bg"></circle>
                                    <circle
                                        cx="55"
                                        cy="55"
                                        r="45"
                                        class="circle-value"
                                        style="
                                            stroke: <?php echo $item['color']; ?>;
                                            stroke-dasharray: <?php echo (2 * pi() * 45); ?>;
                                            stroke-dashoffset: <?php echo (2 * pi() * 45 * (1 - $item['value'] / 100)); ?>;
                                        "
                                    ></circle>
                                </svg>
                                <span class="progress-value"><?php echo $item['value']; ?>%</span>
                            </div>
                            <h3><?php echo $item['label']; ?></h3>
                            <p><?php echo $item['status']; ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="mid-grid">
                <section class="section-card trends-card">
                    <h2 class="section-title">Fitness Test Trends</h2>

                    <div class="chart-shell">
                        <svg class="trend-svg" viewBox="0 0 600 200" aria-label="Performance trend chart">
                            <path d="M50,150 L150,140 L250,120 L350,110 L450,90 L550,80" fill="none" stroke="#a29bfe" stroke-width="4" stroke-linecap="round"></path>
                            <path d="M50,165 L150,160 L250,145 L350,135 L450,120 L550,110" fill="none" stroke="#00b894" stroke-width="4" stroke-linecap="round"></path>
                            <path d="M50,175 L150,170 L250,160 L350,150 L450,135 L550,125" fill="none" stroke="#fdcb6e" stroke-width="4" stroke-linecap="round"></path>
                        </svg>

                        <div class="chart-labels">
                            <?php foreach ($data['trends']['labels'] as $label): ?>
                                <span><?php echo $label; ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="chart-legend">
                            <?php foreach ($data['trends']['datasets'] as $set): ?>
                                <span class="legend-item">
                                    <span class="legend-dot" style="background: <?php echo $set['color']; ?>;"></span>
                                    <?php echo $set['label']; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <section class="section-card comparison-card">
                    <h2 class="section-title">Monthly Comparison</h2>

                    <div class="comparison-list">
                        <?php foreach ($data['comparison'] as $comp): ?>
                            <article class="comp-item">
                                <div class="comp-left">
                                    <div class="comp-icon"><?php echo strtoupper(substr($comp['label'], 0, 1)); ?></div>
                                    <div class="comp-info">
                                        <h5><?php echo $comp['label']; ?></h5>
                                        <p>Progress vs Last Month</p>
                                    </div>
                                </div>
                                <div class="comp-right">
                                    <span class="percent <?php echo $comp['type'] === 'up' ? 'up' : 'down'; ?>"><?php echo $comp['value']; ?></span>
                                    <span class="sub"><?php echo $comp['sub']; ?></span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <section class="season-section">
                <h2 class="section-title">Season Statistics</h2>
                <div class="stats-grid">
                    <?php foreach ($data['stats'] as $stat): ?>
                        <article class="stat-card">
                            <div class="stat-icon" style="background: <?php echo $stat['color']; ?>;">
                                <?php echo $stat['icon']; ?>
                            </div>
                            <h3><?php echo $stat['value']; ?></h3>
                            <p><?php echo $stat['label']; ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>

        <div class="notification-overlay" id="notificationOverlay" hidden>
            <p class="notify-title">Notifications</p>
            <div class="notify-item">
                <p>New fitness test results have been uploaded. Your speed has improved by 4%.</p>
                <span>2 hours ago</span>
            </div>
        </div>
    </div>

    <script>
        const bell = document.getElementById('notificationBell');
        const overlay = document.getElementById('notificationOverlay');

        bell.addEventListener('click', function (e) {
            e.stopPropagation();
            overlay.hidden = !overlay.hidden;
        });

        document.addEventListener('click', function (e) {
            if (!overlay.contains(e.target) && e.target !== bell) {
                overlay.hidden = true;
            }
        });
    </script>
</body>
</html>
