(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    const config = window.INVENTORY_MANAGEMENT_CONFIG || {};
    const root = config.root || '';

    const itemModal = byId('itemModal');
    const itemForm = byId('itemForm');
    const modalTitle = byId('modalTitle');
    const formMessage = byId('formMessage');
    const itemSubmitBtn = byId('itemSubmitBtn');

    const openItemModalBtn = byId('openItemModalBtn');
    const closeItemModalBtn = byId('closeItemModalBtn');
    const cancelItemModalBtn = byId('cancelItemModalBtn');

    const searchInput = byId('itemSearch');
    const categoryFilter = byId('categoryFilter');
    const statusFilter = byId('statusFilter');
    const listBody = byId('inventoryListBody');
    const emptyState = byId('emptyState');

    let isEditMode = false;
    let currentItemId = null;

    function clearMessage() {
        if (!formMessage) {
            return;
        }
        formMessage.textContent = '';
        formMessage.className = 'form-message';
    }

    function setMessage(text, type) {
        if (!formMessage) {
            return;
        }
        formMessage.textContent = text;
        formMessage.className = 'form-message ' + type;
    }

    function toggleEmptyState() {
        if (!listBody || !emptyState) {
            return;
        }

        const rows = listBody.querySelectorAll('.inventory-item');
        emptyState.style.display = rows.length ? 'none' : 'block';
    }

    function openModal(mode) {
        if (!itemModal || !itemForm) {
            return;
        }

        clearMessage();

        if (mode === 'add') {
            isEditMode = false;
            currentItemId = null;
            itemForm.reset();
            byId('itemId').value = '';
            modalTitle.textContent = 'Add Item';
            itemSubmitBtn.textContent = 'Save Item';
        }

        itemModal.classList.add('active');
    }

    function closeModal() {
        if (!itemModal || !itemForm) {
            return;
        }

        itemModal.classList.remove('active');
        itemForm.reset();
        clearMessage();
        isEditMode = false;
        currentItemId = null;
        byId('itemId').value = '';
    }

    function buildEndpoint(path) {
        return root + path;
    }

    function normalizeIcon(icon, itemName) {
        const value = String(icon || '').trim().toLowerCase();
        const normalizedName = String(itemName || '').trim().toLowerCase();
        const iconMap = {
            'inventory.svg': 'inventory_2',
            'inventory_2': 'inventory_2',
            'inventory': 'inventory_2',
            'footballs': 'sports_soccer',
            'football': 'sports_soccer',
            'sports_soccer.svg': 'sports_soccer',
            'sports_soccer': 'sports_soccer',
            'bibs': 'checkroom',
            'bips': 'checkroom',
            'checkroom.svg': 'checkroom',
            'checkroom': 'checkroom',
            'resistance_band': 'fitness_center',
            'fitness_center.svg': 'fitness_center',
            'fitness_center': 'fitness_center',
            'markers': 'sports_bar',
            'cones': 'sports_bar',
            'sports_bar.svg': 'sports_bar',
            'sports_bar': 'sports_bar',
            'water_bottle.svg': 'sports_bar',
            'water_bottle': 'sports_bar',
            'sports_bottle': 'sports_bar'
        };

        const nameFallbackMap = {
            'bibs': 'checkroom',
            'footballs': 'sports_soccer',
            'markers': 'sports_bar',
            'cones': 'sports_bar',
            'resistance band': 'fitness_center',
            'water bottles': 'sports_bar'
        };

        if (!value || value === 'inventory_2' || value === 'inventory.svg') {
            return nameFallbackMap[normalizedName] || 'inventory_2';
        }

        return iconMap[value] || nameFallbackMap[normalizedName] || 'inventory_2';
    }


    function itemToRow(item) {
        const row = document.createElement('div');
        row.className = 'inventory-item';
        row.dataset.itemId = item.id;
        row.dataset.name = (item.name || '').toLowerCase();
        row.dataset.category = item.category || '';
        row.dataset.status = item.status || '';

        row.innerHTML = [
            '<div class="item-main">',
            '    <div class="item-icon">',
            '        <span class="material-symbols-outlined" aria-hidden="true">' + escapeHtml(normalizeIcon(item.icon, item.name)) + '</span>',
            '    </div>',
            '    <div class="item-text">',
            '        <h3 class="item-name">' + escapeHtml(item.name || '') + '</h3>',
            '        <p class="item-desc">' + escapeHtml(item.description || '') + '</p>',
            '    </div>',
            '</div>',
            '<span class="item-category">' + escapeHtml(item.category || '') + '</span>',
            '<span class="item-stock">' + escapeHtml(String(item.quantity || 0)) + ' ' + escapeHtml(item.unit || 'pcs') + '</span>',
            '<span class="status-badge status-' + escapeHtml(item.status || 'available') + '">' + escapeHtml(capitalize(item.status || 'available')) + '</span>',
            '<span class="item-location">' + escapeHtml(item.location || '') + '</span>',
            '<div class="row-actions">',
            '    <button type="button" class="icon-btn edit-btn" data-id="' + item.id + '" aria-label="Edit item">',
            '        <span class="material-symbols-outlined" aria-hidden="true">edit</span>',
            '    </button>',
            '    <button type="button" class="icon-btn delete-btn" data-id="' + item.id + '" aria-label="Delete item">',
            '        <span class="material-symbols-outlined" aria-hidden="true">delete</span>',
            '    </button>',
            '</div>'
        ].join('');

        return row;
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function capitalize(value) {
        return String(value).charAt(0).toUpperCase() + String(value).slice(1);
    }

    async function fetchItem(itemId) {
        const response = await fetch(buildEndpoint('/inventoryManagement/get?id=' + encodeURIComponent(itemId)), {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        return response.json();
    }

    function fillForm(item) {
        byId('itemId').value = item.id || '';
        byId('itemName').value = item.name || '';
        byId('itemCategory').value = item.category || '';
        byId('itemQuantity').value = item.quantity || 0;
        byId('itemUnit').value = item.unit || 'pcs';
        byId('itemStatus').value = item.status || 'available';
        byId('itemLocation').value = item.location || '';
        byId('itemIcon').value = normalizeIcon(item.icon, item.name);
        byId('itemDescription').value = item.description || '';
    }

    async function openEditModal(itemId) {
        try {
            const result = await fetchItem(itemId);
            if (!result.success || !result.data) {
                throw new Error(result.message || 'Unable to load item');
            }

            isEditMode = true;
            currentItemId = itemId;
            fillForm(result.data);
            modalTitle.textContent = 'Edit Item';
            itemSubmitBtn.textContent = 'Update Item';
            itemModal.classList.add('active');
        } catch (error) {
            setMessage(error.message, 'error');
        }
    }

    function collectFormData() {
        return {
            id: byId('itemId').value,
            name: byId('itemName').value.trim(),
            category: byId('itemCategory').value.trim(),
            quantity: byId('itemQuantity').value,
            unit: byId('itemUnit').value.trim(),
            status: byId('itemStatus').value,
            location: byId('itemLocation').value.trim(),
            icon: byId('itemIcon').value,
            description: byId('itemDescription').value.trim()
        };
    }

    function validateForm(formData) {
        if (!formData.name) {
            return 'Item name is required';
        }
        if (!formData.category) {
            return 'Category is required';
        }
        if (formData.quantity === '' || Number.isNaN(Number(formData.quantity))) {
            return 'Quantity is required';
        }
        if (!formData.unit) {
            return 'Unit is required';
        }
        if (!formData.location) {
            return 'Location is required';
        }
        return '';
    }

    async function saveItem(formData) {
        const endpoint = isEditMode && currentItemId ? '/inventoryManagement/update' : '/inventoryManagement/add';
        const payload = isEditMode && currentItemId
            ? { ...formData, id: currentItemId, quantity: Number(formData.quantity) }
            : { ...formData, quantity: Number(formData.quantity) };

        const response = await fetch(buildEndpoint(endpoint), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        return response.json();
    }

    async function handleSubmit(event) {
        event.preventDefault();

        const formData = collectFormData();
        const validationError = validateForm(formData);
        if (validationError) {
            setMessage(validationError, 'error');
            return;
        }

        itemSubmitBtn.disabled = true;
        itemSubmitBtn.textContent = isEditMode ? 'Updating...' : 'Saving...';

        try {
            const result = await saveItem(formData);
            if (!result.success) {
                throw new Error(result.message || 'Unable to save item');
            }

            setMessage(result.message || 'Item saved successfully', 'success');
            setTimeout(function () {
                window.location.reload();
            }, 400);
        } catch (error) {
            setMessage(error.message, 'error');
            itemSubmitBtn.disabled = false;
            itemSubmitBtn.textContent = isEditMode ? 'Update Item' : 'Save Item';
        }
    }

    async function deleteItem(itemId) {
        const confirmed = window.confirm('Delete this item? This action cannot be undone.');
        if (!confirmed) {
            return;
        }

        try {
            const response = await fetch(buildEndpoint('/inventoryManagement/delete'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: itemId })
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Unable to delete item');
            }

            window.location.reload();
        } catch (error) {
            setMessage(error.message, 'error');
        }
    }

    function filterRows() {
        if (!listBody) {
            return;
        }

        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        const selectedCategory = categoryFilter ? categoryFilter.value : 'all';
        const selectedStatus = statusFilter ? statusFilter.value : 'all';
        const rows = listBody.querySelectorAll('.inventory-item');

        let visibleCount = 0;
        rows.forEach(function (row) {
            const name = (row.dataset.name || '').toLowerCase();
            const category = row.dataset.category || '';
            const status = row.dataset.status || '';

            const searchMatches = !query || name.includes(query) || row.textContent.toLowerCase().includes(query);
            const categoryMatches = selectedCategory === 'all' || category === selectedCategory;
            const statusMatches = selectedStatus === 'all' || status === selectedStatus;

            if (searchMatches && categoryMatches && statusMatches) {
                row.style.display = 'grid';
                visibleCount += 1;
            } else {
                row.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.style.display = visibleCount ? 'none' : 'block';
        }
    }

    function bindEvents() {
        if (openItemModalBtn) {
            openItemModalBtn.addEventListener('click', function () {
                openModal('add');
            });
        }

        if (closeItemModalBtn) {
            closeItemModalBtn.addEventListener('click', closeModal);
        }

        if (cancelItemModalBtn) {
            cancelItemModalBtn.addEventListener('click', closeModal);
        }

        if (itemForm) {
            itemForm.addEventListener('submit', handleSubmit);
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterRows);
        }

        if (categoryFilter) {
            categoryFilter.addEventListener('change', filterRows);
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', filterRows);
        }

        if (listBody) {
            listBody.addEventListener('click', function (event) {
                const editBtn = event.target.closest('.edit-btn');
                if (editBtn) {
                    openEditModal(editBtn.getAttribute('data-id'));
                    return;
                }

                const deleteBtn = event.target.closest('.delete-btn');
                if (deleteBtn) {
                    deleteItem(deleteBtn.getAttribute('data-id'));
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && itemModal && itemModal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindEvents();
        toggleEmptyState();
        filterRows();
    });
})();
