const baseUrl = window.ROOT || '';

function openAddItemModal() {
    document.getElementById("addItemModal").classList.add("active");
}

function closeAddItemModal() {
    document.getElementById("addItemModal").classList.remove("active");
    document.getElementById("addItemForm").reset();
}

function submitAddItemForm(event) {
    event.preventDefault();
    const form = document.getElementById("addItemForm");
    const formData = new FormData(form);

    fetch(`${baseUrl}/StoreManagement/addItem`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeAddItemModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function editItem(itemId) {
    fetch(`${baseUrl}/StoreManagement/getItem?item_id=${itemId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {
                document.getElementById("editItemId").value = data.item.product_id;
                document.getElementById("editItemName").value = data.item.product_name;
                document.getElementById("editItemCategory").value = data.item.category;
                document.getElementById("editItemStatus").value = Number(data.item.is_active || 0) === 1 ? 'Available' : 'Sold Out';
                document.getElementById("editItemDescription").value = data.item.description || '';

                const sizeMap = {};
                (data.item.variants || []).forEach((variant) => {
                    sizeMap[String(variant.size || '').toUpperCase()] = variant;
                });

                document.querySelectorAll('.edit-variant-price').forEach((input) => {
                    const size = String(input.dataset.size || '').toUpperCase();
                    const variant = sizeMap[size];
                    input.value = variant ? variant.price : '';
                });

                document.querySelectorAll('.edit-variant-stock').forEach((input) => {
                    const size = String(input.dataset.size || '').toUpperCase();
                    const variant = sizeMap[size];
                    input.value = variant ? variant.stock_qty : '';
                });
                
                // Show current image preview
                const previewDiv = document.getElementById("editItemImagePreview");
                if (data.item.product_image) {
                    previewDiv.innerHTML = `<img src="${baseUrl}/${data.item.product_image}" alt="Current Image" style="width: 100%; height: 100%; object-fit: contain; padding: 10px;">`;
                } else {
                    previewDiv.innerHTML = '<span class="material-symbols-outlined" style="font-size: 64px; color: #d1d5db;">shopping_bag</span>';
                }
                
                document.getElementById("editItemModal").classList.add("active");
            } else {
                alert('Error loading item');
            }
        })
        .catch(error => { 
            console.error('Error:', error);
            alert('An error occurred');
        });
}

function closeEditItemModal() {
    document.getElementById("editItemModal").classList.remove("active");
    document.getElementById("editItemForm").reset();
}

function previewEditImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("editItemImagePreview").innerHTML = `<img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: contain; padding: 10px;">`;
        };
        reader.readAsDataURL(file);
    }
}

function submitEditItemForm(event) {
    event.preventDefault();
    const form = document.getElementById("editItemForm");
    const formData = new FormData(form);

    fetch(`${baseUrl}/StoreManagement/editItem`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeEditItemModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function deleteItem(itemId) {
    if (!confirm('Are you sure you want to delete this item?')) return;

    fetch(`${baseUrl}/StoreManagement/deleteItem`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `item_id=${encodeURIComponent(itemId)}`
    })
    .then(async (response) => {
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (error) {
            throw new Error('Unexpected server response while deleting item.');
        }
        if (!response.ok) {
            throw new Error(data.message || 'Failed to delete item.');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
