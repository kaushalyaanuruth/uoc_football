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
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo ROOT; ?>/uploads/players/notification.png" alt="Notifications"
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
                    <button class="meal-tab active" onclick="switchMeal('Breakfast', this)">☕ Breakfast</button>
                    <button class="meal-tab" onclick="switchMeal('Lunch', this)">🍲 Lunch</button>
                    <button class="meal-tab" onclick="switchMeal('Dinner', this)">🍴 Dinner</button>
                </div>

                <!-- Daily Grid -->
                <div class="days-grid" id="daysGrid">
                    <!-- Cards will be populated by JavaScript -->
                    <?php
                    // Initial render for Breakfast
                    foreach ($data['meal_data']['Breakfast'] as $day => $plan): ?>
                        <div class="day-card">
                            <div class="day-header">
                                <h4><?php echo $day; ?></h4>
                                <span class="calorie-badge"><?php echo $plan['calories']; ?> cal</span>
                            </div>
                            <ul class="meal-items">
                                <?php foreach ($plan['items'] as $item): ?>
                                    <li><span class="bullet"></span><?php echo $item; ?></li>
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
                    <div class="hydration-box">
                        <strong>Hydration Tip</strong><br>
                        Drink 3-4 liters of water daily. Increase intake during training sessions.
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Overlay -->
        <div class="notification-overlay" id="notificationOverlay" style="display: none;">
            <p style="font-size: 0.95rem; color: #333; font-weight: 600; margin-bottom: 12px;">Meal Notifications</p>
            <div style="padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.05);">
                <p style="font-size: 0.85rem; color: #555; line-height: 1.5;">
                    Your personalized meal plan has been updated for the upcoming week based on your fitness goals.
                </p>
            </div>
        </div>
    </div>

    <script>
        const mealData = <?php echo json_encode($data['meal_data']); ?>;

        // Notification Toggle
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

        // Tab Switching Logic
        function switchMeal(category, btn) {
            document.querySelectorAll('.meal-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            const grid = document.getElementById('daysGrid');
            grid.innerHTML = '';

            const selectedData = mealData[category];
            Object.keys(selectedData).forEach((day, index) => {
                const plan = selectedData[day];
                const card = document.createElement('div');
                card.className = 'day-card';
                card.style.animationDelay = `${index * 0.05}s`;

                let itemsHtml = '';
                plan.items.forEach(item => {
                    itemsHtml += `<li><span class="bullet"></span>${item}</li>`;
                });

                card.innerHTML = `
                    <div class="day-header">
                        <h4>${day}</h4>
                        <span class="calorie-badge">${plan.calories} cal</span>
                    </div>
                    <ul class="meal-items">
                        ${itemsHtml}
                    </ul>
                `;
                grid.appendChild(card);
            });
        }
    </script>
</body>

</html>