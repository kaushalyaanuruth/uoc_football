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
            
            playerId: row.dataset.playerId,
            status: row.dataset.originalStatus

        });
    });

    /* ================= UPDATE STATS ================= */

    function setStatus(row, status) {
        const statusSpan = row.querySelector(".status");
        statusSpan.innerText = status;
        statusSpan.className = "status " + status.toLowerCase();

        const playerId = row.dataset.playerId;
        const index = changedData.findIndex(p => p.player_id == playerId);

        if (index !== -1) {
            changedData[index].status = status;
        } else {
            changedData.push({
                player_id: playerId,
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

        const today = new Date().toISOString().split("T")[0];
        if (dateInput.value > today) {
            alert("Future dates are not allowed for attendance.");
            dateInput.value = today;
            return;
        }

        const payload = {
            date: dateInput.value,
            type: typeSelect.value,
            rows: changedData
        };

        fetch(window.location.origin + "/uoc_football/public/CaptainAttendance/update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(data => {
                alert("Attendance saved successfully!");
                location.reload();
            })
            .catch(err => console.error(err));
    });
    /* ================= RESET CHANGES ================= */

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
        changedData = []; 

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
    
   

    function exportPdfReport() {
        if (changedData.length > 0) {
            alert("You have unsaved changes. Please save before downloading.");
            return;
        }

        if (!window.jspdf || !window.jspdf.jsPDF) {
            alert("PDF library is not loaded. Please refresh and try again.");
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: "portrait", unit: "pt", format: "a4" });

        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const left = 40;
        const right = pageWidth - 40;
        const reportDate = dateInput && dateInput.value ? dateInput.value : new Date().toISOString().split("T")[0];
        const reportType = typeSelect && typeSelect.value ? typeSelect.value : "Practice";

        let y = 48;
        doc.setFont("helvetica", "bold");
        doc.setFontSize(16);
        doc.text("UOC Football - Attendance Report", left, y);

        y += 20;
        doc.setFont("helvetica", "normal");
        doc.setFontSize(11);
        doc.text(`Date: ${reportDate}`, left, y);
        doc.text(`Session Type: ${reportType}`, left + 170, y);
        doc.text(`Generated: ${new Date().toLocaleString()}`, left + 330, y);

        y += 22;
        doc.setDrawColor(220, 220, 220);
        doc.line(left, y, right, y);
        y += 18;

        doc.setFont("helvetica", "bold");
        doc.text("Player", left, y);
        doc.text("Position", left + 270, y);
        doc.text("Status", left + 420, y);

        y += 10;
        doc.line(left, y, right, y);
        y += 16;

        doc.setFont("helvetica", "normal");
        const rows = document.querySelectorAll(".attendance-table tbody tr[data-player-id]");
        rows.forEach((row) => {
            if (y > pageHeight - 42) {
                doc.addPage();
                y = 48;
                doc.setFont("helvetica", "bold");
                doc.text("Player", left, y);
                doc.text("Position", left + 270, y);
                doc.text("Status", left + 420, y);
                y += 10;
                doc.line(left, y, right, y);
                y += 16;
                doc.setFont("helvetica", "normal");
            }

            const player = (row.cells[0] && row.cells[0].innerText || "Player").replace(/\s+/g, " ").trim().slice(0, 42);
            const position = (row.cells[1] && row.cells[1].innerText || "-").trim().slice(0, 22);
            const status = (row.querySelector(".status")?.innerText || "Absent").trim().slice(0, 12);

            doc.text(player, left, y);
            doc.text(position, left + 270, y);
            doc.text(status, left + 420, y);
            y += 16;
        });

        const safeType = String(reportType).toLowerCase().replace(/[^a-z0-9]+/g, "-");
        doc.save(`captain-attendance-${reportDate}-${safeType}.pdf`);
    }

    /* ================= EXPORT BUTTON ================= */
    exportBtn.addEventListener("click", exportPdfReport);
    

const typeSelect = document.getElementById("eventType");
const dateInput = document.getElementById("attendanceDate");
const applyFiltersBtn = document.getElementById("applyFilters");
if (dateInput) {
    dateInput.max = new Date().toISOString().split("T")[0];
}

function reloadPage() {
    const date = dateInput.value;
    const type = typeSelect.value;
    const today = new Date().toISOString().split("T")[0];

    if (!date) {
        alert("Please select a date first.");
        return;
    }

    if (date > today) {
        alert("Future dates are not allowed for attendance.");
        dateInput.value = today;
        return;
    }

    const params = new URLSearchParams({ date, type });
    window.location.href = `${window.location.origin}/uoc_football/public/CaptainAttendance?${params.toString()}`;
}

typeSelect.addEventListener("change", reloadPage);
dateInput.addEventListener("change", reloadPage);
if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener("click", reloadPage);
}
});
