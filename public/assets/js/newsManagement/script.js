// News Management JavaScript

// Global variables
let currentNewsId = null;
let isEditMode = false;

// DOM Elements
const modal = document.getElementById('newsModal');
const modalTitle = document.getElementById('modalTitle');
const newsForm = document.getElementById('newsForm');
const addNewsBtn = document.querySelector('.add-news-btn');
const closeBtn = document.querySelector('.close-btn');
const cancelBtn = document.querySelector('.btn-secondary');
const imageInput = document.getElementById('newsImage');
const imagePreview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
const removeImageBtn = document.getElementById('removeImageBtn');
const existingImageInput = document.getElementById('existingImage');

let currentImageData = null;

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('newsDate').value = today;
});

// Initialize event listeners
function initializeEventListeners() {
    // Add news button
    if (addNewsBtn) {
        addNewsBtn.addEventListener('click', openAddModal);
    }

    // Close modal buttons
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    // Click outside modal to close
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Form submission
    if (newsForm) {
        newsForm.addEventListener('submit', handleFormSubmit);
    }
    
    // Image input change
    if (imageInput) {
        imageInput.addEventListener('change', handleImageSelect);
    }
    
    // Remove image button
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', removeImage);
    }

    // Edit buttons - use event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-btn')) {
            const btn = e.target.closest('.edit-btn');
            const newsId = btn.getAttribute('data-id');
            if (newsId) {
                openEditModal(newsId);
            }
        }
    });

    // Delete buttons - use event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const newsId = btn.getAttribute('data-id');
            if (newsId) {
                deleteNews(newsId);
            }
        }
    });
}

// Open modal for adding new news
function openAddModal() {
    isEditMode = false;
    currentNewsId = null;
    currentImageData = null;
    
    if (modalTitle) {
        modalTitle.textContent = 'Add New Article';
    }
    
    // Reset form
    if (newsForm) {
        newsForm.reset();
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('newsDate').value = today;
    }
    
    // Reset image preview
    if (imagePreview) {
        imagePreview.style.display = 'none';
    }
    if (existingImageInput) {
        existingImageInput.value = '';
    }
    
    // Show modal
    if (modal) {
        modal.classList.add('show');
    }
}

// Open modal for editing news
function openEditModal(newsId) {
    isEditMode = true;
    currentNewsId = newsId;
    
    if (modalTitle) {
        modalTitle.textContent = 'Edit Article';
    }
    
    // Fetch news data
    fetchNewsData(newsId);
    
    // Show modal
    if (modal) {
        modal.classList.add('show');
    }
}

// Close modal
function closeModal() {
    if (modal) {
        modal.classList.remove('show');
    }
    
    // Reset form and state
    if (newsForm) {
        newsForm.reset();
    }
    
    // Reset image preview
    if (imagePreview) {
        imagePreview.style.display = 'none';
    }
    currentImageData = null;
    
    isEditMode = false;
    currentNewsId = null;
}

// Handle image selection
function handleImageSelect(e) {
    const file = e.target.files[0];
    
    if (!file) {
        return;
    }
    
    // Validate file type
    if (!file.type.match('image.*')) {
        showMessage('Please select an image file', 'error');
        e.target.value = '';
        return;
    }
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        showMessage('Image size must be less than 5MB', 'error');
        e.target.value = '';
        return;
    }
    
    // Read and preview image
    const reader = new FileReader();
    reader.onload = function(event) {
        currentImageData = event.target.result;
        
        if (previewImg) {
            previewImg.src = event.target.result;
        }
        if (imagePreview) {
            imagePreview.style.display = 'inline-block';
        }
    };
    reader.readAsDataURL(file);
}

// Remove image
function removeImage() {
    currentImageData = null;
    
    if (imageInput) {
        imageInput.value = '';
    }
    if (imagePreview) {
        imagePreview.style.display = 'none';
    }
    if (previewImg) {
        previewImg.src = '';
    }
    if (existingImageInput) {
        existingImageInput.value = '';
    }
}

