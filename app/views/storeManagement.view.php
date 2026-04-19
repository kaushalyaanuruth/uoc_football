<?php
// Store Management View
$items = $items ?? [];
$orders = $orders ?? [];
$categories = $categories ?? [];
$adminImage = (string) ($_SESSION['admin_profile_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg'));
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
                <a href="<?php echo ROOT; ?>/adminDashboard">
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </a>
            </div>
            <div class="right-section user-section">
                <a href="<?php echo ROOT; ?>/adminDashboard?openNotifications=1" class="notification-icon" title="Notifications" aria-label="Notifications">
                    <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
                </a>
                <a href="<?php echo ROOT; ?>/adminDashboard?openProfile=1" class="user-profile" title="Admin Profile" aria-label="Admin Profile">
                    <img class="avatar" src="<?php echo htmlspecialchars($adminImage); ?>" alt="Admin Avatar">
                </a>
                <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
            </div>
        </div>

        <a href="<?php echo ROOT; ?>/adminDashboard" class="back-btn">&lt; Back</a>

        <div class="title-container">
            <h1 class="section-title">Store Management</h1>
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
                            <?php if (!empty($item->product_image)): ?>
                                <img src="<?php echo ROOT; ?>/<?php echo htmlspecialchars($item->product_image); ?>" alt="<?php echo htmlspecialchars($item->product_name); ?>">
                            <?php else: ?>
                                <div class="store-card-placeholder">
                                    <span class="material-symbols-outlined">shopping_bag</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="store-card-body">
                            <div class="store-card-header">
                                <h3 class="store-card-title"><?php echo htmlspecialchars($item->product_name); ?></h3>
                                <span class="category-badge"><?php echo htmlspecialchars($item->category ?? 'Merchandise'); ?></span>
                            </div>
                            <div class="store-card-price">From Rs. <?php echo number_format((float) ($item->min_price ?? 0), 2); ?></div>
                            <div class="store-card-info">
                                <span><strong>Total Stock:</strong> <?php echo (int) ($item->total_stock ?? 0); ?> units</span>
                            </div>
                            <div class="store-card-info" style="display:block; margin-top:6px;">
                                <?php foreach (($item->variants ?? []) as $variant): ?>
                                    <span style="display:inline-block; margin:2px 6px 2px 0; padding:4px 8px; background:#f3f4f6; border-radius:999px; font-size:12px;">
                                        <?php echo htmlspecialchars((string) ($variant->size ?? '')); ?>: Rs. <?php echo number_format((float) ($variant->price ?? 0), 2); ?> (<?php echo (int) ($variant->stock_qty ?? 0); ?>)
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <div>
                                <span class="status-badge <?php echo ((int) ($item->is_active ?? 0) === 1) ? 'status-available' : 'status-soldout'; ?>">
                                    <?php echo ((int) ($item->is_active ?? 0) === 1) ? 'Available' : 'Sold Out'; ?>
                                </span>
                            </div>
                            <div class="store-card-actions">
                                <button class="icon-btn edit-btn" onclick="editItem(<?php echo (int) $item->product_id; ?>)">
                                    <span class="material-symbols-outlined">edit</span>Edit
                                </button>
                                <button class="icon-btn delete-btn" onclick="deleteItem(<?php echo (int) $item->product_id; ?>)">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="orders-section">
            <div class="title-container orders-title-wrap">
                <h2 class="section-title orders-title">Recent Orders</h2>
            </div>

            <?php if (empty($orders)): ?>
                <div class="empty-state orders-empty-state">
                    <div class="empty-state-icon">
                        <span class="material-symbols-outlined" style="font-size: 64px; color: #d1d5db;">receipt_long</span>
                    </div>
                    <h3>No Orders Yet</h3>
                    <p>Placed orders will appear here with buyer details and items.</p>
                </div>
            <?php else: ?>
                <div class="orders-grid">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-card-head">
                                <h3><?php echo htmlspecialchars((string) ($order->order_number ?? 'Order')); ?></h3>
                                <span class="order-status status-<?php echo strtolower((string) ($order->status ?? 'Pending')); ?>">
                                    <?php echo htmlspecialchars((string) ($order->status ?? 'Pending')); ?>
                                </span>
                            </div>

                            <div class="order-summary-row">
                                <span class="order-summary-chip">
                                    <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                                    <?php echo !empty($order->created_at) ? date('Y-m-d h:i A', strtotime((string) $order->created_at)) : '-'; ?>
                                </span>
                                <span class="order-summary-chip order-summary-total">
                                    <span class="material-symbols-outlined" aria-hidden="true">payments</span>
                                    Rs. <?php echo number_format((float) ($order->total ?? 0), 2); ?>
                                </span>
                            </div>

                            <div class="order-meta-grid">
                                <div class="order-meta-cell">
                                    <p class="order-meta-label">Buyer</p>
                                    <p class="order-meta-value"><?php echo htmlspecialchars((string) ($order->buyer_name ?? '-')); ?></p>
                                </div>
                                <div class="order-meta-cell">
                                    <p class="order-meta-label">Phone</p>
                                    <p class="order-meta-value"><?php echo htmlspecialchars((string) ($order->buyer_phone ?? '-')); ?></p>
                                </div>
                                <div class="order-meta-cell order-meta-cell-wide">
                                    <p class="order-meta-label">Email</p>
                                    <p class="order-meta-value"><?php echo htmlspecialchars((string) ($order->buyer_email ?? '-')); ?></p>
                                </div>
                                <div class="order-meta-cell order-meta-cell-wide">
                                    <p class="order-meta-label">Address</p>
                                    <p class="order-meta-value"><?php echo htmlspecialchars((string) ($order->buyer_address ?? '-')); ?></p>
                                </div>
                            </div>

                            <div class="order-items-list">
                                <div class="order-items-head">
                                    <p class="order-items-title">Items</p>
                                    <p class="order-items-count"><?php echo count((array) ($order->items ?? [])); ?> line(s)</p>
                                </div>
                                <div class="order-item-header-row">
                                    <span>Product</span>
                                    <span>Size</span>
                                    <span>Qty</span>
                                    <span>Total</span>
                                </div>
                                <?php if (!empty($order->items)): ?>
                                    <?php foreach (($order->items ?? []) as $orderItem): ?>
                                        <div class="order-item-row">
                                            <span class="order-item-product"><?php echo htmlspecialchars((string) ($orderItem->product_name ?? 'Item')); ?></span>
                                            <span><?php echo htmlspecialchars((string) ($orderItem->size ?? '-')); ?></span>
                                            <span><?php echo (int) ($orderItem->quantity ?? 0); ?></span>
                                            <span class="order-item-total">Rs. <?php echo number_format((float) ($orderItem->line_total ?? 0), 2); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="order-items-empty">No order items available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
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
                    <label class="input-label">Size Variants (Price + Stock) *</label>
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:8px; font-size:12px; color:#6b7280;">
                        <span>Size</span><span>Price (Rs.)</span><span>Stock</span>
                    </div>
                    <?php foreach (['S','M','L','XL'] as $size): ?>
                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:8px;">
                            <input type="text" name="variant_size[]" class="form-input" value="<?php echo $size; ?>" readonly>
                            <input type="number" name="variant_price[]" class="form-input" step="0.01" min="0" placeholder="0.00">
                            <input type="number" name="variant_stock[]" class="form-input" min="0" placeholder="0">
                        </div>
                    <?php endforeach; ?>
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
                    <label class="input-label">Size Variants (Price + Stock) *</label>
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:8px; font-size:12px; color:#6b7280;">
                        <span>Size</span><span>Price (Rs.)</span><span>Stock</span>
                    </div>
                    <?php foreach (['S','M','L','XL'] as $size): ?>
                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:8px;">
                            <input type="text" name="variant_size[]" class="form-input edit-variant-size" value="<?php echo $size; ?>" readonly>
                            <input type="number" name="variant_price[]" class="form-input edit-variant-price" data-size="<?php echo $size; ?>" step="0.01" min="0" placeholder="0.00">
                            <input type="number" name="variant_stock[]" class="form-input edit-variant-stock" data-size="<?php echo $size; ?>" min="0" placeholder="0">
                        </div>
                    <?php endforeach; ?>
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



