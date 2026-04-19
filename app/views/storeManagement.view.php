<?php
// Store Management View
$items = $items ?? [];
$categories = $categories ?? [];
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
    <title>Store Management - UOC Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/storeManagement/style.css">
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
            <div class="right-section user-section">
                <a href="<?php echo ROOT; ?>/adminDashboard?openNotifications=1" class="notification-icon" title="Notifications" aria-label="Notifications">
                    <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
                </a>
                <a href="<?php echo ROOT; ?>/adminDashboard?openProfile=1" class="user-profile" title="Admin Profile" aria-label="Admin Profile">
                    <img class="avatar" src="<?php echo htmlspecialchars($_SESSION['admin_profile_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Admin Avatar">
                </a>
                <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
            </div>
        </div>

        <a href="<?php echo ROOT; ?>/admin" class="back-btn">&lt; Back</a>

        <div class="title-container">
            <h1 class="section-title">Store Management</h1>
            <button class="add-item-btn" type="button" onclick="openAddItemModal()">
                <span class="material-symbols-outlined plus-icon">add</span>Add Item
            </button>
        </div>

        <?php if (empty($items)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <span class="material-symbols-outlined" style="font-size: 64px; color: #d1d5db;">shopping_bag</span>
                </div>
                <h3>No Items Yet</h3>
                <p>Start by adding merchandise items to your store</p>
            </div>
        <?php else: ?>
            <div class="store-grid">
                <div class="add-item-card" onclick="openAddItemModal()">
                    <div class="add-icon">
                        <span class="material-symbols-outlined" style="font-size: 48px;">add</span>
                    </div>
                    <h3>Add New Item</h3>
                </div>

                <?php foreach ($items as $item): ?>
                    <div class="store-item-card">
                        <div class="store-card-image">
                            <?php if (!empty($item->item_image)): ?>
                                <img src="<?php echo ROOT; ?>/<?php echo htmlspecialchars($item->item_image); ?>" alt="<?php echo htmlspecialchars($item->item_name); ?>">
                            <?php else: ?>
                                <div class="store-card-placeholder">
                                    <span class="material-symbols-outlined">shopping_bag</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="store-card-body">
                            <div class="store-card-header">
                                <h3 class="store-card-title"><?php echo htmlspecialchars($item->item_name); ?></h3>
                                <span class="category-badge"><?php echo htmlspecialchars($item->category); ?></span>
                            </div>
                            <div class="store-card-price">Rs. <?php echo number_format($item->price, 2); ?></div>
                            <div class="store-card-info">
                                <span><strong>Stock:</strong> <?php echo htmlspecialchars($item->quantity); ?> units</span>
                            </div>
                            <div>
                                <span class="status-badge <?php echo $item->status === 'Available' ? 'status-available' : 'status-soldout'; ?>">
                                    <?php echo htmlspecialchars($item->status); ?>
                                </span>
                            </div>
                            <div class="store-card-actions">
                                <button class="icon-btn edit-btn" onclick="editItem(<?php echo (int) $item->item_id; ?>)">
                                    <span class="material-symbols-outlined">edit</span>Edit
                                </button>
                                <button class="icon-btn delete-btn" onclick="deleteItem(<?php echo (int) $item->item_id; ?>)">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add Item Modal -->
    <div class="modal-overlay" id="addItemModal">
        <form class="modal" id="addItemForm" onsubmit="submitAddItemForm(event)">
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                <button type="button" class="close-modal-btn" onclick="closeAddItemModal()">✕</button>
            </div>
            <div class="modal-body">
                <h2 class="modal-title">Add Store Item</h2>

                <div class="form-group">
                    <label class="input-label" for="itemName">Item Name *</label>
                    <input type="text" id="itemName" name="item_name" class="form-input" required placeholder="e.g., Team Jersey">
                </div>

                <div class="form-group">
                    <label class="input-label" for="itemCategory">Category *</label>
                    <select id="itemCategory" name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label" for="itemPrice">Price (Rs.) *</label>
                    <input type="number" id="itemPrice" name="price" class="form-input" required placeholder="0.00" step="0.01" min="0">
                </div>

                <div class="form-group">
                    <label class="input-label" for="itemQuantity">Quantity *</label>
                    <input type="number" id="itemQuantity" name="quantity" class="form-input" required placeholder="0" min="0">
                </div>

                <div class="form-group">
                    <label class="input-label" for="itemDescription">Description</label>
                    <textarea id="itemDescription" name="description" class="form-textarea" placeholder="Item description..."></textarea>
                </div>

                <div class="form-group">
                    <label class="input-label" for="itemImage">Item Image</label>
                    <input type="file" id="itemImage" name="item_image" class="form-input" accept="image/*">
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">Add Item</button>
                    <button type="button" class="cancel-btn" onclick="closeAddItemModal()">Cancel</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal-overlay" id="editItemModal">
        <form class="modal" id="editItemForm" onsubmit="submitEditItemForm(event)">
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                <button type="button" class="close-modal-btn" onclick="closeEditItemModal()">✕</button>
            </div>
            <div class="modal-body">
                <h2 class="modal-title">Edit Store Item</h2>

                <input type="hidden" id="editItemId" name="item_id">

                <div class="form-group">
                    <label class="input-label" for="editItemName">Item Name *</label>
                    <input type="text" id="editItemName" name="item_name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="input-label" for="editItemCategory">Category *</label>
                    <select id="editItemCategory" name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label" for="editItemPrice">Price (Rs.) *</label>
                    <input type="number" id="editItemPrice" name="price" class="form-input" required step="0.01" min="0">
                </div>

                <div class="form-group">
                    <label class="input-label" for="editItemQuantity">Quantity *</label>
                    <input type="number" id="editItemQuantity" name="quantity" class="form-input" required min="0">
                </div>

                <div class="form-group">
                    <label class="input-label" for="editItemStatus">Status *</label>
                    <select id="editItemStatus" name="status" class="form-select" required>
                        <option value="Available">Available</option>
                        <option value="Sold Out">Sold Out</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label" for="editItemDescription">Description</label>
                    <textarea id="editItemDescription" name="description" class="form-textarea"></textarea>
                </div>

                <div class="form-group">
                    <label class="input-label">Current Image</label>
                    <div id="editItemImagePreview" style="width: 100%; height: 200px; background-color: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; overflow: hidden;">
                        <span class="material-symbols-outlined" style="font-size: 64px; color: #d1d5db;">shopping_bag</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="input-label" for="editItemImage">Item Image (Leave empty to keep current)</label>
                    <input type="file" id="editItemImage" name="item_image" class="form-input" accept="image/*" onchange="previewEditImage(event)">
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">Update Item</button>
                    <button type="button" class="cancel-btn" onclick="closeEditItemModal()">Cancel</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        window.ROOT = '<?php echo ROOT; ?>';
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/storeManagement/storeManagement.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/potal/adminHeaderPopup.js"></script>
</body>
</html>



