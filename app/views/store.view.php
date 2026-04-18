<?php
// Store Items Management View
$storeItems = $storeItems ?? [];
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
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/teamResult/teamResult.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/header.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/page.css">
    <style>
        .store-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .store-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .store-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .store-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .store-card-image {
            width: 100%;
            height: 200px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .store-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .store-card-body {
            padding: 15px;
        }

        .store-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .category-badge {
            background: #7c3aed;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .store-card-price {
            font-size: 20px;
            font-weight: 700;
            color: #7c3aed;
            margin-bottom: 10px;
        }

        .store-card-info {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-soldout {
            background: #f8d7da;
            color: #721c24;
        }

        .store-card-actions {
            display: flex;
            gap: 8px;
            border-top: 1px solid #eee;
            padding-top: 12px;
        }

        .store-card-actions button {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #7c3aed;
            color: white;
        }

        .btn-edit:hover {
            background: #6d28d9;
        }

        .btn-delete {
            background: #f3f4f6;
            color: #721c24;
        }

        .btn-delete:hover {
            background: #f8d7da;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-submit {
            flex: 1;
            padding: 12px;
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #6d28d9;
        }

        .btn-cancel {
            flex: 1;
            padding: 12px;
            background: #f3f4f6;
            color: #333;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .close-modal {
            float: right;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            line-height: 1;
        }

        .close-modal:hover {
            color: #333;
        }
    </style>
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
                <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
            </div>
        </div>

        <a href="<?php echo ROOT; ?>/admin" class="back-btn">&lt; Back</a>

        <div class="store-container">
            <div class="title-container">
                <h1 class="section-title">Team Store Management</h1>
                <button class="add-result-btn" type="button" onclick="openAddStoreItemModal()">
                    <span class="plus-sign">+</span>Add Item
                </button>
            </div>

            <?php if (empty($storeItems)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3>No Items Yet</h3>
                    <p>Start by adding merchandise items to your team store</p>
                </div>
            <?php else: ?>
                <div class="store-grid">
                    <?php foreach ($storeItems as $item): ?>
                        <div class="store-card">
                            <div class="store-card-image">
                                <?php if (!empty($item->item_image)): ?>
                                    <img src="<?php echo ROOT; ?>/assets/images/<?php echo htmlspecialchars($item->item_image); ?>" alt="<?php echo htmlspecialchars($item->item_name); ?>">
                                <?php else: ?>
                                    <span style="font-size: 64px; color: #ddd;">🛍️</span>
                                <?php endif; ?>
                            </div>
                            <div class="store-card-body">
                                <div class="store-card-title">
                                    <span><?php echo htmlspecialchars($item->item_name); ?></span>
                                    <span class="category-badge"><?php echo htmlspecialchars($item->category); ?></span>
                                </div>
                                <div class="store-card-price">Rs. <?php echo number_format($item->price, 2); ?></div>
                                <div class="store-card-info">
                                    <strong>Stock:</strong> <?php echo htmlspecialchars($item->quantity); ?> units
                                </div>
                                <div>
                                    <span class="status-badge <?php echo $item->status === 'Available' ? 'status-available' : 'status-soldout'; ?>">
                                        <?php echo htmlspecialchars($item->status); ?>
                                    </span>
                                </div>
                                <div class="store-card-actions">
                                    <button class="btn-edit" onclick="editStoreItem(<?php echo (int) $item->item_id; ?>)">
                                        <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">edit</span> Edit
                                    </button>
                                    <button class="btn-delete" onclick="deleteStoreItem(<?php echo (int) $item->item_id; ?>)">
                                        <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Store Item Modal -->
    <div class="modal" id="addStoreItemModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeAddStoreItemModal()">&times;</span>
            <div class="modal-header">Add Store Item</div>
            <form id="addStoreItemForm" onsubmit="submitAddStoreItemForm(event)">
                <div class="form-group">
                    <label for="itemName">Item Name *</label>
                    <input type="text" id="itemName" name="item_name" required placeholder="e.g., Team Jersey">
                </div>
                <div class="form-group">
                    <label for="itemCategory">Category *</label>
                    <select id="itemCategory" name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="itemPrice">Price (Rs.) *</label>
                    <input type="number" id="itemPrice" name="price" required placeholder="0.00" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label for="itemQuantity">Quantity *</label>
                    <input type="number" id="itemQuantity" name="quantity" required placeholder="0" min="0">
                </div>
                <div class="form-group">
                    <label for="itemDescription">Description</label>
                    <textarea id="itemDescription" name="description" placeholder="Item description..."></textarea>
                </div>
                <div class="form-group">
                    <label for="itemImage">Item Image</label>
                    <input type="file" id="itemImage" name="item_image" accept="image/*">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Add Item</button>
                    <button type="button" class="btn-cancel" onclick="closeAddStoreItemModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Store Item Modal -->
    <div class="modal" id="editStoreItemModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditStoreItemModal()">&times;</span>
            <div class="modal-header">Edit Store Item</div>
            <form id="editStoreItemForm" onsubmit="submitEditStoreItemForm(event)">
                <input type="hidden" id="editItemId" name="item_id">
                <div class="form-group">
                    <label for="editItemName">Item Name *</label>
                    <input type="text" id="editItemName" name="item_name" required>
                </div>
                <div class="form-group">
                    <label for="editItemCategory">Category *</label>
                    <select id="editItemCategory" name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editItemPrice">Price (Rs.) *</label>
                    <input type="number" id="editItemPrice" name="price" required step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label for="editItemQuantity">Quantity *</label>
                    <input type="number" id="editItemQuantity" name="quantity" required min="0">
                </div>
                <div class="form-group">
                    <label for="editItemStatus">Status *</label>
                    <select id="editItemStatus" name="status" required>
                        <option value="Available">Available</option>
                        <option value="Sold Out">Sold Out</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editItemDescription">Description</label>
                    <textarea id="editItemDescription" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="editItemImage">Item Image</label>
                    <input type="file" id="editItemImage" name="item_image" accept="image/*">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Update Item</button>
                    <button type="button" class="btn-cancel" onclick="closeEditStoreItemModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const baseUrl = window.ROOT || '<?php echo ROOT; ?>';

        function openAddStoreItemModal() {
            document.getElementById("addStoreItemModal").classList.add("active");
        }

        function closeAddStoreItemModal() {
            document.getElementById("addStoreItemModal").classList.remove("active");
            document.getElementById("addStoreItemForm").reset();
        }

        function submitAddStoreItemForm(event) {
            event.preventDefault();
            const form = document.getElementById("addStoreItemForm");
            const formData = new FormData(form);

            fetch(`${baseUrl}/store/addStoreItem`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeAddStoreItemModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function editStoreItem(itemId) {
            fetch(`${baseUrl}/store/getStoreItem?item_id=${itemId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.item) {
                        document.getElementById("editItemId").value = data.item.item_id;
                        document.getElementById("editItemName").value = data.item.item_name;
                        document.getElementById("editItemCategory").value = data.item.category;
                        document.getElementById("editItemPrice").value = data.item.price;
                        document.getElementById("editItemQuantity").value = data.item.quantity;
                        document.getElementById("editItemStatus").value = data.item.status;
                        document.getElementById("editItemDescription").value = data.item.description || '';
                        document.getElementById("editStoreItemModal").classList.add("active");
                    } else {
                        alert('Error loading item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
        }

        function closeEditStoreItemModal() {
            document.getElementById("editStoreItemModal").classList.remove("active");
            document.getElementById("editStoreItemForm").reset();
        }

        function submitEditStoreItemForm(event) {
            event.preventDefault();
            const form = document.getElementById("editStoreItemForm");
            const formData = new FormData(form);

            fetch(`${baseUrl}/store/editStoreItem`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeEditStoreItemModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function deleteStoreItem(itemId) {
            if (!confirm('Are you sure you want to delete this item?')) return;

            fetch(`${baseUrl}/store/deleteStoreItem`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `item_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
    </script>
</body>
</html>
