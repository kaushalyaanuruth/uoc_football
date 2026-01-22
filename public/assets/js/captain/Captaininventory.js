document.addEventListener("DOMContentLoaded", () => {

    /* ================= CHARTS ================= */
    new Chart(document.getElementById("equipmentUsageChart"), {
        type: "bar",
        data: {
            labels: ["Equipment", "Kits", "Balls", "Accessories"],
            datasets: [
                { label: "In Use", data: [45, 32, 28, 40], backgroundColor: "#f97316" },
                { label: "Available", data: [25, 18, 22, 20], backgroundColor: "#22c55e" }
            ]
        }
    });

    new Chart(document.getElementById("statusDistributionChart"), {
        type: "pie",
        data: {
            labels: ["In Use", "Available", "Damaged"],
            datasets: [{
                data: [142, 89, 17],
                backgroundColor: ["#f97316", "#22c55e", "#ef4444"]
            }]
        }
    });

    /* ================= INVENTORY LOGIC ================= */
    const modal = document.getElementById("inventoryModal");
    const successModal = document.getElementById("successModal");
    const toast = document.getElementById("toast");
    const inventoryForm = document.getElementById("inventoryForm");
    const item_name = document.getElementById("item_name");
    const category = document.getElementById("category");
    const quantity = document.getElementById("quantity");
    const status = document.getElementById("status");
    const categoryFilter = document.getElementById("categoryFilter");
    let currentRow = null;
    let deletedRow = null;

    function getStatusClass(value) {
        return value.toLowerCase().replace(/\s/g, "");
    }

    function showToast(msg, color = "#22c55e") {
        toast.style.background = color;
        toast.innerHTML = msg;
        toast.style.display = "block";
        setTimeout(() => toast.style.display = "none", 2500);
    }
    categoryFilter.addEventListener("change", () => {
        const selectedCategory = categoryFilter.value; // <-- no toLowerCase here

        document.querySelectorAll(".inventory-table tbody tr").forEach(row => {
            const rowCategory = row.children[1].innerText.toLowerCase();

            if (selectedCategory === "all" || rowCategory === selectedCategory) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
    document.getElementById("addItem").addEventListener("click", () => {
        currentRow = null;
        inventoryForm.reset();
        modal.querySelector(".modal-header span").innerText = "Add Inventory Item";
        modal.style.display = "flex";
    });

    function attachRowEvents(row) {
        row.querySelector(".btn-edit").addEventListener("click", () => {
            currentRow = row;
            item_name.value = row.children[0].innerText;
            category.value = row.children[1].innerText;
            quantity.value = row.children[2].innerText;
            status.value = row.children[3].innerText;
            modal.style.display = "flex";
        });

        row.querySelector(".btn-delete").addEventListener("click", () => {
            if (confirm("Are you sure you want to delete this item?")) {
                deletedRow = row;
                row.style.display = "none";
                showToast("Item deleted <u style='cursor:pointer' onclick='undoDelete()'>Undo</u>", "#dc2626");
            }
        });
    }

    document.querySelectorAll(".inventory-table tbody tr")
        .forEach(row => attachRowEvents(row));

    window.undoDelete = function () {
        if (deletedRow) {
            deletedRow.style.display = "";
            deletedRow = null;
            showToast("Undo successful", "#16a34a");
        }
    }

    inventoryForm.addEventListener("submit", e => {
        e.preventDefault();

        if (!inventoryForm.checkValidity()) {
            inventoryForm.reportValidity();
            return;
        }

        if (quantity.value < 0) {
            quantity.setCustomValidity("Quantity cannot be negative");
            quantity.reportValidity();
            return;
        }
        quantity.setCustomValidity("");

        if (currentRow) {
            currentRow.children[0].innerText = item_name.value;
            currentRow.children[1].innerText = category.value;
            currentRow.children[2].innerText = quantity.value;

            const statusSpan = currentRow.children[3].querySelector("span");
            statusSpan.innerText = status.value;
            statusSpan.className = "status " + getStatusClass(status.value);
        } else {
            const tbody = document.querySelector(".inventory-table tbody");
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${item_name.value}</td>
                <td>${category.value}</td>
                <td>${quantity.value}</td>
                <td><span class="status ${getStatusClass(status.value)}">${status.value}</span></td>
                <td>Just now</td>
                <td class="actions">
                    <button class="btn-edit">Edit</button>
                    <button class="btn-delete">Delete</button>
                </td>
            `;
            attachRowEvents(row);
        }

        modal.style.display = "none";
        successModal.style.display = "flex";
    });

    document.getElementById("close").onclick =
        document.getElementById("closeModal").onclick = () => modal.style.display = "none";

    document.getElementById("closeSuccess").onclick = () => {
        successModal.style.display = "none";
    };

    document.addEventListener("keydown", e => {
        if (e.key === "Escape") {
            modal.style.display = "none";
            successModal.style.display = "none";
        }
    });

});
