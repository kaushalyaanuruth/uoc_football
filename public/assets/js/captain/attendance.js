document.addEventListener("DOMContentLoaded", () => {
    let changedData = [];

    /* ================= SELECTORS ================= */
    const table = document.querySelector(".attendance-table tbody");
    const markAllBtn = document.querySelector(".section-header .btn");
    const resetBtn = document.querySelector(".reset-changes");
    const exportBtn = document.querySelector(".exportreport");

    const presentEl = document.querySelector(".stat-card.success").querySelector(".count");
    const absentEl = document.querySelector(".stat-card.danger").querySelector(".count");

    /* ================= STORE ORIGINAL STATE ================= */
    const originalAttendance = [];

    document.querySelectorAll(".attendance-table tbody tr").forEach(row => {
        originalAttendance.push({
            // row: row,
            playerId: row.dataset.playerId,
            // status: row.querySelector(".status").innerText.trim()
            status: row.dataset.originalStatus

        });
    });

    /* ================= UPDATE STATS ================= */
    //     function setStatus(row, status) {
    //     const statusSpan = row.querySelector(".status");
    //     statusSpan.innerText = status;
    //     statusSpan.className = "status " + status.toLowerCase();

    //     const playerId = row.dataset.playerId;
    //     const eventId = 1; // TEMP (same as controller)

    //     // 🔥 SEND TO BACKEND
    //     fetch("/CaptainAttendance/update", {
    //         method: "POST",
    //         headers: {
    //             "Content-Type": "application/json"
    //         },
    //         body: JSON.stringify({
    //             player_id: playerId,
    //             event_id: eventId,
    //             status: status
    //         })
    //     });

    //     updateStats();
    // }

    /* ================= CHANGE STATUS ================= */
    // function setStatus(row, status) {
    //     const statusSpan = row.querySelector(".status");
    //     statusSpan.innerText = status;
    //     statusSpan.className = "status " + status.toLowerCase();
    //     updateStats();
    // }
    function setStatus(row, status) {
        const statusSpan = row.querySelector(".status");
        statusSpan.innerText = status;
        statusSpan.className = "status " + status.toLowerCase();

        const playerId = row.dataset.playerId;
        const eventId = document.getElementById("eventId").value;

        // fetch(window.location.origin + "/uoc_football/public/CaptainAttendance/update", {
        //     method: "POST",
        //     // method: "POST",
        //     headers: {
        //         "Content-Type": "application/json"
        //     },
        //     body: JSON.stringify({
        //         player_id: playerId,
        //         event_id: eventId,
        //         status: status
        //     })
        // })
        //     .then(res => res.text())
        //     .then(data => console.log("Saved:", data))
        //     .catch(err => console.error(err));

        // updateStats();
        // location.reload();
        // }
        // store changes (overwrite if already exists)
        const index = changedData.findIndex(p => p.player_id == playerId);

        if (index !== -1) {
            changedData[index].status = status;
        } else {
            changedData.push({
                player_id: playerId,
                event_id: eventId,
                status: status
            });
        }

        updateStats();
    }

    /* ================= ROW ACTIONS ================= */
    table.querySelectorAll("tr").forEach(row => {
        const [presentBtn, absentBtn, lateBtn] = row.querySelectorAll(".icon");

        presentBtn.addEventListener("click", () => setStatus(row, "Present"));
        absentBtn.addEventListener("click", () => setStatus(row, "Absent"));
        lateBtn.addEventListener("click", () => setStatus(row, "Late"));
    });

    /* ================= MARK ALL PRESENT ================= */
    markAllBtn.addEventListener("click", () => {
        if (!confirm("Mark all players as Present?")) return;

        table.querySelectorAll("tr").forEach(row => {
            setStatus(row, "Present");
        });
    });
    const saveBtn = document.querySelector(".save-attendance");

    saveBtn.addEventListener("click", () => {

        if (changedData.length === 0) {
            alert("No changes to save!");
            return;
        }

        fetch(window.location.origin + "/uoc_football/public/CaptainAttendance/update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(changedData)
        })
            .then(res => res.json())
            .then(data => {
                alert("Attendance saved successfully!");
                location.reload();
            })
            .catch(err => console.error(err));
    });
    /* ================= RESET CHANGES ================= */
    // resetBtn.addEventListener("click", () => {
    //     if (!confirm("Reset all unsaved attendance changes?")) return;

    //     originalAttendance.forEach(item => {
    //         const statusSpan = item.row.querySelector(".status");
    //         statusSpan.innerText = item.status;
    //         statusSpan.className = "status " + item.status.toLowerCase();
    //     });

    //     updateStats();
    //     alert("Attendance changes reset successfully");
    // });
    resetBtn.addEventListener("click", () => {

        if (!confirm("Reset all unsaved attendance changes?")) return;

        document.querySelectorAll(".attendance-table tbody tr").forEach(row => {
            const playerId = row.dataset.playerId;

            const original = originalAttendance.find(p => p.playerId == playerId);

            if (original) {
                const statusSpan = row.querySelector(".status");
                statusSpan.innerText = original.status;
                statusSpan.className = "status " + original.status.toLowerCase();
            }
        });
        changedData = []; // 🔥 IMPORTANT FIX

        updateStats();
    });

    function updateStats() {
        let present = 0;
        let absent = 0;

        document.querySelectorAll(".attendance-table tbody tr").forEach(row => {
            const status = row.querySelector(".status").innerText.toLowerCase();
            if (status === "present") present++;
            if (status === "absent") absent++;
        });

        presentEl.innerText = present;
        absentEl.innerText = absent;
    }
    /* ================= EXPORT CSV (EXCEL) ================= */
    function exportCSV() {
        let csv = [];
        csv.push("Player,Position,Status");

        document.querySelectorAll(".attendance-table tbody tr").forEach(row => {
            const player = row.cells[0].innerText.replace(/\n/g, " ");
            const position = row.cells[1].innerText;
            const status = row.cells[2].innerText;

            csv.push(`"${player}","${position}","${status}"`);
        });

        const blob = new Blob([csv.join("\n")], { type: "text/csv" });
        const link = document.createElement("a");

        link.href = URL.createObjectURL(blob);
        link.download = `attendance-${new Date().toISOString().split("T")[0]}.csv`;

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    /* ================= EXPENSE SUBMIT ================= */
    // expenseForm?.addEventListener("submit", e => {
    //     e.preventDefault();

    //     const inputs = expenseForm.querySelectorAll("select, input, textarea");

    //     addRow(
    //         "Expense",
    //         inputs[0].value,
    //         inputs[1].value,
    //         inputs[2].value,
    //         inputs[3].value
    //     );

    //     expenseForm.reset();
    //     showCenterToast("Expense added successfully");
    //     updateFinanceStats();
    // });

    /* ================= EXPORT PDF ================= */
    function exportPDF() {
        window.print();
    }

    /* ================= EXPORT BUTTON ================= */
    exportBtn.addEventListener("click", () => {
        if (changedData.length > 0) {
            alert("You have unsaved changes. Please save before downloading.");
            return; // 🔥 STOP export
        }

        const choice = confirm(
            "Click OK to download PDF\n"
            // "Click OK to download PDF\n
            // Click Cancel to download Excel (CSV)"
        );

        if (choice) {
            // exportPDF();
            // exportCSV();
            window.location.href = window.location.origin + "/uoc_football/public/CaptainAttendance/export";

        }
        // else {

        // }
    });

});
