// Meal Plan JavaScript

// Current meal data
const mealData = {
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

// Open edit modal
function editMeal(mealType) {
    const modal = document.getElementById('editModal');
    const modalTitle = document.getElementById('modalTitle');
    const mealTypeInput = document.getElementById('mealType');
    const mealItemsTextarea = document.getElementById('mealItems');
    
    // Set modal title
    modalTitle.textContent = `Edit ${mealType.charAt(0).toUpperCase() + mealType.slice(1)}`;
    
    // Set meal type
    mealTypeInput.value = mealType;
    
    // Set current meal items
    const items = mealData[mealType] || [];
    mealItemsTextarea.value = items.join('\n');
    
    // Show modal
    modal.classList.add('active');
}

// Close modal
function closeModal() {
    const modal = document.getElementById('editModal');
    modal.classList.remove('active');
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mealForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const mealType = document.getElementById('mealType').value;
        const mealItems = document.getElementById('mealItems').value;
        
        // Parse items (split by newline and filter empty lines)
        const items = mealItems
            .split('\n')
            .map(item => item.trim())
            .filter(item => item.length > 0);
        
        if (items.length === 0) {
            alert('Please add at least one meal item');
            return;
        }
        
        // Update meal data
        mealData[mealType] = items;
        
        // Update the display
        updateMealCard(mealType, items);
        
        // Close modal
        closeModal();
        
        // Show success message
        showSuccessMessage(`${mealType.charAt(0).toUpperCase() + mealType.slice(1)} updated successfully!`);
    });
});

// Update meal card display
function updateMealCard(mealType, items) {
    // Find the meal card
    const cards = document.querySelectorAll('.meal-card');
    let targetCard = null;
    
    cards.forEach(card => {
        const title = card.querySelector('.meal-title').textContent.toLowerCase();
        if (title === mealType) {
            targetCard = card;
        }
    });
    
    if (!targetCard) return;
    
    // Get the items list container
    const itemsList = targetCard.querySelector('.meal-items-list');
    
    // Clear existing items
    itemsList.innerHTML = '';
    
    // Add new items
    items.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'meal-item';
        itemDiv.textContent = item;
        itemsList.appendChild(itemDiv);
    });
}

// Show success message
function showSuccessMessage(message) {
    // Create success notification
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
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    // Add animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
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
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

console.log('Meal Plan page initialized successfully');