// Handle form submission
async function fetchNewsData(newsId) {
    try {
        console.log('Fetching news data for ID:', newsId);
        
        const response = await fetch(`../newsManagement/get?id=${newsId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Fetched news data:', data);

        if (data.success) {
            populateForm(data.data);
        } else {
            showMessage('Failed to fetch news data: ' + (data.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error fetching news:', error);
        showMessage('Error fetching news data: ' + error.message, 'error');
    }
}

// Populate form with news data
function populateForm(newsData) {
    if (document.getElementById('newsHeading')) {
        document.getElementById('newsHeading').value = newsData.title || '';
    }
    
    if (document.getElementById('newsDate')) {
        document.getElementById('newsDate').value = newsData.publish_date || '';
    }
    
    if (document.getElementById('newsBody')) {
        document.getElementById('newsBody').value = newsData.content || '';
    }
    
    if (document.getElementById('newsStatus')) {
        document.getElementById('newsStatus').value = newsData.status || 'published';
    }
    
    // Handle existing image
    if (newsData.image && newsData.image !== '') {
        if (existingImageInput) {
            existingImageInput.value = newsData.image;
        }
        
        // Show image preview
        if (previewImg && imagePreview) {
            previewImg.src = `${window.location.origin}/uploads/news_images/${newsData.image}`;
            imagePreview.style.display = 'inline-block';
        }
    } else {
        // No existing image
        if (imagePreview) {
            imagePreview.style.display = 'none';
        }
    }
}

// Handle form submission
async function handleFormSubmit(e) {
    e.preventDefault();
    
    // Get form data
    const formData = {
        news_heading: document.getElementById('newsHeading').value.trim(),
        news_date: document.getElementById('newsDate').value,
        news_body: document.getElementById('newsBody').value.trim(),
        status: document.getElementById('newsStatus').value
    };
    
    // Add image data if available
    if (currentImageData) {
        formData.image_data = currentImageData;
    } else if (existingImageInput && existingImageInput.value) {
        formData.existing_image = existingImageInput.value;
    }

    console.log('Form data:', formData);

    // Validation
    if (!formData.news_heading) {
        showMessage('Please enter news heading', 'error');
        return;
    }

    if (!formData.news_date) {
        showMessage('Please select date', 'error');
        return;
    }

    try {
        let url, method;
        
        if (isEditMode && currentNewsId) {
            // Update existing news
            url = '../newsManagement/update';
            method = 'POST';
            formData.id = currentNewsId;
        } else {
            // Add new news
            url = '../newsManagement/add';
            method = 'POST';
        }

        console.log('Submitting to:', url);
        console.log('Method:', method);

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            showMessage(data.message || 'Article saved successfully!', 'success');
            
            setTimeout(() => {
                closeModal();
                window.location.reload();
            }, 500);
        } else {
            showMessage('Error: ' + (data.message || 'Failed to save article'), 'error');
        }
    } catch (error) {
        console.error('Error saving news:', error);
        showMessage('Error saving article: ' + error.message, 'error');
    }
}

// Delete news
async function deleteNews(newsId) {
    // Confirm deletion
    if (!confirm('Are you sure you want to delete this article? This action cannot be undone.')) {
        return;
    }

    try {
        console.log('Deleting news ID:', newsId);

        const response = await fetch('../newsManagement/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: newsId })
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            // Remove the news card from DOM immediately
            const newsCard = document.querySelector(`.news-card[data-news-id="${newsId}"]`);
            if (newsCard) {
                newsCard.style.opacity = '0';
                newsCard.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    newsCard.remove();
                    
                    // Check if there are no more news articles, show empty state
                    const newsGrid = document.querySelector('.news-grid');
                    if (newsGrid && newsGrid.children.length === 0) {
                        window.location.reload();
                    }
                }, 300);
            }
            
            showMessage(data.message || 'Article deleted successfully!', 'success');
        } else {
            showMessage('Error: ' + (data.message || 'Failed to delete article'), 'error');
        }
    } catch (error) {
        console.error('Error deleting news:', error);
        showMessage('Error deleting article: ' + error.message, 'error');
    }
}

// Show message
function showMessage(message, type = 'success') {
    // Check if message container exists in form
    let messageDiv = document.querySelector('#newsForm .message');
    
    if (!messageDiv) {
        // Create message div if it doesn't exist
        messageDiv = document.createElement('div');
        messageDiv.className = 'message';
        
        // Insert at the beginning of the form
        const form = document.getElementById('newsForm');
        if (form) {
            form.insertBefore(messageDiv, form.firstChild);
        }
    }
    
    // Set message content and type
    messageDiv.textContent = message;
    messageDiv.className = `message ${type} show`;
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        messageDiv.classList.remove('show');
    }, 5000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape key to close modal
    if (e.key === 'Escape' && modal && modal.classList.contains('show')) {
        closeModal();
    }
});

console.log('News Management script loaded successfully');
