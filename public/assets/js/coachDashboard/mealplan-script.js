const STORAGE_KEY = 'uoc_football_weekly_meal_plan_v2';
const LEGACY_STORAGE_KEY = 'uoc_football_meal_plan_v1';
const MEAL_TYPES = ['breakfast', 'lunch', 'dinner'];
const DAYS_OF_WEEK = [
    { key: 'monday', label: 'Monday' },
    { key: 'tuesday', label: 'Tuesday' },
    { key: 'wednesday', label: 'Wednesday' },
    { key: 'thursday', label: 'Thursday' },
    { key: 'friday', label: 'Friday' },
    { key: 'saturday', label: 'Saturday' },
    { key: 'sunday', label: 'Sunday' }
];

const defaultDayPlan = {
    breakfast: [
        'Basmati or Red Rice',
        'Chicken, Egg, Fish',
        'Vegetable(minimum 3)',
        'Paip',
        'Yogurt',
        'Fruits'
    ],
    lunch: [
        'Basmati or Red Rice',
        'Chicken, Egg, Fish',
        'Vegetable(minimum 3)',
        'Paip',
        'Yogurt',
        'Fruits'
    ],
    dinner: [
        'Basmati or Red Rice',
        'Chicken, Egg, Fish',
        'Vegetable(minimum 3)',
        'Paip',
        'Yogurt',
        'Fruits'
    ]
};

const defaultWeeklyPlan = buildDefaultWeeklyPlan();

let weeklyMealPlan = clone(defaultWeeklyPlan);
let selectedDay = 'monday';
let currentMealType = '';
let currentModalMode = 'edit';
let modalDraftItems = [];
let initialItemCount = 0;

function getApiBase() {
    return String(window.COACH_MEALPLAN_API_BASE || '/coachMealPlan').replace(/\/+$/, '');
}

function getInitialServerPlan() {
    return window.COACH_MEALPLAN_INITIAL && typeof window.COACH_MEALPLAN_INITIAL === 'object'
        ? window.COACH_MEALPLAN_INITIAL
        : null;
}

function clone(value) {
    return JSON.parse(JSON.stringify(value));
}

function buildDefaultWeeklyPlan() {
    const weekly = {};
    DAYS_OF_WEEK.forEach(function (day) {
        weekly[day.key] = clone(defaultDayPlan);
    });
    return weekly;
}

function getDayLabel(dayKey) {
    const matched = DAYS_OF_WEEK.find(function (day) {
        return day.key === dayKey;
    });

    return matched ? matched.label : 'Monday';
}

function getMealLabel(mealType) {
    return mealType.charAt(0).toUpperCase() + mealType.slice(1);
}

function isValidDay(dayKey) {
    return DAYS_OF_WEEK.some(function (day) {
        return day.key === dayKey;
    });
}

function normalizeItems(items) {
    if (!Array.isArray(items)) {
        return [];
    }

    const normalized = [];
    const seen = new Set();

    items.forEach(function (item) {
        const value = String(item || '').trim();
        const key = value.toLowerCase();

        if (!value || seen.has(key)) {
            return;
        }

        seen.add(key);
        normalized.push(value);
    });

    return normalized;
}

function normalizeDayPlan(rawDayPlan) {
    const dayPlan = {};

    MEAL_TYPES.forEach(function (mealType) {
        dayPlan[mealType] = normalizeItems(rawDayPlan && rawDayPlan[mealType]);
    });

    return dayPlan;
}

function normalizeWeeklyPlan(rawWeeklyPlan) {
    const weekly = clone(defaultWeeklyPlan);

    DAYS_OF_WEEK.forEach(function (day) {
        weekly[day.key] = normalizeDayPlan(rawWeeklyPlan && rawWeeklyPlan[day.key]);
    });

    return weekly;
}

function applyTeamPlanToWeek(teamPlan) {
    if (!teamPlan || typeof teamPlan !== 'object') {
        return;
    }

    weeklyMealPlan = normalizeWeeklyPlan(teamPlan);
}

async function fetchServerPlan() {
    const response = await fetch(getApiBase() + '/data', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    });

    if (!response.ok) {
        throw new Error('Failed to fetch meal plan data.');
    }

    const payload = await response.json();
    if (!payload || payload.success !== true || !payload.plan || typeof payload.plan !== 'object') {
        throw new Error('Invalid meal plan data response.');
    }

    return payload.plan;
}

