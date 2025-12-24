document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('tbody tr');
    const totalCount = document.querySelector('.total-count');
    const presentCount = document.querySelector('.present-count');
    const absentCount = document.querySelector('.absent-count');
    const markAllBtn = document.querySelector('.mark-all-present');
    const saveBtn = document.querySelector('.save-attendance');
    const resetBtn = document.querySelector('.reset-changes');
    const exportBtn = document.querySelector('.export-report');

    let present = 0;
    const total = rows.length;

    totalCount.textContent = total;
    presentCount.textContent = present;
    absentCount.textContent = total - present;

    // Action buttons
    document.querySelectorAll('.action-button').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('tr');
            const badge = row.querySelector('.badge');
            badge.className = 'badge';

            if (btn.classList.contains('action-button-check')) {
                badge.classList.add('badge-present');
                badge.textContent = 'Present';
                present++;
            } else if (btn.classList.contains('action-button-x')) {
                badge.classList.add('badge-absent');
                badge.textContent = 'Absent';
            } else {
                badge.classList.add('badge-late');
                badge.textContent = 'Late';
            }
            presentCount.textContent = present;
            absentCount.textContent = total - present;
        });
    });

    // Mark All Present
    markAllBtn.addEventListener('click', () => {
        rows.forEach(row => {
            const badge = row.querySelector('.badge');
            badge.className = 'badge badge-present';
            badge.textContent = 'Present';
        });
        present = total;
        presentCount.textContent = present;
        absentCount.textContent = 0;
    });

    // Save
    saveBtn.addEventListener('click', () => {
        alert('Attendance saved successfully!');
    });

    // Reset
    resetBtn.addEventListener('click', () => {
        location.reload();
    });

    // Export
    exportBtn.addEventListener('click', () => {
        let csv = 'Player,Position,Status\n';
        rows.forEach(row => {
            const name = row.querySelector('.text-wrapper-20').textContent;
            const pos = row.querySelector('td:nth-child(2)').textContent;
            const status = row.querySelector('.badge').textContent;
            csv += `${name},${pos},${status}\n`;
        });
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'attendance.csv';
        a.click();
    });
});