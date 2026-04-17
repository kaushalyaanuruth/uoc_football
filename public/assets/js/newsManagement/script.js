(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    const config = window.NEWS_MANAGEMENT_CONFIG || {};
    const root = config.root || '';

    const modal = byId('newsModal');
    const form = byId('newsForm');
    const modalTitle = byId('modalTitle');
    const formMessage = byId('formMessage');
    const submitBtn = byId('newsSubmitBtn');

    const openBtn = byId('openNewsModalBtn');
    const closeBtn = byId('closeNewsModalBtn');
    const cancelBtn = byId('cancelNewsModalBtn');

    const searchInput = byId('newsSearch');
    const listBody = byId('newsListBody');
    const emptyState = byId('emptyState');

    const imageInput = byId('newsImage');
    const existingImage = byId('existingImage');
    const imagePreview = byId('imagePreview');
    const previewImg = byId('previewImg');
    const removeImageBtn = byId('removeImageBtn');

    let isEditMode = false;
    let currentId = null;
    let currentImageData = null;

    function buildEndpoint(path) {
        return root + path;
    }

    function setMessage(text, type) {
        if (!formMessage) {
            return;
        }

        formMessage.textContent = text || '';
        formMessage.className = type ? 'form-message ' + type : 'form-message';
    }

    function clearMessage() {
        setMessage('', '');
    }

    function resetImageState() {
        currentImageData = null;
        if (imageInput) {
            imageInput.value = '';
        }
        if (existingImage) {
            existingImage.value = '';
        }
        if (previewImg) {
            previewImg.src = '';
        }
        if (imagePreview) {
            imagePreview.style.display = 'none';
        }
    }

    function openModal(mode) {
        if (!modal || !form) {
            return;
        }

        clearMessage();

        if (mode === 'add') {
            isEditMode = false;
            currentId = null;
            form.reset();
            byId('newsId').value = '';
            byId('newsDate').value = new Date().toISOString().slice(0, 10);
            modalTitle.textContent = 'Add News';
            submitBtn.textContent = 'Save News';
            resetImageState();
        }

        modal.classList.add('active');
    }

    function closeModal() {
        if (!modal || !form) {
            return;
        }

        modal.classList.remove('active');
        form.reset();
        clearMessage();
        isEditMode = false;
        currentId = null;
        byId('newsId').value = '';
        resetImageState();
    }

    function handleImageSelect(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) {
            return;
        }

        if (!file.type.match('image.*')) {
            setMessage('Please select an image file', 'error');
            imageInput.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            setMessage('Image size must be less than 5MB', 'error');
            imageInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            currentImageData = e.target.result;
            if (previewImg) {
                previewImg.src = currentImageData;
            }
            if (imagePreview) {
                imagePreview.style.display = 'flex';
            }
        };
        reader.readAsDataURL(file);
    }

    function collectFormData() {
        return {
            id: byId('newsId').value,
            title: byId('newsTitle').value.trim(),
            date: byId('newsDate').value,
            discription: byId('newsDescription').value.trim()
        };
    }

    function validateForm(data) {
        if (!data.title) {
            return 'Title is required';
        }
        if (!data.date) {
            return 'Date is required';
        }
        if (!data.discription) {
            return 'Description is required';
        }
        return '';
    }

    async function fetchNews(id) {
        const response = await fetch(buildEndpoint('/newsManagement/get?id=' + encodeURIComponent(id)), {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        return response.json();
    }

    function fillForm(item) {
        byId('newsId').value = item.id || '';
        byId('newsTitle').value = item.title || '';
        byId('newsDate').value = item.date || item.publish_date || '';
        byId('newsDescription').value = item.discription || item.content || '';

        if (item.image) {
            if (existingImage) {
                existingImage.value = item.image;
            }
            if (previewImg) {
                previewImg.src = buildEndpoint('/uploads/news_images/' + item.image);
            }
            if (imagePreview) {
                imagePreview.style.display = 'flex';
            }
        } else {
            resetImageState();
        }
    }

    async function openEditModal(id) {
        try {
            const result = await fetchNews(id);
            if (!result.success || !result.data) {
                throw new Error(result.message || 'Unable to load news article');
            }

            isEditMode = true;
            currentId = id;
            fillForm(result.data);
            modalTitle.textContent = 'Edit News';
            submitBtn.textContent = 'Update News';
            modal.classList.add('active');
        } catch (error) {
            setMessage(error.message, 'error');
        }
    }

    async function saveNews(data) {
        const endpoint = isEditMode && currentId ? '/newsManagement/update' : '/newsManagement/add';
        const payload = isEditMode && currentId ? { ...data, id: currentId } : data;

        if (currentImageData) {
            payload.image_data = currentImageData;
        } else if (existingImage && existingImage.value) {
            payload.existing_image = existingImage.value;
        }

        const response = await fetch(buildEndpoint(endpoint), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        return response.json();
    }

    async function handleSubmit(event) {
        event.preventDefault();

        const payload = collectFormData();
        const validationError = validateForm(payload);
        if (validationError) {
            setMessage(validationError, 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = isEditMode ? 'Updating...' : 'Saving...';

        try {
            const result = await saveNews(payload);
            if (!result.success) {
                throw new Error(result.message || 'Unable to save news article');
            }

            setMessage(result.message || 'Saved successfully', 'success');
            setTimeout(function () {
                window.location.reload();
            }, 350);
        } catch (error) {
            setMessage(error.message, 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = isEditMode ? 'Update News' : 'Save News';
        }
    }

    async function deleteNews(id) {
        const confirmed = window.confirm('Delete this news article? This action cannot be undone.');
        if (!confirmed) {
            return;
        }

        try {
            const response = await fetch(buildEndpoint('/newsManagement/delete'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Unable to delete news article');
            }

            window.location.reload();
        } catch (error) {
            setMessage(error.message, 'error');
        }
    }

    function filterRows() {
        if (!listBody) {
            return;
        }

        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        const rows = listBody.querySelectorAll('.news-item');

        let visibleCount = 0;
        rows.forEach(function (row) {
            const searchData = row.dataset.search || '';
            const fullText = (row.textContent || '').toLowerCase();
            const match = !query || searchData.includes(query) || fullText.includes(query);

            if (match) {
                row.style.display = 'grid';
                visibleCount += 1;
            } else {
                row.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.style.display = visibleCount ? 'none' : 'block';
        }
    }

    function bindEvents() {
        if (openBtn) {
            openBtn.addEventListener('click', function () {
                openModal('add');
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', resetImageState);
        }

        if (imageInput) {
            imageInput.addEventListener('change', handleImageSelect);
        }

        if (form) {
            form.addEventListener('submit', handleSubmit);
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterRows);
        }

        if (listBody) {
            listBody.addEventListener('click', function (event) {
                const editBtn = event.target.closest('.edit-btn');
                if (editBtn) {
                    openEditModal(editBtn.getAttribute('data-id'));
                    return;
                }

                const deleteBtn = event.target.closest('.delete-btn');
                if (deleteBtn) {
                    deleteNews(deleteBtn.getAttribute('data-id'));
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal && modal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindEvents();
        if (byId('newsDate')) {
            byId('newsDate').value = new Date().toISOString().slice(0, 10);
        }
        filterRows();
    });
})();