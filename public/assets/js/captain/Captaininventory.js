document.addEventListener("DOMContentLoaded", () => {
    const BASE_URL = "http://localhost/uoc_football/public";
    /* =========================================================
       ELEMENTS
    ========================================================= */
    const totalEl = document.querySelector(".stat-card:nth-child(1) span");
    const inUseEl = document.querySelector(".stat-card.warning span");
    const availableEl = document.querySelector(".stat-card.success span");
    const damagedEl = document.querySelector(".stat-card.danger span");

    const modal = document.getElementById("inventoryModal");
    const toast = document.getElementById("toast");
    const inventoryForm = document.getElementById("inventoryForm");

    const item_id = document.getElementById("item_id");
    const item_name = document.getElementById("item_name");
    const category = document.getElementById("category");
    const quantity = document.getElementById("quantity");
    const status = document.getElementById("status");
    const categoryFilter = document.getElementById("categoryFilter");
    const successModal = document.getElementById("successModal");
    const closeSuccess = document.getElementById("closeSuccess");
    let usageChart, statusChart;


    /* =========================================================
       TOAST (PROFESSIONAL)
    ========================================================= */
    function showSuccessModal(message) {
        const msg = document.getElementById("centerToastMessage");
        msg.innerText = message;
        successModal.style.display = "flex";


    }

    closeSuccess.addEventListener("click", () => {
        successModal.style.display = "none";
    });

    successModal.addEventListener("click", (e) => {
        if (e.target === successModal) {
            successModal.style.display = "none";
        }
    });



    /* =========================================================
       CHARTS
    ========================================================= */
    function initCharts() {

        // BAR CHART (Category Usage)
        usageChart = new Chart(document.getElementById("equipmentUsageChart"), {
            type: "bar",
            data: {
                labels: ["Kits", "Balls", "Equipment", "Accessories"],
                datasets: [
                    {
                        label: "In Use",
                        backgroundColor: "#f97316",
                        data: [0, 0, 0, 0]
                    },
                    {
                        label: "Available",
                        backgroundColor: "#22c55e",
                        data: [0, 0, 0, 0]
                    }
                ]
            }
        });

        // PIE CHART (Status Distribution)
        statusChart = new Chart(document.getElementById("statusDistributionChart"), {
            type: "pie",
            data: {
                labels: ["In Use", "Available", "Damaged"],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: ["#f97316", "#22c55e", "#ef4444"]
                }]
            }
        });
    }

    function updateCharts(inUse, available, damaged) {
        usageChart.data.datasets[0].data = [inUse, available, damaged];
        statusChart.data.datasets[0].data = [inUse, available, damaged];
        usageChart.update();
        statusChart.update();
    }


    /* =========================================================
       STATS CALCULATION
    ========================================================= */
 function updateInventoryStats() {

    let categories = {
        kits: { inUse: 0, available: 0 },
        balls: { inUse: 0, available: 0 },
        equipment: { inUse: 0, available: 0 },
        accessories: { inUse: 0, available: 0 }
    };

    let total = 0;
    let inUseTotal = 0;
    let availableTotal = 0;
    let damagedTotal = 0;

    document.querySelectorAll(".inventory-table tbody tr").forEach(row => {

        const qty = parseInt(row.children[2].innerText) || 0;
        const category = row.children[1].innerText.trim().toLowerCase();
        const status = row.children[3].innerText.trim().toLowerCase();

        total += qty;

        if (status === "in use") {
            inUseTotal += qty;
        } 
        else if (status === "available") {
            availableTotal += qty;
        } 
        else if (status === "damaged") {
            damagedTotal += qty;
        }

        // category chart mapping
        if (categories[category]) {
            if (status === "in use") {
                categories[category].inUse += qty;
            } else if (status === "available") {
                categories[category].available += qty;
            }
        }
    });

    // -------------------------
    // UPDATE PIE CHART
    // -------------------------
    statusChart.data.datasets[0].data = [
        inUseTotal,
        availableTotal,
        damagedTotal
    ];
    statusChart.update();

    // -------------------------
    // UPDATE BAR CHART
    // -------------------------
    usageChart.data.datasets[0].data = [
        categories.kits.inUse,
        categories.balls.inUse,
        categories.equipment.inUse,
        categories.accessories.inUse
    ];

    usageChart.data.datasets[1].data = [
        categories.kits.available,
        categories.balls.available,
        categories.equipment.available,
        categories.accessories.available
    ];

    usageChart.update();
}
    /* =========================================================
           TOAST (SMALL NOTIFICATION)
        ========================================================= */
    function showToast(message, type = "success") {

        const colors = {
            success: "#22c55e",
            error: "#dc2626",
            warning: "#f59e0b"
        };

        toast.style.background = colors[type];
        toast.innerHTML = message;
        toast.style.display = "block";

        setTimeout(() => {
            toast.style.display = "none";
        }, 2500);
    }


    /* =========================================================
       FILTER
    ========================================================= */
    categoryFilter.addEventListener("change", () => {
        const selected = categoryFilter.value.toLowerCase();

        document.querySelectorAll(".inventory-table tbody tr").forEach(row => {
            const rowCategory = row.children[1].innerText.toLowerCase();

            row.style.display =
                selected === "all" || rowCategory === selected ? "" : "none";
        });

        updateInventoryStats();
    });


    /* =========================================================
       ADD ITEM BUTTON
    ========================================================= */
    document.getElementById("addItem").addEventListener("click", () => {
        inventoryForm.reset();
        item_id.value = "";
        modal.querySelector(".modal-header span").innerText = "Add Inventory Item";
        modal.style.display = "flex";
    });


    /* =========================================================
       ROW ACTIONS (EDIT + DELETE)
    ========================================================= */
    function attachRowEvents(row) {

        // EDIT
        row.querySelector(".btn-edit").addEventListener("click", () => {

            item_id.value = row.dataset.id;
            item_name.value = row.children[0].innerText;
            category.value = row.children[1].innerText;
            quantity.value = row.children[2].innerText;
            status.value = row.children[3].innerText.trim();

            modal.style.display = "flex";
        });

        // DELETE
        row.querySelector(".btn-delete").addEventListener("click", () => {

            if (!confirm("Are you sure you want to delete this item?")) return;

            const formData = new FormData();
            formData.append("item_id", row.dataset.id);

            fetch(`${BASE_URL}/captainInventory/delete`, {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        row.remove();
                        updateInventoryStats();
                        showSuccessModal("Item deleted successfully!");
                    }
                })
                .catch(() => {
                    showSuccessModal("Operation failed!");
                });
        });
    }

    document.querySelectorAll(".inventory-table tbody tr")
        .forEach(row => attachRowEvents(row));


    /* =========================================================
       FORM SUBMIT (ADD + UPDATE)
    ========================================================= */
    inventoryForm.addEventListener("submit", e => {
        e.preventDefault();

        const formData = new FormData();
        formData.append("item_id", item_id.value);
        formData.append("item_name", item_name.value);
        formData.append("quantity", quantity.value);
        formData.append("category", category.value);
        formData.append("status", status.value);

        let url = `${BASE_URL}/captainInventory/store`;

        if (item_id.value) {
            url = `${BASE_URL}/captainInventory/update`;
        }

        fetch(url, {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    showSuccessModal("Item saved successfully!");
                    modal.style.display = "none";
                    location.reload();
                }
            })
            .catch(() => {
                showToast("Operation failed!", "error");
            });
    });


    /* =========================================================
       MODAL CONTROLS
    ========================================================= */
    document.getElementById("close").onclick =
        document.getElementById("closeModal").onclick =
        () => modal.style.display = "none";

    document.addEventListener("keydown", e => {
        if (e.key === "Escape") modal.style.display = "none";
    });


    /* =========================================================
       INIT
    ========================================================= */
    initCharts();
    updateInventoryStats();

});