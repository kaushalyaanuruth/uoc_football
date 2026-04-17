<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Plan - UOC Football</title>
    <?php
    $base = rtrim(ROOT, '/');
    $commonFile = __DIR__ . '/../../public/assets/css/playerCommon.css';
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
                <a href="<?php echo $base; ?>/PlayerDashboard">Home</a>
                <a href="<?php echo $base; ?>/Schedule">Schedule</a>
                <a href="<?php echo $base; ?>/Analyze">Analyze</a>
                <a href="<?php echo $base; ?>/Notices">Notices</a>
                <a href="<?php echo $base; ?>/MealPlan" class="active">Meal Plan</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                </div>
                <div class="user-profile">
                    <img src="<?php echo $base; ?>/assets/images/user-placeholder.jpg" alt="User Profile">
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
                    <div class="meal-day-card">
                        <h3><?php echo ucfirst($day); ?></h3>
                        <span class="calories"><?php echo $items['calories']; ?> cal</span>
                        <ul>
                            <?php foreach ($items['items'] as $item): ?>
                                <li>🔹 <?php echo $item; ?></li>
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
                        <span>2,850</span>
                    </div>
                    <div class="nutrition-stat">
                        <label>Protein</label>
                        <span>185g</span>
                    </div>
                    <div class="nutrition-stat">
                        <label>Carbs</label>
                        <span>320g</span>
                    </div>
                    <div class="nutrition-stat">
                        <label>Fat</label>
                        <span>95g</span>
                    </div>
                </div>

                <div class="tip-card hydration">
                    <h4>💧 Hydration Tip</h4>
                    <p>Drink 3-4 liters of water daily. Increase intake during training sessions.</p>
                </div>

                <div class="updates-card">
                    <h3>Coach Updates</h3>
                    <div class="update-item warning">
                        <span class="icon">⚠️</span>
                        <div>
                            <h4>Meal Plan Update</h4>
                            <p>Added extra carbs for Thursday due to intensive training session.</p>
                            <small>2 hours ago</small>
                        </div>
                    </div>
                    <div class="update-item success">
                        <span class="icon">✅</span>
                        <div>
                            <h4>Nutrition Goal</h4>
                            <p>Great job maintaining your meal schedule this week!</p>
                            <small>1 day ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showMealType(type, btn) {
            document.querySelectorAll('.meal-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            // Meal type switching logic here
        }
    </script>
</body>
</html>
