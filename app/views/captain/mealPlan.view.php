<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captain Meal Plan - UOC Football</title>
    <?php
    $base = rtrim(ROOT, '/');
    $commonFile = __DIR__ . '/../../../public/assets/css/playerCommon.css';
    $commonVersion = file_exists($commonFile) ? filemtime($commonFile) : time();
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/mealPlan.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerCommon.css?v=<?php echo $commonVersion; ?>">
</head>
<body>
    <div class="dashboard-container">
        <header class="player-header">
            <div class="logo-section">
                <img src="<?php echo $base; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo $base; ?>/captainDashboard">Home</a>
                <a href="<?php echo $base; ?>/CaptainSchedule">Schedule</a>
                <a href="<?php echo $base; ?>/CaptainAnalyze">Analyze</a>
                <a href="<?php echo $base; ?>/CaptainAttendance">Attendance</a>
                <a href="<?php echo $base; ?>/CaptainFinance">Finance</a>
                <a href="<?php echo $base; ?>/CaptainMealPlan" class="active">Meal Plan</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                </div>
                <div class="user-profile">
                    <img src="<?php echo htmlspecialchars($data['captain_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Captain Profile">
                </div>
            </div>
        </header>

        <div class="page-header">
            <h1>Weekly Meal Plan</h1>
            <p>Follow your personalized nutrition schedule for optimal performance</p>
        </div>

        <div class="meal-tabs">
            <button class="meal-tab active" onclick="showMealType('breakfast', this)">Breakfast</button>
            <button class="meal-tab" onclick="showMealType('lunch', this)">Lunch</button>
            <button class="meal-tab" onclick="showMealType('dinner', this)">Dinner</button>
        </div>

        <div class="meal-plan-container">
            <div class="meals-grid" id="mealsGrid">
                <?php foreach ($data['meals'] as $day => $items): ?>
                    <div class="meal-day-card" data-day="<?php echo strtolower($day); ?>">
                        <h3><?php echo ucfirst($day); ?></h3>
                        <span class="calories" data-calories><?php echo (int) ($items['Breakfast']['calories'] ?? 0); ?> cal</span>
                        <ul data-meal-items>
                            <?php foreach (($items['Breakfast']['items'] ?? []) as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="nutrition-sidebar">
                <div class="nutrition-card">
                    <h3>Daily Nutrition</h3>
                    <div class="nutrition-stat">
                        <label>Calories</label>
                        <span><?php echo number_format((int) ($data['nutrition']['calories'] ?? 0)); ?></span>
                    </div>
                    <div class="nutrition-stat">
                        <label>Protein</label>
                        <span><?php echo (int) ($data['nutrition']['protein'] ?? 0); ?>g</span>
                    </div>
                    <div class="nutrition-stat">
                        <label>Carbs</label>
                        <span><?php echo (int) ($data['nutrition']['carbs'] ?? 0); ?>g</span>
                    </div>
                    <div class="nutrition-stat">
                        <label>Fat</label>
                        <span><?php echo (int) ($data['nutrition']['fat'] ?? 0); ?>g</span>
                    </div>
                </div>

                <div class="tip-card hydration">
                    <h4>Hydration Tip</h4>
                    <p><?php echo htmlspecialchars((string) ($data['hydration_tip'] ?? 'Drink 3-4 liters of water daily.')); ?></p>
                </div>

                <div class="updates-card">
                    <h3>Coach Updates</h3>
                    <?php foreach (($data['coach_updates'] ?? []) as $update): ?>
                        <div class="update-item <?php echo htmlspecialchars((string) ($update['type'] ?? 'warning')); ?>">
                            <span class="icon"><?php echo ($update['type'] ?? '') === 'success' ? '✅' : '⚠️'; ?></span>
                            <div>
                                <h4><?php echo htmlspecialchars((string) ($update['title'] ?? 'Update')); ?></h4>
                                <p><?php echo htmlspecialchars((string) ($update['message'] ?? '')); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        const weeklyMeals = <?php echo json_encode($data['meals'] ?? []); ?>;

        function normalizeMealType(type) {
            if (!type) {
                return 'Breakfast';
            }
            return type.charAt(0).toUpperCase() + type.slice(1).toLowerCase();
        }

        function renderMealsForType(type) {
            const mealType = normalizeMealType(type);
            const cards = document.querySelectorAll('.meal-day-card');

            cards.forEach(card => {
                const dayKey = card.dataset.day || '';
                const dayName = dayKey.charAt(0).toUpperCase() + dayKey.slice(1);
                const dayData = weeklyMeals[dayName] || {};
                const mealData = dayData[mealType] || { items: [], calories: 0 };
                const items = Array.isArray(mealData.items) ? mealData.items : [];

                const caloriesNode = card.querySelector('[data-calories]');
                if (caloriesNode) {
                    caloriesNode.textContent = String(mealData.calories || 0) + ' cal';
                }

                const listNode = card.querySelector('[data-meal-items]');
                if (!listNode) {
                    return;
                }

                listNode.innerHTML = '';
                if (!items.length) {
                    const li = document.createElement('li');
                    li.textContent = 'No meal items available.';
                    listNode.appendChild(li);
                    return;
                }

                items.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    listNode.appendChild(li);
                });
            });
        }

        function showMealType(type, btn) {
            document.querySelectorAll('.meal-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            renderMealsForType(type);
        }

        document.addEventListener('DOMContentLoaded', function () {
            renderMealsForType('breakfast');
        });
    </script>
</body>
</html>
