/**
 * mealplan-script.js
 * Full CRUD for the Coach Meal Plan page.
 * Requires APP_ROOT to be set in the view before this script loads.
 */

// ── Helpers ──────────────────────────────────────────────────────────────────

function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

// Close any modal when clicking the backdrop
window.addEventListener('click', function (e) {
    ['addModal', 'editModal', 'deleteModal'].forEach(function (id) {
        const modal = document.getElementById(id);
        if (e.target === modal) closeModal(id);
    });
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        ['addModal', 'editModal', 'deleteModal'].forEach(closeModal);
    }
});

/**
 * Generic AJAX POST — returns a Promise that resolves with the JSON response.
 */
function ajaxPost(endpoint, payload) {
    const formData = new FormData();
    Object.keys(payload).forEach(function (k) { formData.append(k, payload[k]); });

    return fetch(APP_ROOT + '/coachMealPlan/' + endpoint, {
        method:  'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body:    formData,
    }).then(function (res) {
        if (!res.ok) throw new Error('Network error: ' + res.status);
        return res.json();
    });
}

// ── Notification ─────────────────────────────────────────────────────────────

function showNotification(message, type) {
    // type: 'success' | 'error'
    const colours = {
        success: { bg: 'linear-gradient(135deg,#10b981 0%,#059669 100%)', shadow: 'rgba(16,185,129,0.4)' },
        error:   { bg: 'linear-gradient(135deg,#ef4444 0%,#dc2626 100%)', shadow: 'rgba(239,68,68,0.4)' },
    };
    const c = colours[type] || colours.success;

    const n = document.createElement('div');
    n.style.cssText = [
        'position:fixed', 'top:20px', 'right:20px',
        'background:' + c.bg, 'color:white',
        'padding:16px 24px', 'border-radius:12px',
        'box-shadow:0 4px 12px ' + c.shadow,
        'z-index:10000', 'font-weight:600',
        'animation:notifSlideIn .3s ease',
    ].join(';');
    n.textContent = message;

    if (!document.getElementById('notif-keyframes')) {
        const s = document.createElement('style');
        s.id = 'notif-keyframes';
        s.textContent =
            '@keyframes notifSlideIn{from{transform:translateX(400px);opacity:0}to{transform:translateX(0);opacity:1}}';
        document.head.appendChild(s);
    }

    document.body.appendChild(n);
    setTimeout(function () {
        n.style.animation = 'notifSlideIn .3s ease reverse';
        setTimeout(function () { n.remove(); }, 300);
    }, 3000);
}

// ── DOM helpers ───────────────────────────────────────────────────────────────

/**
 * Build an item row element.
 */
