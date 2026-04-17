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

        <!-- ── Header ─────────────────────────────────────────────────────── -->
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/coach">
                    <img class="header-logo"
                         src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/uoclogo.png"
                         alt="UOC Football Logo">
                </a>
            </div>
            <nav class="nav-menu">
                <a href="<?php echo ROOT; ?>/coachDashboard"  class="nav-link">Home</a>
                <a href="<?php echo ROOT; ?>/coachEvents"     class="nav-link">Events</a>
                <a href="<?php echo ROOT; ?>/coachMealPlan"   class="nav-link active">Meal Plan</a>
                <a href="<?php echo ROOT; ?>/coachPerformance" class="nav-link">Performance</a>
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link">Attendance</a>
                <a href="<?php echo ROOT; ?>/coachNotices"    class="nav-link">Notices</a>
            </nav>
            <div class="right-section">
                <div class="bell-icon"><i>🔔</i></div>
                <img class="avatar"
                     src="<?php echo ROOT; ?>/assets/images/coachDashboard/header/avatar.jpg"
                     alt="Coach Avatar">
            </div>
        </div>

        <!-- ── Page Title ──────────────────────────────────────────────────── -->
        <div class="page-header">
            <h1 class="page-title">Weekly Meal Plan</h1>
            <?php if (!empty($data['updated_date'])): ?>
                <p class="last-updated">
                    Last updated:
                    <strong><?php echo date('F j, Y', strtotime($data['updated_date'])); ?></strong>
                </p>
            <?php endif; ?>
        </div>

        <!-- ── Meal Plan Cards ─────────────────────────────────────────────── -->
        <div class="meal-plan-grid">

            <?php
            $meals = [
                'breakfast' => ['icon' => '🍳', 'label' => 'Breakfast', 'class' => 'breakfast-icon'],
                'lunch'     => ['icon' => '🍛', 'label' => 'Lunch',     'class' => 'lunch-icon'],
                'dinner'    => ['icon' => '🍽️', 'label' => 'Dinner',    'class' => 'dinner-icon'],
            ];

            foreach ($meals as $type => $meta):
                $items = $data[$type] ?? [];
            ?>

            <!-- <?php echo $meta['label']; ?> Card -->
            <div class="meal-card" id="card-<?php echo $type; ?>">
                <div class="meal-card-header">
                    <div class="meal-icon-wrapper <?php echo $meta['class']; ?>">
                        <span class="meal-icon"><?php echo $meta['icon']; ?></span>
                    </div>
                    <h2 class="meal-title"><?php echo $meta['label']; ?></h2>
                    <button class="add-btn"
                            onclick="openAddModal('<?php echo $type; ?>')"
                            title="Add item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                </div>

                <!-- Items list, data-meal-plan-id used by JS -->
                <div class="meal-items-list"
                     id="list-<?php echo $type; ?>"
                     data-meal-plan-id="<?php echo $data['meal_plan_id']; ?>">

                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                        <div class="meal-item" data-id="<?php echo $item->id; ?>">
                            <div class="meal-item-text">
                                <span class="item-name"><?php echo htmlspecialchars($item->meal); ?></span>
                                <span class="item-amount"><?php echo htmlspecialchars($item->amount); ?></span>
                            </div>
                            <div class="meal-item-actions">
                                <button class="item-edit-btn"
                                        onclick="openEditModal('<?php echo $type; ?>',
                                                               <?php echo $item->id; ?>,
                                                               '<?php echo addslashes($item->meal); ?>',
                                                               '<?php echo addslashes($item->amount); ?>')"
                                        title="Edit">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </button>
                                <button class="item-delete-btn"
                                        onclick="deleteItem('<?php echo $type; ?>',
                                                            <?php echo $item->id; ?>)"
                                        title="Delete">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6l-1 14H6L5 6"></path>
                                        <path d="M10 11v6"></path><path d="M14 11v6"></path>
                                        <path d="M9 6V4h6v2"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state" id="empty-<?php echo $type; ?>">
                            No items yet. Click <strong>+</strong> to add one.
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <?php endforeach; ?>

        </div><!-- /.meal-plan-grid -->
    </div><!-- /.container -->

    <!-- ── Add Modal ──────────────────────────────────────────────────────── -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="addModalTitle">Add Item</h2>
                <button class="close-btn" onclick="closeModal('addModal')">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="addMealType">
                <div class="form-group">
                    <label for="addMeal">Meal Name</label>
                    <input type="text" id="addMeal" placeholder="e.g. Grilled Chicken">
                </div>
                <div class="form-group">
                    <label for="addAmount">Amount / Serving</label>
                    <input type="text" id="addAmount" placeholder="e.g. 200g">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
                    <button type="button" class="btn-save"   onclick="submitAdd()">Add Item</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Edit Modal ─────────────────────────────────────────────────────── -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="editModalTitle">Edit Item</h2>
                <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editMealType">
                <input type="hidden" id="editItemId">
                <div class="form-group">
                    <label for="editMeal">Meal Name</label>
                    <input type="text" id="editMeal" placeholder="e.g. Grilled Chicken">
                </div>
                <div class="form-group">
                    <label for="editAmount">Amount / Serving</label>
                    <input type="text" id="editAmount" placeholder="e.g. 200g">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                    <button type="button" class="btn-save"   onclick="submitEdit()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Confirm Delete Modal ───────────────────────────────────────────── -->
    <div id="deleteModal" class="modal">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h2>Delete Item</h2>
                <button class="close-btn" onclick="closeModal('deleteModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p class="confirm-text">Are you sure you want to delete this meal item?</p>
                <input type="hidden" id="deleteMealType">
                <input type="hidden" id="deleteItemId">
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="button" class="btn-delete" onclick="confirmDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inject PHP root into JS so the script file can build AJAX URLs
        const APP_ROOT = '<?php echo ROOT; ?>';
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/mealplan-script.js"></script>
</body>
</html>