// Gallery Management JavaScript

// DOM Elements
const uploadForm = document.getElementById('uploadForm');
const uploadArea = document.getElementById('uploadArea');
const imageInput = document.getElementById('imageInput');
const browseBtn = document.getElementById('browseBtn');
const uploadBtn = document.getElementById('uploadBtn');
const preview = document.getElementById('preview');
const previewGrid = document.getElementById('previewGrid');
const fileCount = document.getElementById('fileCount');
const galleryGrid = document.getElementById('galleryGrid');

// Store selected files
let selectedFiles = [];

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    setupUploadHandlers();
    setupCardActions();
});

// Upload Handlers
function setupUploadHandlers() {
    if (!browseBtn || !imageInput || !uploadForm || !uploadBtn) {
        console.error('Upload elements not found:', {
            browseBtn: !!browseBtn,
            imageInput: !!imageInput,
            uploadForm: !!uploadForm,
            uploadBtn: !!uploadBtn,
            uploadArea: !!uploadArea,
            preview: !!preview,
            previewGrid: !!previewGrid,
            fileCount: !!fileCount
        });
        return;
    }

    // Browse button click
    browseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        imageInput.click();
    });

    // File input change
    imageInput.addEventListener('change', function(e) {
        handleMultipleFiles(Array.from(e.target.files));
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#667eea';
        uploadArea.style.background = '#f8f9ff';
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#ddd';
        uploadArea.style.background = 'transparent';
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.style.borderColor = '#ddd';
        uploadArea.style.background = 'transparent';
        
        const files = Array.from(e.dataTransfer.files);
        if (files.length > 0) {
            handleMultipleFiles(files);
        }
    });

    // Form submit
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        uploadImage();
    });
}

// Handle Multiple Files
function handleMultipleFiles(files) {
    if (!files || files.length === 0) return;

    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
    const validFiles = [];
    const errors = [];

    // Validate each file
    files.forEach(file => {
        if (!validTypes.includes(file.type)) {
            errors.push(`${file.name}: Invalid file type`);
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            errors.push(`${file.name}: Exceeds 10MB limit`);
            return;
        }
        validFiles.push(file);
    });

    // Show errors if any
    if (errors.length > 0) {
        showMessage(errors.join('\n'), 'error');
    }

    if (validFiles.length === 0) return;

    // Store files
    selectedFiles = validFiles;

    // Show previews
    previewGrid.innerHTML = '';
    validFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.style.cssText = 'position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
            previewItem.setAttribute('data-file-index', index);
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width: 100%; height: 150px; object-fit: cover;';
            
            const name = document.createElement('p');
            name.textContent = file.name;
            name.style.cssText = 'font-size: 11px; padding: 5px; margin: 0; background: #f5f5f5; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;';
            
            // Remove button
            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '✕';
            removeBtn.style.cssText = 'position: absolute; top: 5px; right: 5px; width: 25px; height: 25px; border-radius: 50%; background: rgba(255,0,0,0.8); color: white; border: none; cursor: pointer; font-size: 16px; font-weight: bold; display: flex; align-items: center; justify-content: center; z-index: 10;';
            removeBtn.setAttribute('title', 'Remove this image');
            removeBtn.onclick = function(e) {
                e.preventDefault();
                removeFileFromSelection(index);
            };
            
            previewItem.appendChild(img);
            previewItem.appendChild(name);
            previewItem.appendChild(removeBtn);
            previewGrid.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });

    fileCount.textContent = `${validFiles.length} image(s) selected`;
    preview.style.display = 'block';
}

