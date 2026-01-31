document.addEventListener("DOMContentLoaded", () => {

    /* ================= SELECTORS ================= */
    const table = document.querySelector(".attendance-table tbody");
    const markAllBtn = document.querySelector(".section-header .btn");
    const resetBtn = document.querySelector(".reset-changes");
    const exportBtn = document.querySelector(".exportreport");

    const totalPlayersEl = document.querySelector(".stat-card span");
    const presentEl = document.querySelector(".stat-card.success span");
    const absentEl = document.querySelector(".stat-card.danger span");

    /* ================= STORE ORIGINAL STATE ================= */
    const originalAttendance = [];

    document.querySelectorAll(".attendance-table tbody tr").forEach(row => {
        originalAttendance.push({
            row: row,
            status: row.querySelector(".status").innerText.trim()
        });
    });

    /* ================= UPDATE STATS ================= */
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

    /* ================= CHANGE STATUS ================= */
    function setStatus(row, status) {
        const statusSpan = row.querySelector(".status");
        statusSpan.innerText = status;
        statusSpan.className = "status " + status.toLowerCase();
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

    /* ================= RESET CHANGES ================= */
    resetBtn.addEventListener("click", () => {
        if (!confirm("Reset all unsaved attendance changes?")) return;

        originalAttendance.forEach(item => {
            const statusSpan = item.row.querySelector(".status");
            statusSpan.innerText = item.status;
            statusSpan.className = "status " + item.status.toLowerCase();
        });

        updateStats();
        alert("Attendance changes reset successfully");
    });

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

    /* ================= EXPORT PDF ================= */
    function exportPDF() {
        window.print();
    }

    /* ================= EXPORT BUTTON ================= */
    exportBtn.addEventListener("click", () => {
        const choice = confirm(
            "Click OK to download PDF\nClick Cancel to download Excel (CSV)"
        );

        if (choice) {
            exportPDF();
        } else {
            exportCSV();
        }
    });

});
