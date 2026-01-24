<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Meal Plan - UOC Football</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/mealPlan.css">
</head>

<body>
    <div class="mealplan-container">
        <!-- Header -->
        <header>
            <div class="logo-section">
                <img src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo ROOT; ?>/PlayerDashboard">Home</a>
                <a href="<?php echo ROOT; ?>/Schedule">Schedule</a>
                <a href="<?php echo ROOT; ?>/Analyze">Analyze</a>
                <a href="<?php echo ROOT; ?>/Notices">Notices</a>
                <a href="#" class="active">Meal Plan</a>
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

        <div class="page-title-section">
            <h1>Weekly Meal Plan</h1>
            <p>Follow your personalized nutrition schedule for optimal performance</p>
        </div>

        <div class="mealplan-grid">
            <div class="main-content">
                <!-- Meal Tabs -->
                <div class="meal-tabs">
                    <button class="meal-tab <?php echo $data['selected_category'] == 'Breakfast' ? 'active' : ''; ?>">☕
                        Breakfast</button>
                    <button class="meal-tab">🍲 Lunch</button>
                    <button class="meal-tab">🍲 Dinner</button>
                </div>

                <!-- Daily Grid -->
                <div class="days-grid">
                    <?php foreach ($data['daily_plans'] as $day => $plan): ?>
                        <div class="day-card">
                            <div class="day-header">
                                <h4>
                                    <?php echo $day; ?>
                                </h4>
                                <span class="calorie-badge">
                                    <?php echo $plan['calories']; ?> cal
                                </span>
                            </div>
                            <ul class="meal-items">
                                <?php foreach ($plan['items'] as $item): ?>
                                    <li><span class="bullet" style="background: <?php
                                    echo $day == 'Monday' ? '#a29bfe' :
                                        ($day == 'Tuesday' ? '#0984e3' :
                                            ($day == 'Wednesday' ? '#00b894' :
                                                ($day == 'Thursday' ? '#e17055' :
                                                    ($day == 'Friday' ? '#fdcb6e' : '#6c5ce7'))));
                                    ?>;"></span>
                                        <?php echo $item; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="meal-sidebar">
                <div class="sidebar-card">
                    <h3>🌍 Daily Nutrition</h3>
                    <div class="nutrition-list">
                        <?php foreach ($data['nutrition'] as $label => $value): ?>
                            <div class="nutrition-row">
                                <span class="label">
                                    <?php echo $label; ?>
                                </span>
                                <span class="value">
                                    <?php echo $value; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="hydration-box">
                        <strong>Hydration Tip</strong><br>
                        Drink 3-4 liters of water daily. Increase intake during training sessions.
                    </div>
                </div>

                <div class="sidebar-card">
                    <h3>📣 Coach Updates</h3>
                    <?php foreach ($data['updates'] as $update): ?>
                        <div class="update-item <?php echo $update['type'] == 'update' ? 'warning' : 'success'; ?>">
                            <div class="update-title">
                                <?php echo $update['type'] == 'update' ? '⚠️' : '✅'; ?>
                                <?php echo $update['title']; ?>
                            </div>
                            <div class="update-content">
                                <?php echo $update['content']; ?>
                            </div>
                            <div class="update-time">
                                <?php echo $update['time']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>