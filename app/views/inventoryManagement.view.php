<?php
$items = $data['items'] ?? [];

/**
 * This helper converts stored inventory icon values into Material Symbols names.
 * It keeps older icon values working while the UI renders the newer font-based icons.
 */
function inventoryIconSymbol($icon, $itemName = '')
{
    $iconMap = [
        'inventory.svg' => 'inventory_2',
        'inventory_2' => 'inventory_2',
        'inventory' => 'inventory_2',
        'footballs' => 'sports_soccer',
        'football' => 'sports_soccer',
        'sports_soccer.svg' => 'sports_soccer',
        'sports_soccer' => 'sports_soccer',
        'bibs' => 'checkroom',
        'bips' => 'checkroom',
        'checkroom.svg' => 'checkroom',
        'checkroom' => 'checkroom',
        'resistance_band' => 'fitness_center',
        'fitness_center.svg' => 'fitness_center',
        'fitness_center' => 'fitness_center',
        'markers' => 'inventory_2',
        'cones' => 'inventory_2',
        'sports_bar.svg' => 'sports_bar',
        'sports_bar' => 'sports_bar',
        'water_bottle.svg' => 'sports_bar',
        'water_bottle' => 'sports_bar',
        'sports_bottle' => 'sports_bar'
    ];

    $normalized = strtolower(trim((string) $icon));

    $nameFallbackMap = [
        'bibs' => 'checkroom',
        'footballs' => 'sports_soccer',
        'markers' => 'inventory_2',
        'cones' => 'inventory_2',
        'resistance band' => 'fitness_center',
        'water bottles' => 'sports_bar'
    ];

    $normalizedName = strtolower(trim((string) $itemName));

    if ($normalized === '' || $normalized === 'inventory_2' || $normalized === 'inventory.svg') {
        return $nameFallbackMap[$normalizedName] ?? 'inventory_2';
    }

    return $iconMap[$normalized] ?? ($nameFallbackMap[$normalizedName] ?? 'inventory_2');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400" rel="stylesheet">
    <title>UOC Football - Inventory Management</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/inventoryManagement/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/header.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/page.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/admin">
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </a>
            </div>
            <div class="right-section">
                <img class="avatar" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/avatar.jpg" alt="Admin Avatar">
            </div>
        </div>

        <a href="<?php echo ROOT; ?>/adminDashboard" class="back-btn">&lt; Back</a>


        <section class="toolbar-card">
            <div class="toolbar-left">
                <button class="add-item-btn" type="button" id="openItemModalBtn">+ Add Item</button>
            </div>
            <div class="toolbar-right">
                <div class="search-wrap">
                    <span class="search-icon material-symbols-outlined" aria-hidden="true">search</span>
                    <input type="text" id="itemSearch" class="search-input" placeholder="Search items...">
                </div>
                <select id="categoryFilter" class="type-filter" aria-label="Filter by category">
                    <option value="all">All Categories</option>
                    <option value="Training">Training</option>
                    <option value="Match">Match</option>
                    <option value="Fitness">Fitness</option>
                    <option value="Recovery">Recovery</option>
                    <option value="General">General</option>
                </select>
                <select id="statusFilter" class="type-filter" aria-label="Filter by status">
                    <option value="all">All Status</option>
                    <option value="available">Available</option>
                    <option value="low">Low Stock</option>
                    <option value="reserved">Reserved</option>
                </select>
            </div>
        </section>

        <section class="inventory-panel">
            <div class="inventory-panel-head">
                <h2>Inventory Items</h2>
            </div>
            <div class="inventory-list">
                <div class="inventory-list-head">
                    <span>Item</span>
                    <span>Category</span>
                    <span>Stock</span>
                    <span>Status</span>
                    <span>Location</span>
                    <span>Actions</span>
                </div>
                <div id="inventoryListBody">
                    <?php foreach ($items as $item): ?>
                        <div class="inventory-item" data-item-id="<?php echo (int) $item['id']; ?>" data-name="<?php echo htmlspecialchars(strtolower($item['name']), ENT_QUOTES, 'UTF-8'); ?>" data-category="<?php echo htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?>" data-status="<?php echo htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="item-main">
                                <div class="item-icon">
                                    <span class="material-symbols-outlined" aria-hidden="true"><?php echo htmlspecialchars(inventoryIconSymbol($item['icon'] ?? '', $item['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="item-text">
                                    <h3 class="item-name"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p class="item-desc"><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <span class="item-category"><?php echo htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="item-stock"><?php echo (int) $item['quantity']; ?> <?php echo htmlspecialchars($item['unit'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="status-badge status-<?php echo htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo ucfirst($item['status']); ?></span>
                            <span class="item-location"><?php echo htmlspecialchars($item['location'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <div class="row-actions">
                                <button type="button" class="icon-btn edit-btn" data-id="<?php echo (int) $item['id']; ?>" aria-label="Edit item">
                                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                </button>
                                <button type="button" class="icon-btn delete-btn" data-id="<?php echo (int) $item['id']; ?>" aria-label="Delete item">
                                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (empty($items)): ?>
                    <div class="empty-state" id="emptyState">No inventory items yet. Add your first item.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="modal-overlay" id="itemModal">
        <form class="modal" id="itemForm">
            <button type="button" class="close-modal-btn" id="closeItemModalBtn">&times;</button>
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <h2 class="modal-title" id="modalTitle">Add Item</h2>
            <div class="modal-body">
                <input type="hidden" id="itemId" name="id">
                <div class="form-group">
                    <label class="input-label" for="itemName">Item Name</label>
                    <input type="text" class="form-input" id="itemName" name="name" required>
                </div>
                <div class="form-group two-col">
                    <div>
                        <label class="input-label" for="itemCategory">Category</label>
                        <input type="text" class="form-input" id="itemCategory" name="category" placeholder="Training, Match..." required>
                    </div>
                    <div>
                        <label class="input-label" for="itemStatus">Status</label>
                        <select class="form-input" id="itemStatus" name="status" required>
                            <option value="available">Available</option>
                            <option value="low">Low Stock</option>
                            <option value="reserved">Reserved</option>
                        </select>
                    </div>
                </div>
                <div class="form-group two-col">
                    <div>
                        <label class="input-label" for="itemQuantity">Quantity</label>
                        <input type="number" class="form-input" id="itemQuantity" name="quantity" min="0" required>
                    </div>
                    <div>
                        <label class="input-label" for="itemUnit">Unit</label>
                        <input type="text" class="form-input" id="itemUnit" name="unit" placeholder="pcs, sets, bottles" required>
                    </div>
                </div>
                <div class="form-group two-col">
                    <div>
                        <label class="input-label" for="itemLocation">Location</label>
                        <input type="text" class="form-input" id="itemLocation" name="location" required>
                    </div>
                    <div>
                        <label class="input-label" for="itemIcon">Icon</label>
                        <select class="form-input" id="itemIcon" name="icon" required>
                            <option value="inventory_2">Inventory</option>
                            <option value="sports_soccer">Football</option>
                            <option value="checkroom">Bib</option>
                            <option value="fitness_center">Gym</option>
                            <option value="sports_bar">Bottle</option>
                        </select>
                    </div>
                </div>
                <div class="form-group notes-group">
                    <label class="input-label" for="itemDescription">Description</label>
                    <textarea class="form-input" id="itemDescription" name="description" rows="4" placeholder="Add item notes"></textarea>
                </div>
                <p class="form-message" id="formMessage" aria-live="polite"></p>
                <div class="form-actions">
                    <button type="button" class="secondary-btn" id="cancelItemModalBtn">Cancel</button>
                    <button type="submit" class="submit-btn" id="itemSubmitBtn">Save Item</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        window.INVENTORY_MANAGEMENT_CONFIG = {
            root: "<?php echo ROOT; ?>"
        };
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/inventoryManagement/script.js"></script>
</body>
</html>
