// Coach Dashboard JavaScript

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeMealTabs();
    initializeNavigation();
    initializeRealtimeDateTime();
});

function initializeRealtimeDateTime() {
    const update = () => {
        const now = new Date();

        const dateEl = document.getElementById('coachLiveDate');
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString(undefined, {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
        }

        const timeEl = document.getElementById('coachLiveTime');
        if (timeEl) {
            timeEl.textContent = now.toLocaleTimeString(undefined, {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }

        const mealDayLabel = document.getElementById('coachMealDayLabel');
        if (mealDayLabel) {
            const dayName = now.toLocaleDateString(undefined, { weekday: 'long' });
            mealDayLabel.textContent = `Showing ${dayName} meal plan`;
        }
    };

    update();
    setInterval(update, 1000);
}

// Meal tabs functionality
function initializeMealTabs() {
    const tabs = document.querySelectorAll('.meal-tab');
    const mealItemsContainer = document.getElementById('mealItems');
    if (!tabs.length || !mealItemsContainer) {
        return;
    }

    const dashboardData = window.COACH_DASHBOARD_DATA || {};
    const todayMealPlan = dashboardData.todayMealPlan || {};
    
    const mealData = {
        breakfast: Array.isArray(todayMealPlan.breakfast) ? todayMealPlan.breakfast : [],
        lunch: Array.isArray(todayMealPlan.lunch) ? todayMealPlan.lunch : [],
        dinner: Array.isArray(todayMealPlan.dinner) ? todayMealPlan.dinner : []
    };
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Get meal type from data attribute
            const mealType = this.getAttribute('data-meal');
            
            // Update meal items
            updateMealItems(mealData[mealType] || []);
        });
    });

    const activeTab = document.querySelector('.meal-tab.active') || tabs[0];
    if (activeTab) {
        const defaultMealType = activeTab.getAttribute('data-meal');
        updateMealItems(mealData[defaultMealType] || []);
    }
}

// Update meal items display
function updateMealItems(items) {
    const mealItemsContainer = document.getElementById('mealItems');
    if (!mealItemsContainer) return;
    
    // Clear existing items
    mealItemsContainer.innerHTML = '';
    
    if (!Array.isArray(items) || !items.length) {
        const mealItem = document.createElement('div');
        mealItem.className = 'meal-item';
        mealItem.innerHTML = '<span>No meal items set for this section.</span>';
        mealItemsContainer.appendChild(mealItem);
        return;
    }

    // Add new items
    items.forEach(item => {
        const mealItem = document.createElement('div');
        mealItem.className = 'meal-item';
        mealItem.innerHTML = `
            <span>${item}</span>
            <i class="meal-icon">⋮</i>
        `;
        mealItemsContainer.appendChild(mealItem);
    });
}

// Navigation active state handler
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't prevent default - allow navigation
            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
        });
    });
}

// Add button click handlers
const addButtons = document.querySelectorAll('.add-btn');
addButtons.forEach(button => {
    button.addEventListener('click', function() {
        const cardTitle = this.closest('.card').querySelector('.card-title h2').textContent;
        alert(`Add new ${cardTitle.replace(/s$/, '')}`);
    });
});

// Console log for debugging
console.log('Coach Dashboard initialized successfully');