<!-- Meal Plan Page (Weekly View) -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/CaptainMealPlan.css">
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
            <a href="<?= ROOT ?>/Captaindashboard" class="active">Home</a>
            <a href="<?= ROOT ?>/dashboard">Schedule</a>
            <a href="<?= ROOT ?>/CaptainAnalyze">Analyze</a>
            <a href="<?= ROOT ?>/CaptainAttendance">Attendance</a> <a href="<?= ROOT ?>/CaptainInventory">Inventory</a>
            <a href="<?= ROOT ?>/CaptainFinance">Finance</a>
        </nav>
        <div class="nav-right">
            <button class="icon-btn">🔔</button>
            <div class="profile">
                <img class="avatar" src="<?php echo ROOT; ?>../assets/images/adminDashboard/header/avatar.jpg"
                    alt="Admin Avatar">

            </div>
        </div>
    </header>

    <div id="meal-plan-page" class="page-content">
        <div class="page-header">
            <h1>Weekly Meal Plan</h1>
            <p>Your personalized nutrition plan for optimal performance</p>
        </div>

        <div class="meal-plan-header">
            <div class="meal-plan-tabs">
                <button class="meal-plan-tab active" data-meal="breakfast">Breakfast</button>
                <button class="meal-plan-tab" data-meal="lunch">Lunch</button>
                <button class="meal-plan-tab" data-meal="dinner">Dinner</button>
            </div>
        </div>

        <!-- calendar removed per UX request (kept quick-access only) -->

        <div class="meals-grid" id="meals-grid">
            <div class="meal-card" data-meal="breakfast">
                <h3>🌅 Breakfast</h3>
                <ul>
                    <li>Basmati or Red rice</li>
                    <li>Chicken, Egg, Fish</li>
                    <li>Vegetable (minimum 3)</li>
                    <li>Pala</li>
                    <li>Yogurt</li>
                    <li>Fruits</li>
                </ul>
            </div>

            <div class="meal-card" data-meal="lunch">
                <h3>🍽️ Lunch</h3>
                <ul>
                    <li>Basmati or Red rice</li>
                    <li>Chicken, Egg, Fish</li>
                    <li>Vegetable (minimum 3)</li>
                    <li>Pala</li>
                    <li>Yogurt</li>
                    <li>Fruits</li>
                </ul>
            </div>

            <div class="meal-card" data-meal="dinner">
                <h3>🌙 Dinner</h3>
                <ul>
                    <li>Basmati or Red rice</li>
                    <li>Chicken, Egg, Fish</li>
                    <li>Vegetable (minimum 3)</li>
                    <li>Pala</li>
                    <li>Yogurt</li>
                    <li>Fruits</li>
                </ul>
            </div>
        </div>
    </div>
        <script>
            window.APP_ROOT = "<?= ROOT ?>";
        </script>

        <script src="<?= ROOT ?>/assets/js/captain/CaptainMealPlan.js"></script>
    </div>
</body>

</html>