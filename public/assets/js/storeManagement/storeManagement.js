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

    fetch(`${baseUrl}/storemanagement/addItem`, {
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
    fetch(`${baseUrl}/storemanagement/getItem?item_id=${itemId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {
                document.getElementById("editItemId").value = data.item.item_id;
                document.getElementById("editItemName").value = data.item.item_name;
                document.getElementById("editItemCategory").value = data.item.category;
                document.getElementById("editItemPrice").value = data.item.price;
                document.getElementById("editItemQuantity").value = data.item.quantity;
                document.getElementById("editItemStatus").value = data.item.status;
                document.getElementById("editItemDescription").value = data.item.description || '';
                
                // Show current image preview
                const previewDiv = document.getElementById("editItemImagePreview");
                if (data.item.item_image) {
                    previewDiv.innerHTML = `<img src="${baseUrl}/${data.item.item_image}" alt="Current Image" style="width: 100%; height: 100%; object-fit: contain; padding: 10px;">`;
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

    fetch(`${baseUrl}/storemanagement/editItem`, {
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

    fetch(`${baseUrl}/storemanagement/deleteItem`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `item_id=${itemId}`
    })
    .then(response => response.json())
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
