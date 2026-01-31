document.addEventListener("DOMContentLoaded", () => {

    /* ================= TABLE & FORMS ================= */
    const table = document.querySelector(".finance-table");

    const incomeForm = document.querySelector(".btn-income")?.closest("form");
    const expenseForm = document.querySelector(".btn-expense")?.closest("form");

    const modal = document.getElementById("financeModal");
    const editForm = document.getElementById("financeEditForm");

    let currentRow = null;
    let deletedRow = null;
    let deleteTimeout = null;
    /* ================= ADD ROW ================= */
    function addRow(type, category, amount, date, desc) {

        const row = table.insertRow(-1);

        const badge =
            type === "Income"
                ? `<span class="badge badge-income">Income</span>`
                : `<span class="badge badge-expense">Expense</span>`;

        const amountClass = type === "Income" ? "amount_income" : "amount_expense";
        const sign = type === "Income" ? "+" : "-";

        row.innerHTML = `
            <td>${badge}</td>
            <td>${category}</td>
            <td class="${amountClass}">${sign}Rs. ${amount}</td>
            <td>${date}</td>
            <td>${desc}</td>
            <td class="actions">
                <button type="button" class="btn-edit">Edit</button>
                <button type="button" class="btn-delete">Delete</button>
            </td>
        `;

        attachRowEvents(row);
    }

    /* ================= BUDGET OVERVIEW TOGGLE ================= */

    const toggleButtons = document.querySelectorAll(".chart-toggle button");
    const monthlyMonths = document.querySelectorAll(".month.monthly");
    const quarterlyMonths = document.querySelectorAll(".month.quarterly");

    /* Default: Monthly */
    monthlyMonths.forEach(month => {
        month.style.display = "block";
        month.querySelectorAll(".bar").forEach(bar => {
            bar.style.height = bar.dataset.month + "%";
        });
    });
    quarterlyMonths.forEach(m => m.style.display = "none");

    toggleButtons.forEach(btn => {
        btn.addEventListener("click", () => {

            toggleButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            const view = btn.dataset.view;

            if (view === "monthly") {
                monthlyMonths.forEach(month => {
                    month.style.display = "block";
                    month.querySelectorAll(".bar").forEach(bar => {
                        bar.style.height = bar.dataset.month + "%";
                    });
                });
                quarterlyMonths.forEach(m => m.style.display = "none");
            } else {
                monthlyMonths.forEach(m => m.style.display = "none");
                quarterlyMonths.forEach(month => {
                    month.style.display = "block";
                    month.querySelectorAll(".bar").forEach(bar => {
                        bar.style.height = bar.dataset.quarter + "%";
                    });
                });
            }
        });
    });

    /* ================= INCOME SUBMIT ================= */
    incomeForm?.addEventListener("submit", e => {
        e.preventDefault();

        const inputs = incomeForm.querySelectorAll("input, textarea");

        addRow(
            "Income",
            inputs[0].value,
            inputs[1].value,
            inputs[2].value,
            inputs[3].value
        );

        incomeForm.reset();
        showCenterToast("Income added successfully");
        updateFinanceStats();
    });

    /* ================= EXPENSE SUBMIT ================= */
    expenseForm?.addEventListener("submit", e => {
        e.preventDefault();

        const inputs = expenseForm.querySelectorAll("select, input, textarea");

        addRow(
            "Expense",
            inputs[0].value,
            inputs[1].value,
            inputs[2].value,
            inputs[3].value
        );

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
            editDate.value = row.cells[3].innerText;
            editDescription.value = row.cells[4].innerText;

            modal.style.display = "flex";
        };

        row.querySelector(".btn-delete").addEventListener("click", () => {
            if (confirm("Are you sure you want to delete this transaction?")) {

                deletedRow = row;
                row.style.display = "none";

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
        const sign = type === "Income" ? "+" : "-";
        const badgeClass = type === "Income" ? "badge-income" : "badge-expense";
        const amountClass = type === "Income" ? "amount_income" : "amount_expense";

        currentRow.cells[0].innerHTML =
            `<span class="badge ${badgeClass}">${type}</span>`;
        currentRow.cells[1].innerText = editCategory.value;
        currentRow.cells[2].className = amountClass;
        currentRow.cells[2].innerText = `${sign}Rs. ${editAmount.value}`;
        currentRow.cells[3].innerText = editDate.value;
        currentRow.cells[4].innerText = editDescription.value;

        modal.style.display = "none";
        showCenterToast("Transaction updated successfully");
        updateFinanceStats();
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
    function showToast(message, color) {
        let toast = document.getElementById("toast");

        if (!toast) {
            toast = document.createElement("div");
            toast.id = "toast";
            toast.className = "toast";
            document.body.appendChild(toast);
        }

        toast.innerHTML = message;
        toast.style.background = color;
        toast.classList.add("show");

        // Attach undo click AFTER rendering
        const undoLink = document.getElementById("undoLink");
        if (undoLink) {
            undoLink.onclick = () => undoDelete();
        }

        setTimeout(() => {
            toast.classList.remove("show");
        }, 4000);
    }


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

    document.querySelector(".stat-income .stat-value").innerText = `Rs. ${income.toLocaleString()}`;
    document.querySelector(".stat-expense .stat-value").innerText = `Rs. ${expense.toLocaleString()}`;
    document.querySelector(".stat-balance .stat-value").innerText =
        `Rs. ${(income - expense).toLocaleString()}`;
}



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

document.getElementById("exportPDF").addEventListener("click", () => {
    window.print();
});