async function persistMealTypeToServer(mealType, items, day) {
    const response = await fetch(getApiBase() + '/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            day: day,
            mealType: mealType,
            items: items
        })
    });

    if (!response.ok) {
        throw new Error('Unable to save meal plan to database.');
    }

    const payload = await response.json();
    if (!payload || payload.success !== true || !payload.plan || typeof payload.plan !== 'object') {
        throw new Error(payload && payload.message ? payload.message : 'Invalid save response.');
    }

    return payload.plan;
}

async function resetPlanOnServer() {
    const response = await fetch(getApiBase() + '/reset', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        }
    });

    if (!response.ok) {
        throw new Error('Unable to reset meal plan in database.');
    }

    const payload = await response.json();
    if (!payload || payload.success !== true || !payload.plan || typeof payload.plan !== 'object') {
        throw new Error(payload && payload.message ? payload.message : 'Invalid reset response.');
    }

    return payload.plan;
}

function buildWeeklyPlanFromLegacy(rawLegacyState) {
    const migratedDay = clone(defaultDayPlan);

    MEAL_TYPES.forEach(function (mealType) {
        const legacyValue = rawLegacyState ? rawLegacyState[mealType] : null;

        if (Array.isArray(legacyValue)) {
            migratedDay[mealType] = normalizeItems(legacyValue);
            return;
        }

        if (legacyValue && typeof legacyValue === 'object') {
            migratedDay[mealType] = normalizeItems(legacyValue.items);
        }
    });

    const weekly = {};
    DAYS_OF_WEEK.forEach(function (day) {
        weekly[day.key] = clone(migratedDay);
    });

    return weekly;
}

function loadMealPlanState() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (raw) {
            const parsed = JSON.parse(raw);

            if (parsed && typeof parsed === 'object' && parsed.plan) {
                weeklyMealPlan = normalizeWeeklyPlan(parsed.plan);
                selectedDay = isValidDay(parsed.selectedDay) ? parsed.selectedDay : 'monday';
                return;
            }

            if (parsed && typeof parsed === 'object') {
                weeklyMealPlan = normalizeWeeklyPlan(parsed);
                selectedDay = 'monday';
                return;
            }
        }
    } catch (error) {
        console.warn('Unable to load weekly meal plan state:', error);
    }

    try {
        const legacyRaw = localStorage.getItem(LEGACY_STORAGE_KEY);
        if (legacyRaw) {
            const parsedLegacy = JSON.parse(legacyRaw);
            weeklyMealPlan = buildWeeklyPlanFromLegacy(parsedLegacy);
            selectedDay = 'monday';
            return;
        }
    } catch (error) {
        console.warn('Unable to migrate old meal plan state:', error);
    }

    weeklyMealPlan = clone(defaultWeeklyPlan);
    selectedDay = 'monday';
}

function saveMealPlanState() {
    try {
        const payload = {
            selectedDay: selectedDay,
            plan: weeklyMealPlan
        };

        localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    } catch (error) {
        console.warn('Unable to save weekly meal plan state:', error);
    }
}

function setFormError(message) {
    const errorBox = document.getElementById('mealFormError');
    if (!errorBox) {
        return;
    }

    errorBox.textContent = message || '';
}

function clearFormError() {
    setFormError('');
}

function renderSelectedDayLabel() {
    const daySelect = document.getElementById('selectedDay');
    const dayLabel = document.getElementById('selectedDayLabel');

    if (daySelect) {
        daySelect.value = selectedDay;
    }

    if (dayLabel) {
        dayLabel.textContent = 'Meal plan for ' + getDayLabel(selectedDay);
    }
}

function renderMealCard(mealType) {
    const mealList = document.querySelector('.meal-items-list[data-meal-list="' + mealType + '"]');
    if (!mealList) {
        return;
    }

    mealList.innerHTML = '';

    const currentDayPlan = weeklyMealPlan[selectedDay] || defaultDayPlan;
    const items = currentDayPlan[mealType] || [];

    if (!items.length) {
        const emptyState = document.createElement('div');
        emptyState.className = 'meal-empty-state';
        emptyState.textContent = 'No meals added yet for this section.';
        mealList.appendChild(emptyState);
        return;
    }

    items.forEach(function (item, index) {
        const itemNode = document.createElement('div');
        itemNode.className = 'meal-item';
        itemNode.setAttribute('role', 'listitem');

        const itemText = document.createElement('span');
        itemText.className = 'meal-item-text';
        itemText.textContent = item;

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'meal-item-delete';
        deleteButton.setAttribute('aria-label', 'Delete meal item');
        deleteButton.textContent = 'x';
        deleteButton.addEventListener('click', function () {
            deleteMealItem(mealType, index);
        });

        itemNode.appendChild(itemText);
        itemNode.appendChild(deleteButton);
        mealList.appendChild(itemNode);
    });
}

