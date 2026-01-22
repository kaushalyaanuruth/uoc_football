/* ===============================
   Captain Meal Plan JS
   =============================== */

/* ---------- NAVIGATION ---------- */
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();

        // Active nav
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');

        // Pages
        document.querySelectorAll('.page-content').forEach(p => p.classList.remove('active'));
        const page = this.dataset.page;
        const target = document.getElementById(`${page}-page`);
        if (target) target.classList.add('active');

        if (page === 'meal-plan') {
            applyMealFilterFromActiveTab();
        }
    });
});

/* ---------- QUICK LINKS ---------- */
const mealPreview = document.getElementById('meal-plan-preview');
if (mealPreview) {
    mealPreview.addEventListener('click', () => {
        document.querySelectorAll('.page-content').forEach(p => p.classList.remove('active'));
        document.getElementById('meal-plan-page')?.classList.add('active');
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        applyMealFilterFromActiveTab();
    });
}

const perfPreview = document.getElementById('performance-preview');
if (perfPreview) {
    perfPreview.addEventListener('click', () => {
        document.querySelectorAll('.page-content').forEach(p => p.classList.remove('active'));
        document.getElementById('analyze-page')?.classList.add('active');
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    });
}

/* ---------- MEAL PLAN FILTER ---------- */
document.querySelectorAll('.meal-plan-tab').forEach(tab => {
    tab.addEventListener('click', function () {
        document.querySelectorAll('.meal-plan-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        const meal = this.dataset.meal;
        document.querySelectorAll('#meals-grid .meal-card').forEach(card => {
            card.style.display = (card.dataset.meal === meal) ? '' : 'none';
        });
    });
});

function applyMealFilterFromActiveTab() {
    const activeTab = document.querySelector('.meal-plan-tab.active');
    const meal = activeTab ? activeTab.dataset.meal : 'breakfast';

    document.querySelectorAll('#meals-grid .meal-card').forEach(card => {
        card.style.display = (card.dataset.meal === meal) ? '' : 'none';
    });
}

// Apply on page load
applyMealFilterFromActiveTab();

/* ---------- SCHEDULE FILTER ---------- */
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        document.querySelectorAll('.schedule-item').forEach(item => {
            item.style.display =
                (filter === 'all' || item.dataset.type === filter)
                    ? 'block'
                    : 'none';
        });
    });
});

/* ---------- SIMPLE MEAL TABS ---------- */
document.querySelectorAll('.meal-tab').forEach(tab => {
    tab.addEventListener('click', function () {
        document.querySelectorAll('.meal-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
    });
});

/* ---------- LOGOUT ---------- */
const logoutBtn = document.getElementById('logout-btn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
        if (!confirm('Are you sure you want to logout?')) return;

        try {
            localStorage.removeItem('authToken');
        } catch (e) {}

        // ROOT must be defined globally in HTML
        window.location.href = window.APP_ROOT + '/LandingPage';
    });
}
