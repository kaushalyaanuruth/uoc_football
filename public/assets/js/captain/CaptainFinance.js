document.addEventListener("DOMContentLoaded", () => {

    /* ================= TABLE & FORMS ================= */
    const BASE_URL = window.APP_ROOT || "http://localhost/UOC_Football/public";
    const table = document.querySelector(".finance-table");

    const incomeForm = document.querySelector(".btn-income")?.closest("form");
    const expenseForm = document.querySelector(".btn-expense")?.closest("form");

    const modal = document.getElementById("financeModal");
    const editForm = document.getElementById("financeEditForm");


    let currentRow = null;
    let deletedRow = null;
    let deleteTimeout = null;
    /* ================= ADD ROW ================= */

    /* ================= BUDGET OVERVIEW TOGGLE ================= */

    const toggleButtons = document.querySelectorAll(".chart-toggle button");
    const monthlyMonths = document.querySelectorAll(".month.monthly");
    const quarterlyMonths = document.querySelectorAll(".month.quarterly");


    let financeChart;

function loadFinanceChart() {
    fetch(`${BASE_URL}/CaptainFinance/getChartData`)
        .then(res => res.json())
        .then(data => {

            const allMonths = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

            let monthlyData = allMonths.map(m => ({
                month: m,
                income: 0,
                expense: 0
            }));

            data.forEach(item => {
                const index = allMonths.indexOf(item.month);
                if (index !== -1) {
                    monthlyData[index].income = parseInt(item.income) || 0;
                    monthlyData[index].expense = parseInt(item.expense) || 0;
                }
            });

            const quarterlyData = [];
            for (let i = 0; i < 12; i += 3) {
                quarterlyData.push({
                    label: `Q${(i / 3) + 1}`,
                    income: monthlyData[i].income + monthlyData[i+1].income + monthlyData[i+2].income,
                    expense: monthlyData[i].expense + monthlyData[i+1].expense + monthlyData[i+2].expense
                });
            }

            renderFinanceChart(monthlyData, "monthly");

            document.querySelectorAll(".chart-toggle button").forEach(btn => {
                btn.onclick = () => {
                    document.querySelectorAll(".chart-toggle button")
                        .forEach(b => b.classList.remove("active"));

                    btn.classList.add("active");

                    if (btn.dataset.view === "monthly") {
                        renderFinanceChart(monthlyData, "monthly");
                    } else {
                        renderFinanceChart(quarterlyData, "quarterly");
                    }
                };
            });
        });
}
function renderFinanceChart(data, type) {

    const ctx = document.getElementById("financeChart").getContext("2d");

    if (financeChart) financeChart.destroy();

    financeChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: data.map(d => type === "monthly" ? d.month : d.label),

            datasets: [
                {
                    label: "Income",
                    data: data.map(d => d.income),
                    backgroundColor: "#22c55e"
                },
                {
                    label: "Expense",
                    data: data.map(d => d.expense),
                    backgroundColor: "#ef4444"
                }
            ]
        },
        options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: "top" }
    },
    scales: {
        x: { stacked: false },
        y: { beginAtZero: true }
    }
}
    });
}
    
    /* ================= INCOME SUBMIT ================= */
    incomeForm?.addEventListener("submit", e => {
        e.preventDefault();

        const inputs = incomeForm.querySelectorAll("input, textarea");

        const formData = new FormData();
        formData.append("category", inputs[0].value);
        formData.append("amount", inputs[1].value);
        formData.append("date", inputs[2].value);
        formData.append("description", inputs[3].value);

        fetch(`${BASE_URL}/CaptainFinance/addIncome`, {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    showCenterToast("Income added successfully");
                    location.reload(); // reload like inventory
                }
            })
            .catch(() => {
                showToast("Failed to save income", "#dc2626");
            });

        incomeForm.reset();
        showCenterToast("Income added successfully");
        updateFinanceStats();
    });

    /* ================= EXPENSE SUBMIT ================= */
    expenseForm?.addEventListener("submit", e => {
        e.preventDefault();

        const inputs = expenseForm.querySelectorAll("select, input, textarea");

        const formData = new FormData();
        formData.append("category", inputs[0].value);
        formData.append("amount", inputs[1].value);
        formData.append("date", inputs[2].value);
        formData.append("description", inputs[3].value);

        fetch(`${BASE_URL}/CaptainFinance/addExpense`, {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    showCenterToast("Expense added successfully");
                    location.reload();
                }
            })
            .catch(() => {
                showToast("Failed to save expense", "#dc2626");
            });

        expenseForm.reset();
        showCenterToast("Expense added successfully");
        updateFinanceStats();
    });

    /* ================= EDIT & DELETE ================= */
    function attachRowEvents(row) {

        row.querySelector(".btn-edit").onclick = () => {
            currentRow = row;

            editType.value = row.cells[0].innerText.trim();
            editCategory.value = row.cells[1].innerText;
            editAmount.value = row.cells[2].innerText.replace(/[^\d]/g, "");
            const rawDate = new Date(row.cells[3].innerText);
            editDate.value = rawDate.toISOString().split("T")[0];
            editDescription.value = row.cells[4].innerText;

            modal.style.display = "flex";
        };

        row.querySelector(".btn-delete").addEventListener("click", () => {
            if (confirm("Are you sure you want to delete this transaction?")) {

                const type = row.cells[0].innerText.trim();
                const id = row.dataset.id; // we will set this in view

                const formData = new FormData();
                formData.append("type", type);
                formData.append("id", id);

                fetch(`${BASE_URL}/CaptainFinance/delete`, {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "success") {
                            row.remove();
                            showCenterToast("Transaction deleted");
                            updateFinanceStats();
                        }
                    })
                    .catch(() => {
                        showToast("Delete failed", "#dc2626");
                    });
                showToast(
                    "Transaction deleted <u id='undoLink' style='cursor:pointer'>Undo</u>",
                    "#dc2626"
                );
                updateFinanceStats();
                // Auto remove undo after 5 seconds
                deleteTimeout = setTimeout(() => {
                    deletedRow = null;
                }, 5000);
            }
        });


    }

    document.querySelectorAll(".finance-table tr").forEach((row, index) => {
        if (index !== 0) attachRowEvents(row);
    });
    window.undoDelete = function () {
        if (deletedRow) {
            deletedRow.style.display = "";
            deletedRow = null;

            if (deleteTimeout) {
                clearTimeout(deleteTimeout);
                deleteTimeout = null;
            }

            showToast("Undo successful", "#16a34a");
            updateFinanceStats();
        }
    };

    /* ================= SAVE EDIT ================= */
    editForm?.addEventListener("submit", e => {
        e.preventDefault();

        const type = editType.value;
        const id = currentRow.dataset.id;

        const formData = new FormData();
        formData.append("type", type);
        formData.append("id", id);
        formData.append("category", editCategory.value);
        formData.append("amount", editAmount.value);
        formData.append("date", editDate.value);
        formData.append("description", editDescription.value);

        fetch(`${BASE_URL}/CaptainFinance/update`, {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {

                    // ✅ update UI AFTER DB success
                    const sign = type === "Income" ? "+" : "-";
                    const badgeClass = type === "Income" ? "badge-income" : "badge-expense";
                    const amountClass = type === "Income" ? "amount_income" : "amount_expense";

                    currentRow.cells[0].innerHTML =
                        `<span class="badge ${badgeClass}">${type}</span>`;
                    currentRow.cells[1].innerText = editCategory.value;
                    currentRow.cells[2].className = amountClass;
                    currentRow.cells[2].innerText = `${sign}LKR ${Number(editAmount.value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    const formattedDate = new Date(editDate.value).toLocaleDateString("en-US", {
                        month: "short",
                        day: "2-digit",
                        year: "numeric"
                    });

                    currentRow.cells[3].innerText = formattedDate;
                    currentRow.cells[4].innerText = editDescription.value;

                    modal.style.display = "none";
                    showCenterToast("Transaction updated successfully");
                    updateFinanceStats();
                }
            })
            .catch(() => {
                showToast("Update failed", "#dc2626");
            });
    });

    closeFinanceModal.onclick =
        cancelFinanceEdit.onclick = () => modal.style.display = "none";

    /* ================= TRANSACTION FILTER ================= */
    const filterButtons = document.querySelectorAll(".finance-charttoggle button");

    filterButtons.forEach(btn => {
        btn.addEventListener("click", () => {

            filterButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            const filter = btn.innerText.toLowerCase();

            document.querySelectorAll(".finance-table tr").forEach((row, index) => {
                if (index === 0) return;

                const typeText = row.cells[0].innerText.toLowerCase();

                if (filter === "all" || typeText.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });

    /* ================= TOAST ================= */
    // function showToast(message, color) {
    //     let toast = document.getElementById("toast");

    //     if (!toast) {
    //         toast = document.createElement("div");
    //         toast.id = "toast";
    //         toast.className = "toast";
    //         document.body.appendChild(toast);
    //     }

    //     toast.innerHTML = message;
    //     toast.style.background = color;
    //     toast.classList.add("show");

    //     // Attach undo click AFTER rendering
    //     const undoLink = document.getElementById("undoLink");
    //     if (undoLink) {
    //         undoLink.onclick = () => undoDelete();
    //     }

    //     setTimeout(() => {
    //         toast.classList.remove("show");
    //     }, 4000);
    // }


    /* ================= CENTER TOAST ================= */
    const centerToast = document.getElementById("centerToast");
    const centerToastMessage = document.getElementById("centerToastMessage");
    const centerToastOk = document.getElementById("centerToastOk");

    function showCenterToast(message) {
        centerToastMessage.innerText = message;
        centerToast.style.display = "flex";
    }

    centerToastOk.onclick = () => centerToast.style.display = "none";
    document.addEventListener("keydown", e => {
        if (centerToast.style.display === "flex" && e.key === "Enter") {
            centerToastOk.click();
        }
    });
    function updateFinanceStats() {
        let income = 0;
        let expense = 0;

        document.querySelectorAll(".finance-table tr").forEach((row, index) => {
            if (index === 0 || row.style.display === "none") return;

            const amountText = row.cells[2].innerText.replace(/[^\d]/g, "");
            const amount = parseInt(amountText) || 0;

            if (row.cells[0].innerText.includes("Income")) {
                income += amount;
            } else {
                expense += amount;
            }
        });

        document.querySelector(".stat-income .stat-value").innerText = `LKR ${income.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        document.querySelector(".stat-expense .stat-value").innerText = `LKR ${expense.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        document.querySelector(".stat-balance .stat-value").innerText =
            `LKR ${(income - expense).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    loadFinanceChart();

});
document.getElementById("exportReport").addEventListener("click", () => {

    const table = document.querySelector(".finance-table");
    let csv = [];

    // Table headers (skip Actions)
    const headers = [];
    table.querySelectorAll("th").forEach((th, index) => {
        if (index < 5) {
            headers.push(`"${th.innerText.trim()}"`);
        }
    });
    csv.push(headers.join(","));

    // Table rows
    table.querySelectorAll("tr").forEach((row, index) => {
        if (index === 0 || row.style.display === "none") return;

        let rowData = [];
        for (let i = 0; i < 5; i++) {
            rowData.push(`"${row.cells[i].innerText.trim()}"`);
        }
        csv.push(rowData.join(","));
    });

    const csvBlob = new Blob([csv.join("\n")], { type: "text/csv" });
    const link = document.createElement("a");

    const today = new Date().toISOString().split("T")[0];
    link.download = `finance-report-${today}.csv`;
    link.href = URL.createObjectURL(csvBlob);

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

});

const exportPdfBtn = document.getElementById("exportPDF");
if (exportPdfBtn) {
    exportPdfBtn.addEventListener("click", () => {
        window.print();
    });
}