// Remove file from selection
function removeFileFromSelection(index) {
    // Remove from selectedFiles array
    selectedFiles.splice(index, 1);
    
    // Update preview
    if (selectedFiles.length > 0) {
        // Re-render previews with updated indices
        previewGrid.innerHTML = '';
        selectedFiles.forEach((file, newIndex) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.style.cssText = 'position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
                previewItem.setAttribute('data-file-index', newIndex);
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width: 100%; height: 150px; object-fit: cover;';
                
                const name = document.createElement('p');
                name.textContent = file.name;
                name.style.cssText = 'font-size: 11px; padding: 5px; margin: 0; background: #f5f5f5; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;';
                
                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '✕';
                removeBtn.style.cssText = 'position: absolute; top: 5px; right: 5px; width: 25px; height: 25px; border-radius: 50%; background: rgba(255,0,0,0.8); color: white; border: none; cursor: pointer; font-size: 16px; font-weight: bold; display: flex; align-items: center; justify-content: center; z-index: 10;';
                removeBtn.setAttribute('title', 'Remove this image');
                removeBtn.onclick = function(e) {
                    e.preventDefault();
                    removeFileFromSelection(newIndex);
                };
                
                previewItem.appendChild(img);
                previewItem.appendChild(name);
                previewItem.appendChild(removeBtn);
                previewGrid.appendChild(previewItem);
            };
            reader.readAsDataURL(file);
        });
        
        fileCount.textContent = `${selectedFiles.length} image(s) selected`;
    } else {
        // No files left, hide preview
        preview.style.display = 'none';
        fileCount.textContent = '';
        previewGrid.innerHTML = '';
    }
}

// Upload Image
function uploadImage() {
    console.log('Upload function called');
    console.log('Selected files:', selectedFiles);
    
    // Validate required fields
    if (!selectedFiles || selectedFiles.length === 0) {
        showMessage('Please select at least one image', 'error');
        return;
    }

    const category = document.getElementById('category').value;
    console.log('Category:', category);
    
    if (!category) {
        showMessage('Please select a category', 'error');
        return;
    }

    const description = document.getElementById('description').value;
    const tags = document.getElementById('tags').value;

    console.log('Starting upload...', { 
        fileCount: selectedFiles.length, 
        description, 
        category, 
        tags 
    });

    uploadBtn.disabled = true;
    uploadBtn.textContent = `Uploading ${selectedFiles.length} image(s)...`;

    // Create FormData and append all files
    const formData = new FormData();
    selectedFiles.forEach((file, index) => {
        console.log(`Adding file ${index}:`, file.name);
        formData.append('images[]', file);
    });
    formData.append('description', description);
    formData.append('category', category);
    formData.append('tags', tags);

    console.log('Sending request...');

    fetch('../galleryManagement/uploadMultiple', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text().then(text => {
            console.log('Response text:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response was:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        console.log('Parsed data:', data);
        if (data.success) {
            showMessage(`${data.uploaded} image(s) uploaded successfully!`, 'success');
            resetForm();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showMessage(data.message || 'Upload failed', 'error');
            uploadBtn.disabled = false;
            uploadBtn.textContent = 'Upload Images';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred during upload: ' + error.message, 'error');
        uploadBtn.disabled = false;
        uploadBtn.textContent = 'Upload Images';
    });
}

// Reset Form
function resetForm() {
    uploadForm.reset();
    preview.style.display = 'none';
    uploadBtn.style.display = 'none';
    imageInput.value = '';
    selectedFiles = [];
    previewGrid.innerHTML = '';
    fileCount.textContent = '';
}

// Filter Handlers
function setupFilterHandlers() {
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', applyFilters);
    }
}

function applyFilters() {
    const category = filterCategory ? filterCategory.value : '';
    const status = filterStatus ? filterStatus.value : '';
    const dateFrom = filterDateFrom ? filterDateFrom.value : '';
    const dateTo = filterDateTo ? filterDateTo.value : '';

    const params = new URLSearchParams();
    if (category) params.append('category', category);
    if (status) params.append('status', status);
    if (dateFrom) params.append('dateFrom', dateFrom);
    if (dateTo) params.append('dateTo', dateTo);

    const url = '../galleryManagement/filter?' + params.toString();
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderGallery(data.images);
            } else {
                showMessage('Filter failed', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while filtering', 'error');
        });
}

// Render Gallery
function renderGallery(images) {
    if (!images || images.length === 0) {
        galleryGrid.innerHTML = `
            <div class="empty-state">
                <div style="font-size: 64px; margin-bottom: 20px;">📷</div>
                <h3>No images found</h3>
                <p>Try adjusting your filters</p>
            </div>
        `;
        return;
    }

    galleryGrid.innerHTML = images.map(image => `
        <div class="gallery-card" data-image-id="${image.id}">
            <div class="card-image-container" style="position: relative;">
                <img src="../${image.filepath}" alt="${escapeHtml(image.description)}" class="gallery-image">
                <div class="card-overlay">
                    <button class="action-btn edit-btn" data-id="${image.id}" title="Edit">✏️</button>
                    <button class="action-btn delete-btn" data-id="${image.id}" title="Delete">🗑️</button>
                </div>
            </div>
            <div class="card-content">
                <p class="card-description">${escapeHtml(image.description)}</p>
                <div class="card-meta">
                    <span class="category-badge category-${image.category}">
                        ${capitalizeFirst(image.category)}
                    </span>
                    <span class="card-date">${formatDate(image.created_at)}</span>
                </div>
                ${image.tags ? `
                    <div class="card-tags">
                        ${image.tags.split(',').map(tag => `<span class="tag">${escapeHtml(tag.trim())}</span>`).join('')}
                    </div>
                ` : ''}
            </div>
        </div>
    `).join('');

    setupCardActions();
}

// Bulk Actions
function setupBulkActions() {
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.image-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', bulkDelete);
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.image-checkbox:checked');
    if (checkedBoxes.length === 0) {
        showMessage('Please select images to delete', 'error');
        return;
    }

    if (!confirm(`Are you sure you want to delete ${checkedBoxes.length} image(s)?`)) {
        return;
    }

    const ids = Array.from(checkedBoxes).map(cb => cb.value);

    fetch('../galleryManagement/bulkDelete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Images deleted successfully', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showMessage(data.message || 'Delete failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred during deletion', 'error');
    });
}