function buildItemEl(mealType, id, meal, amount) {
    const div = document.createElement('div');
    div.className  = 'meal-item';
    div.dataset.id = id;
    div.innerHTML  = `
        <div class="meal-item-text">
            <span class="item-name">${escHtml(meal)}</span>
            <span class="item-amount">${escHtml(amount)}</span>
        </div>
        <div class="meal-item-actions">
            <button class="item-edit-btn" title="Edit"
                    onclick="openEditModal('${mealType}',${id},'${escJs(meal)}','${escJs(amount)}')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </button>
            <button class="item-delete-btn" title="Delete"
                    onclick="deleteItem('${mealType}',${id})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>
                    <path d="M9 6V4h6v2"/>
                </svg>
            </button>
        </div>`;
    return div;
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function escJs(str) {
    return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

function removeEmptyState(mealType) {
    const el = document.getElementById('empty-' + mealType);
    if (el) el.remove();
}

function maybeShowEmptyState(mealType) {
    const list = document.getElementById('list-' + mealType);
    if (list && list.querySelectorAll('.meal-item').length === 0) {
        if (!document.getElementById('empty-' + mealType)) {
            const d = document.createElement('div');
            d.className = 'empty-state';
            d.id        = 'empty-' + mealType;
            d.innerHTML = 'No items yet. Click <strong>+</strong> to add one.';
            list.appendChild(d);
        }
    }
}

// ── ADD ───────────────────────────────────────────────────────────────────────

function openAddModal(mealType) {
    document.getElementById('addMealType').value = mealType;
    document.getElementById('addModalTitle').textContent =
        'Add ' + capitalize(mealType) + ' Item';
    document.getElementById('addMeal').value   = '';
    document.getElementById('addAmount').value = '';
    openModal('addModal');
    document.getElementById('addMeal').focus();
}

function submitAdd() {
    const mealType = document.getElementById('addMealType').value;
    const meal     = document.getElementById('addMeal').value.trim();
    const amount   = document.getElementById('addAmount').value.trim();

    if (!meal || !amount) {
        showNotification('Please fill in both fields.', 'error');
        return;
    }

    ajaxPost('add', { meal_type: mealType, meal: meal, amount: amount })
        .then(function (res) {
            if (res.success) {
                const list = document.getElementById('list-' + mealType);
                removeEmptyState(mealType);
                list.appendChild(buildItemEl(mealType, res.item.id, res.item.meal, res.item.amount));
                closeModal('addModal');
                showNotification(res.message, 'success');
            } else {
                showNotification(res.message || 'Failed to add item.', 'error');
            }
        })
        .catch(function () { showNotification('Server error. Please try again.', 'error'); });
}

// ── EDIT ──────────────────────────────────────────────────────────────────────

function openEditModal(mealType, itemId, meal, amount) {
    document.getElementById('editMealType').value    = mealType;
    document.getElementById('editItemId').value      = itemId;
    document.getElementById('editModalTitle').textContent =
        'Edit ' + capitalize(mealType) + ' Item';
    document.getElementById('editMeal').value   = meal;
    document.getElementById('editAmount').value = amount;
    openModal('editModal');
    document.getElementById('editMeal').focus();
}

function submitEdit() {
    const mealType = document.getElementById('editMealType').value;
    const itemId   = document.getElementById('editItemId').value;
    const meal     = document.getElementById('editMeal').value.trim();
    const amount   = document.getElementById('editAmount').value.trim();

    if (!meal || !amount) {
        showNotification('Please fill in both fields.', 'error');
        return;
    }

    ajaxPost('update', { meal_type: mealType, item_id: itemId, meal: meal, amount: amount })
        .then(function (res) {
            if (res.success) {
                // Update the row in-place
                const row = document.querySelector(
                    '#list-' + mealType + ' .meal-item[data-id="' + itemId + '"]'
                );
                if (row) {
                    row.querySelector('.item-name').textContent   = res.item.meal;
                    row.querySelector('.item-amount').textContent = res.item.amount;
                    // Refresh onclick handlers with new values
                    row.querySelector('.item-edit-btn').setAttribute(
                        'onclick',
                        `openEditModal('${mealType}',${res.item.id},'${escJs(res.item.meal)}','${escJs(res.item.amount)}')`
                    );
                }
                closeModal('editModal');
                showNotification(res.message, 'success');
            } else {
                showNotification(res.message || 'Failed to update item.', 'error');
            }
        })
        .catch(function () { showNotification('Server error. Please try again.', 'error'); });
}

// ── DELETE ────────────────────────────────────────────────────────────────────

function deleteItem(mealType, itemId) {
    document.getElementById('deleteMealType').value = mealType;
    document.getElementById('deleteItemId').value   = itemId;
    openModal('deleteModal');
}

function confirmDelete() {
    const mealType = document.getElementById('deleteMealType').value;
    const itemId   = document.getElementById('deleteItemId').value;

    ajaxPost('delete', { meal_type: mealType, item_id: itemId })
        .then(function (res) {
            if (res.success) {
                const row = document.querySelector(
                    '#list-' + mealType + ' .meal-item[data-id="' + itemId + '"]'
                );
                if (row) row.remove();
                maybeShowEmptyState(mealType);
                closeModal('deleteModal');
                showNotification(res.message, 'success');
            } else {
                showNotification(res.message || 'Failed to delete item.', 'error');
            }
        })
        .catch(function () { showNotification('Server error. Please try again.', 'error'); });
}

// ── Utils ─────────────────────────────────────────────────────────────────────

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

console.log('Meal Plan CRUD initialised successfully.');