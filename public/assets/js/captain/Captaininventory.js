document.addEventListener('DOMContentLoaded', () => {
    const cfg = window.CAPTAIN_INVENTORY_CONFIG || {};
    const root = cfg.root || '';
    const pollMs = Number(cfg.pollMs || 15000);

    const modal = document.getElementById('inventoryModal');
    const toast = document.getElementById('toast');
    const inventoryForm = document.getElementById('inventoryForm');
    const tbody = document.querySelector('.inventory-table tbody');

    const itemId = document.getElementById('item_id');
    const itemName = document.getElementById('item_name');
    const category = document.getElementById('category');
    const quantity = document.getElementById('quantity');
    const status = document.getElementById('status');
    const categoryFilter = document.getElementById('categoryFilter');

    const totalEl = document.getElementById('captainTotalItems');
    const inUseEl = document.getElementById('captainInUseItems');
    const availableEl = document.getElementById('captainAvailableItems');
    const damagedEl = document.getElementById('captainDamagedItems');

    const successModal = document.getElementById('successModal');
    const closeSuccess = document.getElementById('closeSuccess');

    let usageChart;
    let statusChart;
    let allItems = [];
    let pollHandle = null;

    function endpoint(path) {
        return root + path;
    }

    function statusKey(label) {
        const value = String(label || '').trim().toLowerCase().replace(/[\s-]+/g, '_');
        if (value === 'damaged') {
            return 'damaged';
        }

        if (value === 'in_use' || value === 'inuse' || value === 'low' || value === 'reserved') {
            return 'in_use';
        }

        return 'available';
    }

    function showSuccessModal(message) {
        const msg = document.getElementById('centerToastMessage');
        msg.innerText = message;
        successModal.style.display = 'flex';
    }

    function showToast(message, type = 'success') {
        const colors = {
            success: '#22c55e',
            error: '#dc2626',
            warning: '#f59e0b'
        };

        toast.style.background = colors[type] || colors.success;
        toast.innerHTML = message;
        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.display = 'none';
        }, 2500);
    }

    function initCharts() {
        usageChart = new Chart(document.getElementById('equipmentUsageChart'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'In Use',
                        backgroundColor: '#f97316',
                        data: []
                    },
                    {
                        label: 'Available',
                        backgroundColor: '#22c55e',
                        data: []
                    }
                ]
            }
        });

        statusChart = new Chart(document.getElementById('statusDistributionChart'), {
            type: 'pie',
            data: {
                labels: ['In Use', 'Available', 'Damaged'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: ['#f97316', '#22c55e', '#ef4444']
                }]
            }
        });
    }

    function renderRows(items) {
        tbody.innerHTML = '';

        items.forEach((item) => {
            const tr = document.createElement('tr');
            tr.dataset.id = String(item.item_id || 0);

            const key = statusKey(item.status_key || item.status);
            const statusLabel = item.status || 'Available';

            tr.innerHTML = `
                <td>${escapeHtml(item.item_name || '')}</td>
                <td>${escapeHtml(item.category || 'General')}</td>
                <td>${Number(item.total_count || 0)}</td>
                <td><span class="status ${escapeHtml(key.replace('_', ''))}">${escapeHtml(statusLabel)}</span></td>
                <td>${escapeHtml(item.last_updated || '')}</td>
                <td class="actions">
                    <button class="btn-edit">Edit</button>
                    <button class="btn-delete">Delete</button>
                </td>
            `;

            tbody.appendChild(tr);
        });
    }

    function refreshCategoryFilter(items) {
        const selected = categoryFilter.value;
        const categories = Array.from(new Set(items.map((i) => String(i.category || 'General')))).sort();

        categoryFilter.innerHTML = '<option value="all">All Categories</option>';
        categories.forEach((cat) => {
            const opt = document.createElement('option');
            opt.value = cat;
            opt.textContent = cat;
            categoryFilter.appendChild(opt);
        });

        if (selected && Array.from(categoryFilter.options).some((o) => o.value === selected)) {
            categoryFilter.value = selected;
        }
    }

    function applyCategoryFilter() {
        const selected = categoryFilter.value;

        document.querySelectorAll('.inventory-table tbody tr').forEach((row) => {
            const rowCategory = (row.children[1] ? row.children[1].innerText : '').trim();
            row.style.display = selected === 'all' || rowCategory === selected ? '' : 'none';
        });
    }

    function updateCharts(categoryStats) {
        const labels = [];
        const inUseData = [];
        const availableData = [];
        let totalInUse = 0;
        let totalAvailable = 0;
        let totalDamaged = 0;

        (categoryStats || []).forEach((item) => {
            labels.push(String(item.category || 'General'));

            const inUse = Number(item.in_use || 0);
            const available = Number(item.available || 0);
            const damaged = Number(item.damaged || 0);

            inUseData.push(inUse);
            availableData.push(available);

            totalInUse += inUse;
            totalAvailable += available;
            totalDamaged += damaged;
        });

        usageChart.data.labels = labels;
        usageChart.data.datasets[0].data = inUseData;
        usageChart.data.datasets[1].data = availableData;
        usageChart.update();

        statusChart.data.datasets[0].data = [totalInUse, totalAvailable, totalDamaged];
        statusChart.update();
    }

    function updateStats(stats) {
        totalEl.textContent = Number(stats.total || 0);
        inUseEl.textContent = Number(stats.in_use || 0);
        availableEl.textContent = Number(stats.available || 0);
        damagedEl.textContent = Number(stats.damaged || 0);
    }

    async function fetchSnapshot() {
        const res = await fetch(endpoint('/captainInventory/snapshot'), {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        if (!res.ok) {
            throw new Error('Failed to load inventory');
        }

        return res.json();
    }

    async function refreshSnapshot() {
        const payload = await fetchSnapshot();
        if (payload.status !== 'success' || !payload.data) {
            throw new Error(payload.message || 'Failed to load inventory');
        }

        allItems = payload.data.items || [];
        refreshCategoryFilter(allItems);
        renderRows(allItems);
        applyCategoryFilter();
        updateStats(payload.data.stats || {});
        updateCharts(payload.data.category_stats || []);
    }

    async function removeItem(row) {
        if (!confirm('Are you sure you want to delete this item?')) {
            return;
        }

        const itemIdVal = Number(row.dataset.id || 0);
        if (!itemIdVal) {
            return;
        }

        try {
            const res = await fetch(endpoint('/captainInventory/delete'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ item_id: itemIdVal })
            });
            const data = await res.json();
            if (data.status !== 'success') {
                throw new Error(data.message || 'Delete failed');
            }

            await refreshSnapshot();
            showSuccessModal('Item deleted successfully!');
        } catch (error) {
            showToast(error.message || 'Operation failed!', 'error');
        }
    }

    function openModalForAdd() {
        inventoryForm.reset();
        itemId.value = '';
        modal.querySelector('.modal-header span').innerText = 'Add Inventory Item';
        modal.style.display = 'flex';
    }

    function openModalForEdit(row) {
        itemId.value = row.dataset.id || '';
        itemName.value = row.children[0].innerText.trim();
        category.value = row.children[1].innerText.trim();
        quantity.value = row.children[2].innerText.trim();
        status.value = row.children[3].innerText.trim();
        modal.querySelector('.modal-header span').innerText = 'Edit Inventory Item';
        modal.style.display = 'flex';
    }

    async function submitForm(e) {
        e.preventDefault();

        const payload = {
            item_id: Number(itemId.value || 0),
            item_name: itemName.value.trim(),
            quantity: Number(quantity.value || 0),
            category: category.value.trim(),
            status: status.value.trim()
        };

        const isEdit = payload.item_id > 0;
        const url = isEdit ? '/captainInventory/update' : '/captainInventory/store';

        try {
            const res = await fetch(endpoint(url), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.status !== 'success') {
                throw new Error(data.message || 'Save failed');
            }

            modal.style.display = 'none';
            await refreshSnapshot();
            showSuccessModal('Item saved successfully!');
        } catch (error) {
            showToast(error.message || 'Operation failed!', 'error');
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function startPolling() {
        if (pollHandle) {
            clearInterval(pollHandle);
        }

        pollHandle = setInterval(() => {
            if (document.hidden) {
                return;
            }

            if (modal.style.display === 'flex') {
                return;
            }

            refreshSnapshot().catch(() => {
                // keep page usable even if one poll fails
            });
        }, pollMs);
    }

    closeSuccess.addEventListener('click', () => {
        successModal.style.display = 'none';
    });

    successModal.addEventListener('click', (e) => {
        if (e.target === successModal) {
            successModal.style.display = 'none';
        }
    });

    document.getElementById('addItem').addEventListener('click', openModalForAdd);
    categoryFilter.addEventListener('change', applyCategoryFilter);

    tbody.addEventListener('click', (e) => {
        const row = e.target.closest('tr');
        if (!row) {
            return;
        }

        if (e.target.closest('.btn-edit')) {
            openModalForEdit(row);
            return;
        }

        if (e.target.closest('.btn-delete')) {
            removeItem(row);
        }
    });

    inventoryForm.addEventListener('submit', submitForm);

    document.getElementById('close').onclick = document.getElementById('closeModal').onclick = () => {
        modal.style.display = 'none';
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modal.style.display = 'none';
        }
    });

    initCharts();
    refreshSnapshot().catch(() => {
        showToast('Unable to load inventory data.', 'error');
    });
    startPolling();
});