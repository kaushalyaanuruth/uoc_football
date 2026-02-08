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
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </a>
            </div>
            <nav class="nav-menu">
                <a href="<?php echo ROOT; ?>/coach" class="nav-link">Home</a>
                <a href="<?php echo ROOT; ?>/coach/events" class="nav-link">Events</a>
                <a href="<?php echo ROOT; ?>/coach/mealplan" class="nav-link active">Meal Plan</a>
                <a href="<?php echo ROOT; ?>/coach/performance" class="nav-link">Performance</a>
                <a href="<?php echo ROOT; ?>/coach/attendance" class="nav-link">Attendance</a>
                <a href="<?php echo ROOT; ?>/coach/notices" class="nav-link">Notices</a>
            </nav>
            <div class="right-section">
                <div class="bell-icon">
                    <i>🔔</i>
                </div>
                <img class="avatar" src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/avatar.jpg" alt="Coach Avatar">
            </div>
        </div>

        <!-- Page Title -->
        <div class="page-header">
            <h1 class="page-title">Weekly Meal Plan</h1>
        </div>

        <!-- Meal Plan Cards Grid -->
        <div class="meal-plan-grid">
            <!-- Breakfast Card -->
            <div class="meal-card">
                <div class="meal-card-header">
                    <div class="meal-icon-wrapper breakfast-icon">
                        <span class="meal-icon">🍳</span>
                    </div>
                    <h2 class="meal-title">Breakfast</h2>
                    <button class="edit-btn" onclick="editMeal('breakfast')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                </div>
                <div class="meal-items-list">
                    <div class="meal-item">Basmati or Red Rice</div>
                    <div class="meal-item">Chicken, Egg, Fish</div>
                    <div class="meal-item">Vegetable(minimum 3)</div>
                    <div class="meal-item">Paip</div>
                    <div class="meal-item">Yogurt</div>
                    <div class="meal-item">Fruits</div>
                </div>
            </div>

            <!-- Lunch Card -->
            <div class="meal-card">
                <div class="meal-card-header">
                    <div class="meal-icon-wrapper lunch-icon">
                        <span class="meal-icon">🍛</span>
                    </div>
                    <h2 class="meal-title">Lunch</h2>
                    <button class="edit-btn" onclick="editMeal('lunch')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                </div>
                <div class="meal-items-list">
                    <div class="meal-item">Basmati or Red Rice</div>
                    <div class="meal-item">Chicken, Egg, Fish</div>
                    <div class="meal-item">Vegetable(minimum 3)</div>
                    <div class="meal-item">Paip</div>
                    <div class="meal-item">Yogurt</div>
                    <div class="meal-item">Fruits</div>
                </div>
            </div>

            <!-- Dinner Card -->
            <div class="meal-card">
                <div class="meal-card-header">
                    <div class="meal-icon-wrapper dinner-icon">
                        <span class="meal-icon">🍽️</span>
                    </div>
                    <h2 class="meal-title">Dinner</h2>
                    <button class="edit-btn" onclick="editMeal('dinner')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                </div>
                <div class="meal-items-list">
                    <div class="meal-item">Basmati or Red Rice</div>
                    <div class="meal-item">Chicken, Egg, Fish</div>
                    <div class="meal-item">Vegetable(minimum 3)</div>
                    <div class="meal-item">Paip</div>
                    <div class="meal-item">Yogurt</div>
                    <div class="meal-item">Fruits</div>
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
                    <div class="form-group">
                        <label for="mealType">Meal Type</label>
                        <input type="text" id="mealType" readonly>
                    </div>
                    <div class="form-group">
                        <label for="mealItems">Meal Items (one per line)</label>
                        <textarea id="mealItems" rows="8" placeholder="Enter meal items, one per line"></textarea>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/mealplan-script.js"></script>
</body>
</html>