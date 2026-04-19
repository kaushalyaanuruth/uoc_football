<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $base = rtrim(ROOT, '/');
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>UOC_football - Meal Plan</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/mealplan-style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/common.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/coachDashboard">
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </a>
            </div>
            <nav class="nav-menu">
                <a href="<?php echo ROOT; ?>/coachDashboard" class="nav-link">Home</a>
                <a href="<?php echo ROOT; ?>/coachEvents" class="nav-link">Events</a>
                <a href="<?php echo ROOT; ?>/coachMealPlan" class="nav-link active">Meal Plan</a>
                <a href="<?php echo ROOT; ?>/coachPerformance" class="nav-link">Performance</a>
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link">Attendance</a>
                <a href="<?php echo ROOT; ?>/coachNotices" class="nav-link">Notices</a>
            </nav>
            <div class="right-section">
                <a class="player-logout-btn" href="<?php echo $base; ?>/login/logout">Logout</a>
                <div class="notification-icon" id="coachNotificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                    <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
                </div>
                <a class="user-profile" href="<?php echo $base; ?>/coachDashboard#profile" title="Profile">
                    <img src="<?php echo htmlspecialchars($data['coach_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Coach Avatar">
                </a>
            </div>
        </div>

        <!-- Page Title -->
        <div class="page-header">
            <h1 class="page-title">Weekly Meal Plan</h1>
            <button type="button" class="reset-plan-btn" onclick="resetWeeklyPlan()">Reset Week Plan</button>
        </div>

        <div class="page-controls">
            <div class="day-picker">
                <label for="selectedDay">Select Day</label>
                <select id="selectedDay" class="day-select" onchange="changeSelectedDay(this.value)">
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                    <option value="saturday">Saturday</option>
                    <option value="sunday">Sunday</option>
                </select>
            </div>
        </div>

        <p class="selected-day-label" id="selectedDayLabel">Meal plan for Monday</p>

        <!-- Meal Plan Cards Grid -->
        <div class="meal-plan-grid">
            <!-- Breakfast Card -->
            <div class="meal-card" data-meal-type="breakfast">
                <div class="meal-card-header">
 
                    <h2 class="meal-title">Breakfast</h2>
                    <div class="meal-card-actions">
                        <button type="button" class="edit-btn" onclick="editMeal('breakfast')" aria-label="Edit breakfast meals">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="meal-items-list" data-meal-list="breakfast" role="list">
                </div>
            </div>

            <!-- Lunch Card -->
            <div class="meal-card" data-meal-type="lunch">
                <div class="meal-card-header">
 
                    <h2 class="meal-title">Lunch</h2>
                    <div class="meal-card-actions">
                        <button type="button" class="edit-btn" onclick="editMeal('lunch')" aria-label="Edit lunch meals">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="meal-items-list" data-meal-list="lunch" role="list">
                </div>
            </div>

            <!-- Dinner Card -->
            <div class="meal-card" data-meal-type="dinner">
                <div class="meal-card-header">
                    
                    <h2 class="meal-title">Dinner</h2>
                    <div class="meal-card-actions">
                        <button type="button" class="edit-btn" onclick="editMeal('dinner')" aria-label="Edit dinner meals">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="meal-items-list" data-meal-list="dinner" role="list">
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Edit Meal</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="mealForm">
                    <div class="form-row-inline">
                        <div class="form-group">
                            <label for="mealDay">Day</label>
                            <input type="text" id="mealDay" readonly>
                        </div>
                        <div class="form-group">
                            <label for="mealType">Meal Type</label>
                            <input type="text" id="mealType" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="newMealItem">Add Meal Item</label>
                        <div class="item-input-row">
                            <input type="text" id="newMealItem" placeholder="Enter a meal item" maxlength="100">
                            <button type="button" id="addMealItemBtn" class="btn-add-item">Add</button>
                        </div>
                        <p class="form-hint">Press Enter in the input or click Add to include an item.</p>
                        <div id="mealFormError" class="form-error" aria-live="polite"></div>
                    </div>
                    <div class="form-group">
                        <label>Current Items</label>
                        <div id="modalItemList" class="modal-item-list"></div>
                    </div>
                    <input type="hidden" id="modalMode" value="edit">
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn-save" id="saveMealBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="notification-overlay" id="coachNotificationOverlay" style="display: none;">
        <?php foreach (array_slice($data['notices'] ?? [], 0, 5) as $notice): ?>
            <div style="padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid #eee;">
                <p style="font-size:0.86rem; color:#4a1150; font-weight:600; margin-bottom:4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                <p style="font-size:0.82rem; color:#4b5563; line-height:1.35;"><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        window.COACH_MEALPLAN_API_BASE = "<?php echo ROOT; ?>/coachMealPlan";
        window.COACH_MEALPLAN_INITIAL = <?php echo json_encode($data['team_meal_plan'] ?? []); ?>;
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/mealplan-script.js"></script>
    <script>
        const coachBell = document.getElementById('coachNotificationBell');
        const coachOverlay = document.getElementById('coachNotificationOverlay');

        coachBell.addEventListener('click', (e) => {
            e.stopPropagation();
            coachOverlay.style.display = coachOverlay.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (
                coachOverlay.style.display === 'block' &&
                !coachOverlay.contains(e.target) &&
                !coachBell.contains(e.target)
            ) {
                coachOverlay.style.display = 'none';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                coachOverlay.style.display = 'none';
            }
        });
    </script>
</body>
</html>