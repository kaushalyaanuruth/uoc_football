// Coach Dashboard JavaScript

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeMealTabs();
    initializeNavigation();
});

// Meal tabs functionality
function initializeMealTabs() {
    const tabs = document.querySelectorAll('.meal-tab');
    
    const mealData = {
        breakfast: [
            'Basmati or Red Rice',
            'Chicken, Egg, Fish',
            'Vegetable(Minimum 3)',
            'Paip',
            'Yogurt'
        ],
        lunch: [
            'Brown Rice or Quinoa',
            'Grilled Chicken Breast',
            'Mixed Vegetables',
            'Fresh Salad',
            'Fruit Bowl'
        ],
        dinner: [
            'Whole Wheat Pasta',
            'Lean Beef or Fish',
            'Steamed Vegetables',
            'Green Salad',
            'Low-fat Yogurt'
        ]
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
            updateMealItems(mealData[mealType] || mealData.breakfast);
        });
    });
}

// Update meal items display
function updateMealItems(items) {
    const mealItemsContainer = document.getElementById('mealItems');
    if (!mealItemsContainer) return;
    
    // Clear existing items
    mealItemsContainer.innerHTML = '';
    
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