function renderAllMealCards() {
    MEAL_TYPES.forEach(function (mealType) {
        renderMealCard(mealType);
    });
}

function renderModalItems() {
    const modalItemList = document.getElementById('modalItemList');
    if (!modalItemList) {
        return;
    }

    modalItemList.innerHTML = '';

    if (!modalDraftItems.length) {
        const emptyState = document.createElement('div');
        emptyState.className = 'modal-item-empty';
        emptyState.textContent = 'No items added yet.';
        modalItemList.appendChild(emptyState);
        return;
    }

    modalDraftItems.forEach(function (item, index) {
        const row = document.createElement('div');
        row.className = 'modal-item-row';

        const rowText = document.createElement('span');
        rowText.className = 'modal-item-text';
        rowText.textContent = item;

        const rowDelete = document.createElement('button');
        rowDelete.type = 'button';
        rowDelete.className = 'modal-item-delete';
        rowDelete.textContent = 'x';
        rowDelete.setAttribute('aria-label', 'Remove meal item from draft list');
        rowDelete.addEventListener('click', function () {
            removeModalItem(index);
        });

        row.appendChild(rowText);
        row.appendChild(rowDelete);
        modalItemList.appendChild(row);
    });
}

function removeModalItem(index) {
    modalDraftItems = modalDraftItems.filter(function (_, itemIndex) {
        return itemIndex !== index;
    });
    renderModalItems();
}

function addModalItem() {
    const newMealItemInput = document.getElementById('newMealItem');
    if (!newMealItemInput) {
        return false;
    }

    const value = newMealItemInput.value.trim();
    if (!value) {
        setFormError('Enter a meal item before adding.');
        return false;
    }

    const exists = modalDraftItems.some(function (item) {
        return item.toLowerCase() === value.toLowerCase();
    });

    if (exists) {
        setFormError('This meal already exists in this section.');
        return false;
    }

    modalDraftItems.push(value);
    newMealItemInput.value = '';
    clearFormError();
    renderModalItems();
    newMealItemInput.focus();
    return true;
}

function openMealModal(mealType, modalMode) {
    currentMealType = mealType;
    currentModalMode = modalMode;

    const currentDayPlan = weeklyMealPlan[selectedDay] || defaultDayPlan;
    modalDraftItems = (currentDayPlan[mealType] || []).slice();
    initialItemCount = modalDraftItems.length;

    const modal = document.getElementById('editModal');
    const modalTitle = document.getElementById('modalTitle');
    const mealDayInput = document.getElementById('mealDay');
    const mealTypeInput = document.getElementById('mealType');
    const saveMealBtn = document.getElementById('saveMealBtn');
    const modalModeInput = document.getElementById('modalMode');
    const newMealItemInput = document.getElementById('newMealItem');

    if (!modal || !modalTitle || !mealDayInput || !mealTypeInput || !saveMealBtn || !modalModeInput || !newMealItemInput) {
        return;
    }

    mealDayInput.value = getDayLabel(selectedDay);
    mealTypeInput.value = getMealLabel(mealType);
    modalModeInput.value = modalMode;
    modalTitle.textContent = modalMode === 'create'
        ? 'Add Meal - ' + getMealLabel(mealType)
        : 'Edit ' + getMealLabel(mealType);
    saveMealBtn.textContent = modalMode === 'create' ? 'Add Meal' : 'Save Changes';
    newMealItemInput.value = '';

    clearFormError();
    renderModalItems();

    modal.classList.add('active');
    newMealItemInput.focus();
}

function openCreateModal(mealType) {
    openMealModal(mealType, 'create');
}

function editMeal(mealType) {
    openMealModal(mealType, 'edit');
}

function closeModal() {
    const modal = document.getElementById('editModal');
    if (!modal) {
        return;
    }

    modal.classList.remove('active');
    clearFormError();
    currentMealType = '';
    currentModalMode = 'edit';
    modalDraftItems = [];
    initialItemCount = 0;
}

