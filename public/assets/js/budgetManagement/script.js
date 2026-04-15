(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    const config = window.BUDGET_MANAGEMENT_CONFIG || {};
    const root = config.root || '';

    const entryModal = byId('entryModal');
    const entryForm = byId('entryForm');
    const modalTitle = byId('modalTitle');
    const formMessage = byId('formMessage');
    const submitBtn = byId('entrySubmitBtn');

    const openModalBtn = byId('openEntryModalBtn');
    const closeModalBtn = byId('closeEntryModalBtn');
    const cancelModalBtn = byId('cancelEntryModalBtn');

    const searchInput = byId('entrySearch');
    const typeFilter = byId('typeFilter');
    const listBody = byId('budgetListBody');
    const emptyState = byId('emptyState');

    let isEditMode = false;
    let currentId = null;

    function setMessage(message, type) {
        if (!formMessage) {
            return;
        }

        formMessage.textContent = message || '';
        formMessage.className = type ? 'form-message ' + type : 'form-message';
    }

    function clearMessage() {
        setMessage('', '');
    }

    function buildEndpoint(path) {
        return root + path;
    }

    function openModal(mode) {
        if (!entryModal || !entryForm) {
            return;
        }

        clearMessage();

        if (mode === 'add') {
            isEditMode = false;
            currentId = null;
            entryForm.reset();
            byId('entryId').value = '';
            byId('entryDate').value = new Date().toISOString().slice(0, 10);
            const nowYear = new Date().getFullYear();
            byId('entrySeason').value = nowYear + '/' + (nowYear + 1);
            modalTitle.textContent = 'Add Budget Entry';
            submitBtn.textContent = 'Save Entry';
        }

        entryModal.classList.add('active');
    }

    function closeModal() {
        if (!entryModal || !entryForm) {
            return;
        }

        entryModal.classList.remove('active');
        entryForm.reset();
        clearMessage();
        isEditMode = false;
        currentId = null;
        byId('entryId').value = '';
    }

    function collectFormData() {
        return {
            id: byId('entryId').value,
            date: byId('entryDate').value,
            type: byId('entryType').value,
            category: byId('entryCategory').value.trim(),
            description: byId('entryDescription').value.trim(),
            amount: Number(byId('entryAmount').value),
            season: byId('entrySeason').value.trim()
        };
    }

    function validateForm(formData) {
        if (!formData.date) {
            return 'Date is required';
        }
        if (!formData.category) {
            return 'Category is required';
        }
        if (!formData.description) {
            return 'Description is required';
        }
        if (!formData.amount || Number.isNaN(formData.amount) || formData.amount <= 0) {
            return 'Amount must be greater than 0';
        }
        if (!formData.season) {
            return 'Season is required';
        }

        return '';
    }

    async function fetchEntry(id) {
        const response = await fetch(buildEndpoint('/budgetManagement/get?id=' + encodeURIComponent(id)), {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        return response.json();
    }

    function fillForm(item) {
        byId('entryId').value = item.id || '';
        byId('entryDate').value = item.date || '';
        byId('entryType').value = item.type || 'income';
        byId('entryCategory').value = item.category || '';
        byId('entryDescription').value = item.description || '';
        byId('entryAmount').value = item.amount || '';
        byId('entrySeason').value = item.season || '';
    }

    async function openEditModal(id) {
        try {
            const result = await fetchEntry(id);
            if (!result.success || !result.data) {
                throw new Error(result.message || 'Unable to load entry');
            }

            isEditMode = true;
            currentId = id;
            fillForm(result.data);
            modalTitle.textContent = 'Edit Budget Entry';
            submitBtn.textContent = 'Update Entry';
            entryModal.classList.add('active');
        } catch (error) {
            setMessage(error.message, 'error');
        }
    }

    async function saveEntry(formData) {
        const endpoint = isEditMode && currentId ? '/budgetManagement/update' : '/budgetManagement/add';
        const payload = isEditMode && currentId ? { ...formData, id: currentId } : formData;

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

        submitBtn.disabled = true;
        submitBtn.textContent = isEditMode ? 'Updating...' : 'Saving...';

        try {
            const result = await saveEntry(formData);
            if (!result.success) {
                throw new Error(result.message || 'Unable to save entry');
            }

            setMessage(result.message || 'Saved successfully', 'success');
            setTimeout(function () {
                window.location.reload();
            }, 350);
        } catch (error) {
            setMessage(error.message, 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = isEditMode ? 'Update Entry' : 'Save Entry';
        }
    }

    async function deleteEntry(id) {
        const confirmed = window.confirm('Delete this budget entry? This action cannot be undone.');
        if (!confirmed) {
            return;
        }

        try {
            const response = await fetch(buildEndpoint('/budgetManagement/delete'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });

            if (!response.ok) {
                throw new Error('Request failed with status ' + response.status);
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.message || 'Unable to delete entry');
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
        const selectedType = typeFilter ? typeFilter.value : 'all';
        const rows = listBody.querySelectorAll('.budget-item');

        let visibleCount = 0;
        rows.forEach(function (row) {
            const rowType = row.dataset.type || '';
            const rowSearch = row.dataset.search || '';
            const fullText = (row.textContent || '').toLowerCase();

            const searchMatches = !query || rowSearch.includes(query) || fullText.includes(query);
            const typeMatches = selectedType === 'all' || selectedType === rowType;

            if (searchMatches && typeMatches) {
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

        if (entryForm) {
            entryForm.addEventListener('submit', handleSubmit);
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterRows);
        }

        if (typeFilter) {
            typeFilter.addEventListener('change', filterRows);
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
                    deleteEntry(deleteBtn.getAttribute('data-id'));
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && entryModal && entryModal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindEvents();
        filterRows();
    });
})();
