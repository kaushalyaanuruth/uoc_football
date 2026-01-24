<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Dashboard - UOC Football</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/playerDashboard.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header>
            <div class="logo-section">
                <img src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo ROOT; ?>/PlayerDashboard" class="active">Home</a>
                <a href="<?php echo ROOT; ?>/Schedule">Schedule</a>
                <a href="<?php echo ROOT; ?>/Analyze">Analyze</a>
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

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Welcome,<br><?php echo $data['player_name']; ?></h1>
            </div>
            <div class="banner-datetime">
                <h2><?php echo $data['date']; ?></h2>
                <p><?php echo $data['time']; ?></p>
            </div>
            <div class="banner-decoration"></div>
        </div>

        <div class="dashboard-grid">
            <!-- Main Content -->
            <div class="main-content">
                <div class="info-cards">
                    <!-- Next Practice Card -->
                    <div class="card next-card">
                        <h3>Next Practice...</h3>
                        <p><strong><?php echo $data['next_practice']['date']; ?>,
                                <?php echo $data['next_practice']['time_of_day']; ?></strong></p>
                        <p><?php echo $data['next_practice']['time']; ?></p>
                    </div>

                    <!-- Next Event Card -->
                    <div class="card next-card">
                        <h3>Next Event...</h3>
                        <p style="color: #888; font-size: 0.9rem;"><?php echo $data['next_event']['type']; ?></p>
                        <h4 style="margin: 5px 0; color: #333;"><?php echo $data['next_event']['title']; ?></h4>
                        <p style="font-size: 0.85rem;">📅 <?php echo $data['next_event']['date']; ?></p>
                        <p style="font-size: 0.85rem;">📍 <?php echo $data['next_event']['location']; ?></p>
                    </div>

                    <!-- SLUG Countdown -->
                    <div class="card slug-countdown">
                        <span class="slug-number"><?php echo $data['slug_countdown']; ?></span>
                        <span class="slug-text">days<br>more</span>
                        <p style="font-size: 0.8rem; margin-top: 10px; color: var(--primary-color);">to be SLUG
                            Champions</p>
                    </div>
                </div>

                <div class="bottom-row">
                    <!-- Test Summary chart -->
                    <div class="card">
                        <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Test Summery</h3>
                        <div class="chart-container"
                            style="height: 150px; background: #fafafa; border-radius: 8px; position: relative;">
                            <!-- SVG Chart Placeholder -->
                            <svg width="100%" height="100%" viewBox="0 0 400 150">
                                <path d="M0,120 Q50,110 100,105 T200,95 T300,85 T400,70" fill="none" stroke="#a29bfe"
                                    stroke-width="2" />
                                <path d="M0,140 Q50,135 100,130 T200,125 T300,115 T400,110" fill="none" stroke="#dcdde1"
                                    stroke-width="2" />
                            </svg>
                            <div
                                style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.7rem; color: #999;">
                                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notice Board -->
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

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Calendar (Visual Placeholder) -->
                <div class="card">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span style="font-weight: 600;">August 2025</span>
                        <span style="cursor: pointer;">&gt;</span>
                    </div>
                    <div
                        style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-size: 0.8rem; row-gap: 10px;">
                        <div style="color: #999;">Mo</div>
                        <div style="color: #999;">Tu</div>
                        <div style="color: #999;">We</div>
                        <div style="color: #999;">Th</div>
                        <div style="color: #999;">Fr</div>
                        <div style="color: #999;">Sa</div>
                        <div style="color: #999;">Su</div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div>1</div>
                        <div>2</div>
                        <div>3</div>
                        <div>4</div>
                        <div>5</div>
                        <div>6</div>
                        <div>7</div>
                        <div>8</div>
                        <div>9</div>
                        <div>10</div>
                        <div>11</div>
                        <div
                            style="background: var(--primary-light); border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: var(--primary-color);">
                            12</div>
                        <div>13</div>
                        <div>14</div>
                        <div>15</div>
                        <div>16</div>
                        <div>17</div>
                    </div>
                </div>

                <!-- Meal Plan -->
                <div class="card">
                    <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Meal Plan</h3>
                    <div class="meal-plan-tabs">
                        <button class="meal-btn">Breakfast</button>
                        <button class="meal-btn active">Lunch</button>
                        <button class="meal-btn">Dinner</button>
                    </div>
                    <ul class="meal-list">
                        <?php foreach ($data['meal_plan']['items'] as $item): ?>
                            <li><?php echo $item; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Notification Overlay -->
        <div class="notification-overlay">
            <p style="font-size: 0.85rem; color: #444; line-height: 1.4;">
                Tomorrow (25th august 2025) practice has been canceled.
            </p>
        </div>
    </div>
</body>

</html>