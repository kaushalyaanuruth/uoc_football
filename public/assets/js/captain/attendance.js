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
    
   

    /* ================= EXPORT BUTTON ================= */
    exportBtn.addEventListener("click", () => {
        if (changedData.length > 0) {
            alert("You have unsaved changes. Please save before downloading.");
            return; 
        }

        const choice = confirm(
            "Click OK to download PDF\n"
            
        );

        if (choice) {
            
            window.location.href =
    window.location.origin +
    "/uoc_football/public/CaptainAttendance/export?date=" +
    dateInput.value +
    "&type=" +
    typeSelect.value;

        }
       
    });
    

const typeSelect = document.getElementById("eventType");
const dateInput = document.getElementById("attendanceDate");
const applyFiltersBtn = document.getElementById("applyFilters");

function reloadPage() {
    const date = dateInput.value;
    const type = typeSelect.value;

    if (!date) {
        alert("Please select a date first.");
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
