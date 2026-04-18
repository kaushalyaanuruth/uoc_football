(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function normalizeType(value) {
        const type = (value || '').toLowerCase();
        if (['match', 'training', 'meeting', 'other'].includes(type)) {
            return type;
        }
        return 'other';
    }

    const config = window.EVENT_MANAGEMENT_CONFIG || {};
    const root = config.root || '';

    const modal = byId('eventModal');
    const eventForm = byId('eventForm');
    const modalTitle = byId('modalTitle');
    const formMessage = byId('formMessage');
    const eventSubmitBtn = byId('eventSubmitBtn');

    const openModalBtn = byId('openEventModalBtn');
    const closeModalBtn = byId('closeEventModalBtn');
    const cancelModalBtn = byId('cancelEventModalBtn');

    const searchInput = byId('eventSearch');
    const typeFilter = byId('typeFilter');
    const tableBody = byId('eventsTableBody');
    const eventImageInput = byId('eventImage');
    const eventImagePreview = byId('eventImagePreview');
    const eventImagePreviewImg = byId('eventImagePreviewImg');

    let isEditMode = false;
    let currentEventId = null;
    let selectedImageBase64 = null;

    function clearMessage() {
        if (!formMessage) {
            return;
        }
        formMessage.textContent = '';
        formMessage.classList.remove('error', 'success');
    }

    function setMessage(text, type) {
        if (!formMessage) {
            return;
        }
        formMessage.textContent = text;
        formMessage.classList.remove('error', 'success');
        if (type) {
            formMessage.classList.add(type);
        }
    }

    function openModal(mode) {
        if (!modal || !eventForm) {
            return;
        }

        clearMessage();

        if (mode === 'add') {
            isEditMode = false;
            currentEventId = null;
            eventForm.reset();
            byId('eventId').value = '';
            modalTitle.textContent = 'Add Event';
            eventSubmitBtn.textContent = 'Save Event';
            selectedImageBase64 = null;
            if (eventImagePreview) {
                eventImagePreview.style.display = 'none';
            }
        }

        modal.classList.add('active');
    }

    function closeModal() {
        if (!modal || !eventForm) {
            return;
        }
        modal.classList.remove('active');
        eventForm.reset();
        clearMessage();
        isEditMode = false;
        currentEventId = null;
        byId('eventId').value = '';
        selectedImageBase64 = null;
        if (eventImagePreview) {
            eventImagePreview.style.display = 'none';
        }
    }

    function buildEndpoint(path) {
        return root + path;
    }

    function handleImageSelect(event) {
        const file = event.target.files[0];
        if (!file) {
            selectedImageBase64 = null;
            eventImagePreview.style.display = 'none';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            selectedImageBase64 = e.target.result;
            eventImagePreviewImg.src = selectedImageBase64;
            eventImagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    async function fetchEvent(eventId) {
        const url = buildEndpoint('/eventManagement/get?id=' + encodeURIComponent(eventId));
        const response = await fetch(url, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        return response.json();
    }

    function toDateTimeLocal(eventDate, eventTime) {
        if (!eventDate) {
            return '';
        }
        const timePart = (eventTime || '00:00:00').slice(0, 5);
        return eventDate + 'T' + timePart;
    }

    function fillForm(data) {
        byId('eventId').value = data.id || data.event_id || '';
        byId('eventTitle').value = data.title || '';
        byId('eventDate').value = toDateTimeLocal(data.date || data.event_date, data.event_time);
        byId('eventCategory').value = normalizeType(data.event_type);
        byId('eventStatus').value = (data.status || 'upcoming').toLowerCase();
        byId('eventLocation').value = data.location || '';
        byId('eventDescription').value = data.description || '';
    }

    async function openEditModal(eventId) {
        try {
            clearMessage();
            const result = await fetchEvent(eventId);

            if (!result.success || !result.data) {
                throw new Error(result.message || 'Unable to load event');
            }

            isEditMode = true;
            currentEventId = eventId;
            selectedImageBase64 = null;
            fillForm(result.data);
            modalTitle.textContent = 'Edit Event';
            eventSubmitBtn.textContent = 'Update Event';

            if (eventImagePreview) {
                eventImagePreview.style.display = 'none';
            }

            modal.classList.add('active');
        } catch (error) {
            alert(error.message);
        }
    }

    function collectFormData() {
        const data = {
            id: byId('eventId').value,
            title: byId('eventTitle').value.trim(),
            event_date: byId('eventDate').value,
            category: normalizeType(byId('eventCategory').value),
            status: (byId('eventStatus').value || 'upcoming').toLowerCase(),
            location: byId('eventLocation').value.trim(),
            description: byId('eventDescription').value.trim()
        };

        if (selectedImageBase64) {
            data.image_data = selectedImageBase64;
        }

        return data;
    }

    function validateForm(formData) {
        if (!formData.title) {
            return 'Event title is required';
        }
        if (!formData.event_date) {
            return 'Date and time are required';
        }
        if (!formData.location) {
            return 'Location is required';
        }
        return '';
    }

    async function saveEvent(formData) {
        const isUpdate = isEditMode && currentEventId;
        const endpoint = isUpdate ? '/eventManagement/update' : '/eventManagement/add';
        let payload = isUpdate
            ? {
                id: currentEventId,
                title: formData.title,
                event_date: formData.event_date,
                category: formData.category,
                status: formData.status,
                location: formData.location,
                description: formData.description
            }
            : formData;

        if (isUpdate && selectedImageBase64) {
            payload.image_data = selectedImageBase64;
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

        const formData = collectFormData();
        const validationError = validateForm(formData);
        if (validationError) {
            setMessage(validationError, 'error');
            return;
        }

        eventSubmitBtn.disabled = true;
        eventSubmitBtn.textContent = isEditMode ? 'Updating...' : 'Saving...';

        try {
            const result = await saveEvent(formData);
            if (!result.success) {
                throw new Error(result.message || 'Unable to save event');
            }

            setMessage(result.message || 'Event saved successfully', 'success');
            setTimeout(function () {
                window.location.reload();
            }, 450);
        } catch (error) {
            setMessage(error.message, 'error');
            eventSubmitBtn.disabled = false;
            eventSubmitBtn.textContent = isEditMode ? 'Update Event' : 'Save Event';
        }
    }

    async function deleteEvent(eventId) {
        const confirmed = window.confirm('Delete this event? This action cannot be undone.');
        if (!confirmed) {
            return;
        }

        try {
            const response = await fetch(buildEndpoint('/eventManagement/delete'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: eventId })
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Unable to delete event');
            }

            window.location.reload();
        } catch (error) {
            alert(error.message);
        }
    }

    function ensureNoResultsRow() {
        const existing = byId('noResultsRow');
        if (existing) {
            existing.remove();
        }

        const row = document.createElement('tr');
        row.id = 'noResultsRow';

        const cell = document.createElement('td');
        cell.colSpan = 5;
        cell.className = 'no-results';
        cell.textContent = 'No events match your filters.';

        row.appendChild(cell);
        tableBody.appendChild(row);
    }

    function filterRows() {
        if (!tableBody) {
            return;
        }

        const query = (searchInput.value || '').trim().toLowerCase();
        const selectedType = (typeFilter.value || 'all').toLowerCase();
        const rows = tableBody.querySelectorAll('tr.event-row');

        let visibleCount = 0;
        rows.forEach(function (row) {
            const title = (row.dataset.title || '').toLowerCase();
            const type = (row.dataset.type || 'other').toLowerCase();

            const searchMatches = !query || title.includes(query);
            const typeMatches = selectedType === 'all' || type === selectedType;

            if (searchMatches && typeMatches) {
                row.classList.remove('is-hidden');
                visibleCount += 1;
            } else {
                row.classList.add('is-hidden');
            }
        });

        const noResultsRow = byId('noResultsRow');
        if (visibleCount === 0) {
            ensureNoResultsRow();
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    }

    function bindTableActions() {
        if (!tableBody) {
            return;
        }

        tableBody.addEventListener('click', function (event) {
            const editBtn = event.target.closest('.edit-btn');
            if (editBtn) {
                const id = editBtn.getAttribute('data-id');
                if (id) {
                    openEditModal(id);
                }
                return;
            }

            const deleteBtn = event.target.closest('.delete-btn');
            if (deleteBtn) {
                const id = deleteBtn.getAttribute('data-id');
                if (id) {
                    deleteEvent(id);
                }
            }
        });
    }

    function init() {
        if (openModalBtn) {
            openModalBtn.addEventListener('click', function () {
                openModal('add');
            });
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }

        if (cancelModalBtn) {
            cancelModalBtn.addEventListener('click', closeModal);
        }

        if (eventForm) {
            eventForm.addEventListener('submit', handleSubmit);
        }

        if (eventImageInput) {
            eventImageInput.addEventListener('change', handleImageSelect);
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterRows);
        }

        if (typeFilter) {
            typeFilter.addEventListener('change', filterRows);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal && modal.classList.contains('active')) {
                closeModal();
            }
        });

        bindTableActions();
    }

    document.addEventListener('DOMContentLoaded', init);
})();
