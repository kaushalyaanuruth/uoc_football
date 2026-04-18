<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>UOC_football - Meal Plan</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/mealplan-style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/coach">
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
 
                <img class="avatar" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/avatar.jpg" alt="Coach Avatar">
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
                        <button type="button" class="create-btn" onclick="openCreateModal('breakfast')">Add Meal</button>
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
                        <button type="button" class="create-btn" onclick="openCreateModal('lunch')">Add Meal</button>
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
                        <button type="button" class="create-btn" onclick="openCreateModal('dinner')">Add Meal</button>
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

    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/mealplan-script.js"></script>
</body>
</html>