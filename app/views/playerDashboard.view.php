<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Dashboard - UOC Football</title>
    <?php
    $base = rtrim(ROOT, '/');
    $commonFile = __DIR__ . '/../../public/assets/css/playerCommon.css';
    $commonVersion = file_exists($commonFile) ? filemtime($commonFile) : time();
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerDashboard.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerCommon.css?v=<?php echo $commonVersion; ?>">
</head>
<body>
    <div class="dashboard-container">
        <header class="player-header">
            <div class="logo-section">
                <img src="<?php echo $base; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo $base; ?>/PlayerDashboard" class="active">Home</a>
                <a href="<?php echo $base; ?>/Schedule">Schedule</a>
                <a href="<?php echo $base; ?>/Analyze">Analyze</a>
                <a href="<?php echo $base; ?>/Notices">Notices</a>
                <a href="<?php echo $base; ?>/MealPlan">Meal Plan</a>
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

        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Welcome,<br><?php echo $data['player_name']; ?></h1>
            </div>
            <div class="banner-datetime">
                <h2><?php echo $data['date']; ?></h2>
                <p id="live-time"><?php echo $data['time']; ?></p>
            </div>
            <div class="banner-decoration"></div>
        </div>

        <div class="dashboard-grid">
            <div class="main-content">
                <div class="info-cards">
                    <div class="card next-card">
                        <h3>Next Practice...</h3>
                        <p><strong><?php echo $data['next_practice']['date']; ?>, <?php echo $data['next_practice']['time_of_day']; ?></strong></p>
                        <p><?php echo $data['next_practice']['time']; ?></p>
                    </div>

                    <div class="card next-card">
                        <h3>Next Event...</h3>
                        <p style="color: #888; font-size: 0.9rem;"><?php echo $data['next_event']['type']; ?></p>
                        <h4 style="margin: 5px 0; color: #333;"><?php echo $data['next_event']['title']; ?></h4>
                        <p style="font-size: 0.85rem;">📍 <?php echo $data['next_event']['date']; ?></p>
                        <p style="font-size: 0.85rem;">📌 <?php echo $data['next_event']['location']; ?></p>
                    </div>

                    <div class="card slug-countdown">
                        <span class="slug-number"><?php echo $data['slug_countdown']; ?></span>
                        <span class="slug-text">days<br>more</span>
                        <p style="font-size: 0.8rem; margin-top: 10px; color: var(--primary-color);">to be SLUG Champions</p>
                    </div>
                </div>

                <div class="bottom-row">
                    <div class="card">
                        <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Test Summery</h3>
                        <div class="chart-container" style="height: 150px; background: #fafafa; border-radius: 8px; position: relative;">
                            <svg width="100%" height="100%" viewBox="0 0 400 150">
                                <path d="M0,120 Q50,110 100,105 T200,95 T300,85 T400,70" fill="none" stroke="#a29bfe" stroke-width="2" />
                                <path d="M0,140 Q50,135 100,130 T200,125 T300,115 T400,110" fill="none" stroke="#dcdde1" stroke-width="2" />
                            </svg>
                            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.7rem; color: #999;">
                                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Notice</h3>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php foreach ($data['notices'] as $notice): ?>
                                <div style="padding-bottom: 10px; border-bottom: 1px solid #f0f0f0;">
                                    <p style="font-size: 0.9rem; color: #555;"><?php echo $notice['text']; ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sidebar">
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span style="font-weight: 600;" id="calendarHighlight">August 2025</span>
                        <span style="cursor: pointer;" onclick="alert('Next month view coming soon!')">&gt;</span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-size: 0.8rem; row-gap: 10px;" id="calendarGrid">
                        <div style="color: #999;">Mo</div>
                        <div style="color: #999;">Tu</div>
                        <div style="color: #999;">We</div>
                        <div style="color: #999;">Th</div>
                        <div style="color: #999;">Fr</div>
                        <div style="color: #999;">Sa</div>
                        <div style="color: #999;">Su</div>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Meal Plan</h3>
                    <div class="meal-plan-tabs">
                        <button class="meal-btn" onclick="showMeal('Breakfast', this)">Breakfast</button>
                        <button class="meal-btn active" onclick="showMeal('Lunch', this)">Lunch</button>
                        <button class="meal-btn" onclick="showMeal('Dinner', this)">Dinner</button>
                    </div>
                    <ul class="meal-list" id="mealList">
                        <?php foreach ($data['meal_plan']['Lunch'] as $item): ?>
                            <li><?php echo $item; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="notification-overlay" id="notificationOverlay" style="display: none;">
            <p style="font-size: 0.85rem; color: #444; line-height: 1.4;">Tomorrow (25th august 2025) practice has been canceled.</p>
        </div>

        <script>
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

            const mealPlans = <?php echo json_encode($data['meal_plan']); ?>;

            function showMeal(mealType, btn) {
                document.querySelectorAll('.meal-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const list = document.getElementById('mealList');
                list.innerHTML = '';
                if (mealPlans[mealType]) {
                    mealPlans[mealType].forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = item;
                        list.appendChild(li);
                    });
                }
            }

            function updateClock() {
                const now = new Date();
                let hours = now.getHours();
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                const strTime = hours + ':' + minutes + ' ' + ampm;
                document.getElementById('live-time').textContent = strTime;
            }
            setInterval(updateClock, 1000);

            function generateCalendar() {
                const now = new Date();
                const year = now.getFullYear();
                const month = now.getMonth();
                const today = now.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                document.getElementById('calendarHighlight').textContent = `${monthNames[month]} ${year}`;
                const firstDay = new Date(year, month, 1).getDay();
                let startingIndex = firstDay === 0 ? 6 : firstDay - 1;
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const grid = document.getElementById('calendarGrid');
                const headers = Array.from(grid.children).slice(0, 7);
                grid.innerHTML = '';
                headers.forEach(h => grid.appendChild(h));
                for (let i = 0; i < startingIndex; i++) {
                    const empty = document.createElement('div');
                    grid.appendChild(empty);
                }
                for (let d = 1; d <= daysInMonth; d++) {
                    const dayCell = document.createElement('div');
                    dayCell.textContent = d;
                    if (d === today) {
                        dayCell.style.background = 'var(--primary-light)';
                        dayCell.style.borderRadius = '50%';
                        dayCell.style.width = '25px';
                        dayCell.style.height = '25px';
                        dayCell.style.display = 'flex';
                        dayCell.style.alignItems = 'center';
                        dayCell.style.justifyContent = 'center';
                        dayCell.style.margin = '0 auto';
                        dayCell.style.color = 'var(--primary-color)';
                    }
                    grid.appendChild(dayCell);
                }
            }
            generateCalendar();
        </script>
    </div>
</body>
</html>