// Card Actions
function setupCardActions() {
    // Delete Button
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            deleteImage(id);
        });
    });

    // Edit Button
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            editImage(id);
        });
    });
}

// Delete Image
function deleteImage(id) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    fetch('../galleryManagement/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Image deleted successfully', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showMessage(data.message || 'Delete failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred during deletion', 'error');
    });
}

// Edit Image (placeholder - can be expanded with a modal)
function editImage(id) {
    // Find the card
    const card = document.querySelector(`[data-image-id="${id}"]`);
    if (!card) return;

    // Get current values
    const description = card.querySelector('.card-description').textContent;
    const category = card.querySelector('.category-badge').textContent.toLowerCase();
    const tags = Array.from(card.querySelectorAll('.tag')).map(t => t.textContent).join(', ');

    // Simple prompt-based edit (can be replaced with a modal)
    const newDescription = prompt('Edit Description:', description);
    if (newDescription === null) return;

    const newTags = prompt('Edit Tags (comma-separated):', tags);
    if (newTags === null) return;

    const data = {
        id: id,
        description: newDescription,
        tags: newTags
    };

    fetch('../galleryManagement/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Image updated successfully', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showMessage(data.message || 'Update failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred during update', 'error');
    });
}

// Utility Functions
function showMessage(message, type) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.message');
    existingMessages.forEach(msg => msg.remove());

    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type} show`;
    messageDiv.textContent = message;

    // Insert at the top of the container
    const container = document.querySelector('.container');
    container.insertBefore(messageDiv, container.firstChild);

    // Auto remove after 5 seconds
    setTimeout(() => {
        messageDiv.classList.remove('show');
        setTimeout(() => messageDiv.remove(), 300);
    }, 5000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
}
