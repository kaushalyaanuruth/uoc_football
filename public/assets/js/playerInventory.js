document.addEventListener('DOMContentLoaded', function () {
    var cfg = window.PLAYER_INVENTORY_CONFIG || {};
    var root = cfg.root || '';
    var pollMs = Number(cfg.pollMs || 15000);

    var inventoryTableBody = document.getElementById('inventoryTableBody');
    var openLogsTableBody = document.getElementById('openLogsTableBody');
    var historyLogsTableBody = document.getElementById('historyLogsTableBody');
    var toast = document.getElementById('inventoryToast');

    var statTotal = document.getElementById('statTotal');
    var statAvailable = document.getElementById('statAvailable');
    var statInUse = document.getElementById('statInUse');
    var statDamaged = document.getElementById('statDamaged');

    function endpoint(path) {
        return root + path;
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function showToast(message, isError) {
        toast.style.display = 'block';
        toast.textContent = message;
        toast.style.background = isError ? '#b91c1c' : '#111827';

        setTimeout(function () {
            toast.style.display = 'none';
        }, 2500);
    }

    function renderItems(items) {
        inventoryTableBody.innerHTML = '';

        if (!Array.isArray(items) || items.length === 0) {
            inventoryTableBody.innerHTML = '<tr><td colspan="7">No inventory items available.</td></tr>';
            return;
        }

        items.forEach(function (item) {
            var canTake = !!item.can_take;
            var maxQty = Math.max(1, Number(item.available_count || 0));
            var row = document.createElement('tr');
            row.setAttribute('data-item-id', String(item.item_id || 0));
            row.innerHTML =
                '<td>' + escapeHtml(item.item_name) + '</td>' +
                '<td>' + escapeHtml(item.category || 'General') + '</td>' +
                '<td>' + Number(item.total_count || 0) + '</td>' +
                '<td>' + Number(item.available_count || 0) + '</td>' +
                '<td><span class="status-badge ' + escapeHtml(item.status_key || 'available') + '">' + escapeHtml(item.status || 'Available') + '</span></td>' +
                '<td><input class="qty-input" type="number" min="1" max="' + maxQty + '" value="1" ' + (canTake ? '' : 'disabled') + '></td>' +
                '<td><button class="btn-take" ' + (canTake ? '' : 'disabled') + '>Take</button></td>';

            inventoryTableBody.appendChild(row);
        });
    }

    function renderOpenLogs(logs) {
        openLogsTableBody.innerHTML = '';

        if (!Array.isArray(logs) || logs.length === 0) {
            openLogsTableBody.innerHTML = '<tr><td colspan="4">No borrowed items.</td></tr>';
            return;
        }

        logs.forEach(function (log) {
            var row = document.createElement('tr');
            row.setAttribute('data-log-id', String(log.log_id || 0));
            row.innerHTML =
                '<td>' + escapeHtml(log.item_name) + '</td>' +
                '<td>' + Number(log.quantity || 0) + ' ' + escapeHtml(log.unit || 'pcs') + '</td>' +
                '<td>' + escapeHtml(log.taken_date || '') + '</td>' +
                '<td><button class="btn-return">Return</button></td>';

            openLogsTableBody.appendChild(row);
        });
    }

    function renderHistory(logs) {
        historyLogsTableBody.innerHTML = '';

        if (!Array.isArray(logs) || logs.length === 0) {
            historyLogsTableBody.innerHTML = '<tr><td colspan="3">No recent returns.</td></tr>';
            return;
        }

        logs.forEach(function (log) {
            var row = document.createElement('tr');
            row.innerHTML =
                '<td>' + escapeHtml(log.item_name) + '</td>' +
                '<td>' + Number(log.quantity || 0) + ' ' + escapeHtml(log.unit || 'pcs') + '</td>' +
                '<td>' + escapeHtml(log.return_date || '') + '</td>';

            historyLogsTableBody.appendChild(row);
        });
    }

    function renderStats(stats) {
        statTotal.textContent = Number(stats.total || 0);
        statAvailable.textContent = Number(stats.available || 0);
        statInUse.textContent = Number(stats.in_use || 0);
        statDamaged.textContent = Number(stats.damaged || 0);
    }

    function refreshSnapshot() {
        return fetch(endpoint('/PlayerInventory/snapshot'))
            .then(function (res) { return res.json(); })
            .then(function (payload) {
                if (!payload || payload.status !== 'success' || !payload.data) {
                    throw new Error((payload && payload.message) || 'Failed to load inventory');
                }

                renderItems(payload.data.items || []);
                renderOpenLogs(payload.data.open_logs || []);
                renderHistory(payload.data.history_logs || []);
                renderStats(payload.data.stats || {});
            });
    }

    function takeItem(itemId, quantity) {
        return fetch(endpoint('/PlayerInventory/take'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: itemId, quantity: quantity })
        })
            .then(function (res) { return res.json(); })
            .then(function (payload) {
                if (!payload || payload.status !== 'success') {
                    throw new Error((payload && payload.message) || 'Take action failed');
                }

                showToast(payload.message || 'Item taken successfully', false);
                return refreshSnapshot();
            });
    }

    function returnItem(logId) {
        return fetch(endpoint('/PlayerInventory/returnItem'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ log_id: logId })
        })
            .then(function (res) { return res.json(); })
            .then(function (payload) {
                if (!payload || payload.status !== 'success') {
                    throw new Error((payload && payload.message) || 'Return action failed');
                }

                showToast(payload.message || 'Item returned successfully', false);
                return refreshSnapshot();
            });
    }

    inventoryTableBody.addEventListener('click', function (event) {
        var target = event.target;
        if (!target.classList.contains('btn-take')) {
            return;
        }

        var row = target.closest('tr');
        if (!row) {
            return;
        }

        var itemId = Number(row.getAttribute('data-item-id') || 0);
        var qtyInput = row.querySelector('.qty-input');
        var quantity = Number(qtyInput ? qtyInput.value : 1);

        if (!itemId || quantity < 1) {
            showToast('Please enter a valid quantity', true);
            return;
        }

        takeItem(itemId, quantity).catch(function (error) {
            showToast(error.message || 'Take action failed', true);
        });
    });

    openLogsTableBody.addEventListener('click', function (event) {
        var target = event.target;
        if (!target.classList.contains('btn-return')) {
            return;
        }

        var row = target.closest('tr');
        if (!row) {
            return;
        }

        var logId = Number(row.getAttribute('data-log-id') || 0);
        if (!logId) {
            showToast('Invalid borrow record', true);
            return;
        }

        returnItem(logId).catch(function (error) {
            showToast(error.message || 'Return action failed', true);
        });
    });

    refreshSnapshot().catch(function (error) {
        showToast(error.message || 'Unable to load inventory', true);
    });

    setInterval(function () {
        refreshSnapshot().catch(function () {
            // Silent poll failures; user can continue with current UI state.
        });
    }, pollMs);
});