function deleteMealItem(mealType, itemIndex) {
    const dayPlan = weeklyMealPlan[selectedDay];
    if (!dayPlan || !Array.isArray(dayPlan[mealType])) {
        return;
    }

    const itemToDelete = dayPlan[mealType][itemIndex];
    if (typeof itemToDelete === 'undefined') {
        return;
    }

    dayPlan[mealType].splice(itemIndex, 1);
    saveMealPlanState();
    renderMealCard(mealType);
    showSuccessMessage('Deleted meal item from ' + getMealLabel(mealType) + '.');
}

async function handleMealFormSubmit(event) {
    event.preventDefault();

    if (!currentMealType) {
        return;
    }

    const newMealItemInput = document.getElementById('newMealItem');
    if (newMealItemInput && newMealItemInput.value.trim()) {
        const itemAdded = addModalItem();
        if (!itemAdded) {
            return;
        }
    }

    if (!modalDraftItems.length) {
        setFormError('Add at least one meal item.');
        return;
    }

    if (currentModalMode === 'create' && modalDraftItems.length === initialItemCount) {
        setFormError('Add a new meal item before saving.');
        return;
    }

    weeklyMealPlan[selectedDay][currentMealType] = modalDraftItems.slice();

    try {
        const savedPlan = await persistMealTypeToServer(currentMealType, modalDraftItems.slice(), selectedDay);
        applyTeamPlanToWeek(savedPlan);
        saveMealPlanState();
        renderAllMealCards();
    } catch (error) {
        setFormError('Failed to save to database. Please try again.');
        console.warn(error);
        return;
    }

    const actionText = currentModalMode === 'create' ? 'added' : 'updated';
    showSuccessMessage(
        getMealLabel(currentMealType) + ' meals ' + actionText + ' for ' + getDayLabel(selectedDay) + '.'
    );

    closeModal();
}

function changeSelectedDay(dayKey) {
    if (!isValidDay(dayKey)) {
        return;
    }

    selectedDay = dayKey;
    saveMealPlanState();
    renderSelectedDayLabel();
    renderAllMealCards();
}

async function resetWeeklyPlan() {
    const confirmed = window.confirm('Reset all meal plans for Monday to Sunday?');
    if (!confirmed) {
        return;
    }

    try {
        const resetPlan = await resetPlanOnServer();
        weeklyMealPlan = clone(defaultWeeklyPlan);
        applyTeamPlanToWeek(resetPlan);
        selectedDay = 'monday';
        saveMealPlanState();
        renderSelectedDayLabel();
        renderAllMealCards();
        closeModal();
        showSuccessMessage('Weekly meal plan reset successfully.');
    } catch (error) {
        console.warn(error);
        showSuccessMessage('Unable to reset meal plan right now.');
    }
}

function ensureNotificationStyle() {
    if (document.getElementById('mealplan-notification-style')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'mealplan-notification-style';
    style.textContent = `
        @keyframes mealPlanSlideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
}

function showSuccessMessage(message) {
    ensureNotificationStyle();

    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        z-index: 10000;
        font-weight: 600;
        animation: mealPlanSlideIn 0.3s ease;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(function () {
        notification.style.animation = 'mealPlanSlideIn 0.3s ease reverse';
        setTimeout(function () {
            notification.remove();
        }, 300);
    }, 3000);
}

window.addEventListener('click', function (event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        closeModal();
    }
});

document.addEventListener('keydown', function (event) {
    const modal = document.getElementById('editModal');
    if (event.key === 'Escape' && modal && modal.classList.contains('active')) {
        closeModal();
    }
});

document.addEventListener('DOMContentLoaded', async function () {
    loadMealPlanState();

    try {
        const initialServerPlan = getInitialServerPlan();
        if (initialServerPlan) {
            applyTeamPlanToWeek(initialServerPlan);
        } else {
            const serverPlan = await fetchServerPlan();
            applyTeamPlanToWeek(serverPlan);
        }
    } catch (error) {
        console.warn('Using local meal plan state. DB sync failed:', error);
    }

    saveMealPlanState();
    renderSelectedDayLabel();
    renderAllMealCards();

    const mealForm = document.getElementById('mealForm');
    const addMealItemBtn = document.getElementById('addMealItemBtn');
    const newMealItemInput = document.getElementById('newMealItem');

    if (mealForm) {
        mealForm.addEventListener('submit', handleMealFormSubmit);
    }

    if (addMealItemBtn) {
        addMealItemBtn.addEventListener('click', function () {
            addModalItem();
        });
    }

    if (newMealItemInput) {
        newMealItemInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                addModalItem();
            }
        });
    }

    console.log('Weekly Meal Plan page initialized successfully');
});