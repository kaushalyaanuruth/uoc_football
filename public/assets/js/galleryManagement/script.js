(function () {
    /**
     * Returns a DOM element by ID to keep selectors concise and consistent.
     */
    function byId(id) {
        return document.getElementById(id);
    }

    /**
     * Builds absolute endpoint URLs using root config injected from the PHP view.
     */
    function buildEndpoint(path) {
        const config = window.GALLERY_MANAGEMENT_CONFIG || {};
        return (config.root || '') + path;
    }

    const uploadForm = byId('uploadForm');
    const uploadArea = byId('uploadArea');
    const imageInput = byId('imageInput');
    const browseBtn = byId('browseBtn');
    const uploadBtn = byId('uploadBtn');
    const preview = byId('preview');
    const previewGrid = byId('previewGrid');
    const fileCount = byId('fileCount');
    const galleryGrid = byId('galleryGrid');
    const filterCategory = byId('filterCategory');
    const editModal = byId('editModal');
    const editForm = byId('editForm');
    const closeEditModalBtn = byId('closeEditModalBtn');
    const cancelEditBtn = byId('cancelEditBtn');
    const saveEditBtn = byId('saveEditBtn');
    const deleteModal = byId('deleteModal');
    const deleteForm = byId('deleteForm');
    const closeDeleteModalBtn = byId('closeDeleteModalBtn');
    const cancelDeleteBtn = byId('cancelDeleteBtn');
    const confirmDeleteBtn = byId('confirmDeleteBtn');

    let selectedFiles = [];

    /**
     * Displays a temporary success/error message at the top of the page.
     */
    function showMessage(message, type) {
        const old = document.querySelectorAll('.message');
        old.forEach(function (node) {
            node.remove();
        });

        const box = document.createElement('div');
        box.className = 'message ' + type + ' show';
        box.textContent = message;

        const container = document.querySelector('.container');
        if (container) {
            container.insertBefore(box, container.firstChild);
        }

        setTimeout(function () {
            box.classList.remove('show');
            setTimeout(function () {
                box.remove();
            }, 250);
        }, 3500);
    }

    /**
     * Updates message text and style inside modal forms.
     */
    function setFormMessage(element, message, type) {
        if (!element) {
            return;
        }

        element.textContent = message || '';
        element.className = 'form-message' + (type ? ' ' + type : '');
    }

    /**
     * Validates one file and returns an error string when invalid, otherwise empty string.
     */
    function validateFile(file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxBytes = 10 * 1024 * 1024;

        if (!validTypes.includes(file.type)) {
            return file.name + ': invalid file type';
        }

        if (file.size > maxBytes) {
            return file.name + ': exceeds 10MB limit';
        }

        return '';
    }

    /**
     * Stores valid files and immediately renders thumbnails so users can review selection.
     */
    function setSelectedFiles(files) {
        const validFiles = [];
        const errors = [];

        files.forEach(function (file) {
            const validationError = validateFile(file);
            if (validationError) {
                errors.push(validationError);
                return;
            }

            validFiles.push(file);
        });

        if (errors.length) {
            showMessage(errors.join(' | '), 'error');
        }

        selectedFiles = validFiles;
        renderPreview();
    }

    /**
     * Rebuilds the preview grid and allows removing individual files from the selection.
     */
    function renderPreview() {
        if (!preview || !previewGrid || !fileCount) {
            return;
        }

        previewGrid.innerHTML = '';

        if (!selectedFiles.length) {
            preview.style.display = 'none';
            fileCount.textContent = '';
            return;
        }

        selectedFiles.forEach(function (file, index) {
            const card = document.createElement('div');
            card.className = 'gallery-card';
            card.style.position = 'relative';

            const img = document.createElement('img');
            img.className = 'gallery-image';
            img.alt = file.name;
            img.src = URL.createObjectURL(file);

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'action-btn delete-btn';
            removeButton.style.position = 'absolute';
            removeButton.style.top = '8px';
            removeButton.style.right = '8px';
            removeButton.innerHTML = '<span class="material-symbols-outlined" aria-hidden="true">close</span>';
            removeButton.addEventListener('click', function () {
                selectedFiles.splice(index, 1);
                renderPreview();
            });

            card.appendChild(img);
            card.appendChild(removeButton);
            previewGrid.appendChild(card);
        });

        preview.style.display = 'block';
        fileCount.textContent = selectedFiles.length + ' image(s) selected';
    }

    /**
     * Sends selected images and form metadata to the uploadMultiple controller endpoint.
     */
    async function uploadImages() {
        if (!selectedFiles.length) {
            showMessage('Please select at least one image', 'error');
            return;
        }

        const categoryInput = byId('category');
        const descriptionInput = byId('description');
        const tagsInput = byId('tags');

        if (!categoryInput || !categoryInput.value) {
            showMessage('Please select a category', 'error');
            return;
        }

        uploadBtn.disabled = true;
        uploadBtn.textContent = 'Uploading...';

        const formData = new FormData();
        selectedFiles.forEach(function (file) {
            formData.append('images[]', file);
        });
        formData.append('category', categoryInput.value);
        formData.append('description', descriptionInput ? descriptionInput.value : '');
        formData.append('tags', tagsInput ? tagsInput.value : '');

        try {
            const response = await fetch(buildEndpoint('/galleryManagement/uploadMultiple'), {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Upload failed');
            }

            showMessage(result.message || 'Images uploaded successfully', 'success');
            setTimeout(function () {
                window.location.reload();
            }, 700);
        } catch (error) {
            showMessage(error.message, 'error');
            uploadBtn.disabled = false;
            uploadBtn.textContent = 'Upload Images';
        }
    }

    /**
     * Deletes a gallery image by ID through the controller delete endpoint.
     */
    async function deleteImage(id) {
        try {
            const response = await fetch(buildEndpoint('/galleryManagement/delete'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Delete failed');
            }

            showMessage(result.message || 'Image deleted successfully', 'success');
            setTimeout(function () {
                window.location.reload();
            }, 500);
            return true;
        } catch (error) {
            setFormMessage(byId('deleteFormMessage'), error.message, 'error');
            return false;
        }
    }

    /**
     * Opens the edit modal and pre-fills the form using data from the selected card.
     */
    function openEditModal(card, id) {
        if (!editModal || !card) {
            return;
        }

        byId('editImageId').value = id;
        byId('editDescription').value = card.getAttribute('data-description') || '';
        byId('editCategory').value = (card.getAttribute('data-category') || 'events').toLowerCase();
        byId('editTags').value = card.getAttribute('data-tags') || '';
        setFormMessage(byId('editFormMessage'), '', '');

        editModal.classList.add('active');
    }

    /**
     * Closes edit modal and clears temporary message state.
     */
    function closeEditModal() {
        if (!editModal || !editForm) {
            return;
        }

        editModal.classList.remove('active');
        editForm.reset();
        setFormMessage(byId('editFormMessage'), '', '');
        if (saveEditBtn) {
            saveEditBtn.disabled = false;
            saveEditBtn.textContent = 'Update Photo';
        }
    }

    /**
     * Sends edited photo metadata to the update endpoint from modal form values.
     */
    async function submitEditForm() {
        const id = byId('editImageId').value;
        const descriptionValue = byId('editDescription').value.trim();
        const categoryValue = byId('editCategory').value;
        const tagsValue = byId('editTags').value.trim();

        if (!id) {
            setFormMessage(byId('editFormMessage'), 'Image ID is missing', 'error');
            return;
        }

        saveEditBtn.disabled = true;
        saveEditBtn.textContent = 'Updating...';

        try {
            const response = await fetch(buildEndpoint('/galleryManagement/update'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: id,
                    description: descriptionValue,
                    category: categoryValue,
                    tags: tagsValue
                })
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Update failed');
            }

            setFormMessage(byId('editFormMessage'), result.message || 'Photo updated successfully', 'success');
            setTimeout(function () {
                window.location.reload();
            }, 600);
        } catch (error) {
            setFormMessage(byId('editFormMessage'), error.message, 'error');
            saveEditBtn.disabled = false;
            saveEditBtn.textContent = 'Update Photo';
        }
    }

    /**
     * Opens the delete modal and stores the target image id in hidden input.
     */
    function openDeleteModal(id) {
        if (!deleteModal) {
            return;
        }

        byId('deleteImageId').value = id;
        setFormMessage(byId('deleteFormMessage'), '', '');
        deleteModal.classList.add('active');
    }

    /**
     * Closes delete modal and clears temporary message state.
     */
    function closeDeleteModal() {
        if (!deleteModal || !deleteForm) {
            return;
        }

        deleteModal.classList.remove('active');
        deleteForm.reset();
        setFormMessage(byId('deleteFormMessage'), '', '');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.textContent = 'Delete Photo';
        }
    }

    /**
     * Submits delete request for the selected image id from delete modal form.
     */
    async function submitDeleteForm() {
        const id = byId('deleteImageId').value;
        if (!id) {
            setFormMessage(byId('deleteFormMessage'), 'Image ID is missing', 'error');
            return;
        }

        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.textContent = 'Deleting...';

        const deleted = await deleteImage(id);
        if (!deleted) {
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.textContent = 'Delete Photo';
        }
    }

    /**
     * Filters visible cards on the page by selected category without another server request.
     */
    function filterCardsByCategory() {
        if (!galleryGrid || !filterCategory) {
            return;
        }

        const selected = filterCategory.value;
        const cards = galleryGrid.querySelectorAll('.gallery-card');

        cards.forEach(function (card) {
            const cardCategory = (card.getAttribute('data-category') || '').toLowerCase();
            const matches = selected === 'all' || selected === cardCategory;
            card.style.display = matches ? '' : 'none';
        });
    }

    /**
     * Registers all page event listeners for uploading, filtering, and card actions.
     */
    function bindEvents() {
        if (browseBtn && imageInput) {
            browseBtn.addEventListener('click', function () {
                imageInput.click();
            });

            imageInput.addEventListener('change', function (event) {
                setSelectedFiles(Array.from(event.target.files || []));
            });
        }

        if (uploadArea) {
            uploadArea.addEventListener('dragover', function (event) {
                event.preventDefault();
                uploadArea.style.borderColor = '#7c3aed';
                uploadArea.style.background = '#faf5ff';
            });

            uploadArea.addEventListener('dragleave', function () {
                uploadArea.style.borderColor = '#d1d5db';
                uploadArea.style.background = '#fafafa';
            });

            uploadArea.addEventListener('drop', function (event) {
                event.preventDefault();
                uploadArea.style.borderColor = '#d1d5db';
                uploadArea.style.background = '#fafafa';
                setSelectedFiles(Array.from(event.dataTransfer.files || []));
            });
        }

        if (uploadForm) {
            uploadForm.addEventListener('submit', function (event) {
                event.preventDefault();
                uploadImages();
            });
        }

        if (filterCategory) {
            filterCategory.addEventListener('change', filterCardsByCategory);
        }

        if (galleryGrid) {
            galleryGrid.addEventListener('click', function (event) {
                const editBtn = event.target.closest('.edit-btn');
                if (editBtn) {
                    const id = editBtn.getAttribute('data-id');
                    const card = editBtn.closest('.gallery-card');
                    openEditModal(card, id);
                    return;
                }

                const deleteBtn = event.target.closest('.delete-btn');
                if (deleteBtn) {
                    openDeleteModal(deleteBtn.getAttribute('data-id'));
                }
            });
        }

        if (editForm) {
            editForm.addEventListener('submit', function (event) {
                event.preventDefault();
                submitEditForm();
            });
        }

        if (deleteForm) {
            deleteForm.addEventListener('submit', function (event) {
                event.preventDefault();
                submitDeleteForm();
            });
        }

        if (closeEditModalBtn) {
            closeEditModalBtn.addEventListener('click', closeEditModal);
        }

        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', closeEditModal);
        }

        if (closeDeleteModalBtn) {
            closeDeleteModalBtn.addEventListener('click', closeDeleteModal);
        }

        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', closeDeleteModal);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeEditModal();
                closeDeleteModal();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindEvents();
        filterCardsByCategory();
    });
})();
