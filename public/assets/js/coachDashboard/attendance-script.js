const CONFIG = window.COACH_ATTENDANCE_CONFIG || {};

let attendanceTrendChart = null;
let attendanceDistributionChart = null;
let changedData = [];
const originalStatuses = new Map();

function normalizeStatusClass(status) {
    return String(status || 'Absent').toLowerCase();
}

function renderStatusBadge(row, status) {
    const badge = row.querySelector('.status-badge');
    if (!badge) {
        return;
    }

    badge.textContent = status;
    badge.className = 'status-badge ' + normalizeStatusClass(status);
}

function getVisibleRows() {
    return Array.from(document.querySelectorAll('.attendance-table tbody tr')).filter(row => {
        return row.style.display !== 'none' && row.dataset.playerId;
    });
}

function updateStatsFromTable() {
    const rows = getVisibleRows();
    let total = 0;
    let present = 0;
    let absent = 0;
    let late = 0;

    rows.forEach(row => {
        const badge = row.querySelector('.status-badge');
        const status = badge ? badge.textContent.trim() : 'Absent';
        total += 1;

        if (status === 'Present') {
            present += 1;
        } else if (status === 'Late') {
            late += 1;
        } else {
            absent += 1;
        }
    });

    const totalNode = document.getElementById('totalPlayersValue');
    const presentNode = document.getElementById('presentPlayersValue');
    const absentNode = document.getElementById('absentPlayersValue');
    const overallNode = document.getElementById('overallAttendanceValue');

    if (totalNode) totalNode.textContent = String(total);
    if (presentNode) presentNode.textContent = String(present);
    if (absentNode) absentNode.textContent = String(absent);
    if (overallNode) {
        const percent = total > 0 ? (((present + late) / total) * 100).toFixed(1) : '0.0';
        overallNode.textContent = percent;
    }
}

function setStatus(row, status) {
    const playerId = parseInt(row.dataset.playerId || '0', 10);
    if (!playerId) {
        return;
    }

    renderStatusBadge(row, status);

    const existingIndex = changedData.findIndex(item => item.player_id === playerId);
    if (existingIndex >= 0) {
        changedData[existingIndex].status = status;
    } else {
        changedData.push({ player_id: playerId, status: status });
    }

    const original = originalStatuses.get(playerId);
    if (original === status) {
        changedData = changedData.filter(item => item.player_id !== playerId);
    }

    updateStatsFromTable();
}

function initializeRowStatusActions() {
    const rows = document.querySelectorAll('.attendance-table tbody tr[data-player-id]');
    rows.forEach(row => {
        const playerId = parseInt(row.dataset.playerId || '0', 10);
        const original = row.dataset.originalStatus || 'Absent';
        if (playerId) {
            originalStatuses.set(playerId, original);
        }

        row.querySelectorAll('.status-action-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const status = btn.dataset.status || 'Absent';
                setStatus(row, status);
            });
        });
    });
}

function applyPlayerFilter() {
    const playerFilter = document.getElementById('playerFilter');
    const selected = playerFilter ? playerFilter.value : 'all';

    document.querySelectorAll('.attendance-table tbody tr[data-player-id]').forEach(row => {
        if (selected === 'all') {
            row.style.display = '';
            return;
        }

        row.style.display = row.dataset.playerId === selected ? '' : 'none';
    });

    updateStatsFromTable();
    updatePlayerHistoryPanel();
}

function formatHistoryDate(value) {
    if (!value) {
        return 'N/A';
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return parsed.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}

function renderPlayerHistoryRows(rows) {
    const historyBody = document.getElementById('playerHistoryBody');
    if (!historyBody) {
        return;
    }

    if (!Array.isArray(rows) || rows.length === 0) {
        historyBody.innerHTML = '<tr><td colspan="4" style="text-align:center; color:#6b7280;">No attendance history found for this player.</td></tr>';
        return;
    }

    const html = rows.map((row) => {
        const status = String(row.status || 'Absent');
        return '<tr>' +
            '<td>' + formatHistoryDate(row.date || '') + '</td>' +
            '<td>' + String(row.session_type || 'Practice') + '</td>' +
            '<td>' + String(row.location || 'Ground') + '</td>' +
            '<td><span class="status-badge ' + normalizeStatusClass(status) + '">' + status + '</span></td>' +
            '</tr>';
    }).join('');

    historyBody.innerHTML = html;
}

async function updatePlayerHistoryPanel() {
    const playerFilter = document.getElementById('playerFilter');
    const historyCard = document.getElementById('playerHistoryCard');
    const historyTitle = document.getElementById('playerHistoryTitle');
    const historyStatus = document.getElementById('playerHistoryStatus');
    const selected = playerFilter ? playerFilter.value : 'all';

    if (!historyCard || !historyTitle || !historyStatus) {
        return;
    }

    if (!selected || selected === 'all') {
        historyCard.style.display = 'none';
        renderPlayerHistoryRows([]);
        return;
    }

    const selectedOption = playerFilter.options[playerFilter.selectedIndex];
    const selectedName = selectedOption ? selectedOption.textContent.trim() : 'Player';

    historyCard.style.display = '';
    historyTitle.textContent = selectedName + ' - Full Attendance History';
    historyStatus.textContent = 'Loading history...';

    try {
        const url = String(CONFIG.historyUrl || (String(CONFIG.baseUrl || '/coachAttendance') + '/playerHistory')) + '?player_id=' + encodeURIComponent(selected);
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        if (!response.ok || !result || !result.success) {
            throw new Error((result && result.message) ? result.message : 'Failed to load attendance history');
        }

        const history = Array.isArray(result.history) ? result.history : [];
        historyStatus.textContent = history.length > 0
            ? ('Total sessions found: ' + history.length)
            : 'No attendance sessions found.';
        renderPlayerHistoryRows(history);
    } catch (error) {
        historyStatus.textContent = error.message || 'Failed to load attendance history.';
        renderPlayerHistoryRows([]);
    }
}

function buildUrlWithFilters() {
    const dateInput = document.getElementById('dateFilter');
    const typeSelect = document.getElementById('seasonFilter');
    const today = new Date().toISOString().split('T')[0];
    let selectedDate = (dateInput && dateInput.value) || CONFIG.selectedDate || '';
    if (selectedDate > today) {
        selectedDate = today;
        if (dateInput) {
            dateInput.value = today;
        }
    }

    const params = new URLSearchParams({
        date: selectedDate,
        type: (typeSelect && typeSelect.value) || CONFIG.selectedType || 'Practice'
    });

    return String(CONFIG.baseUrl || '/coachAttendance') + '?' + params.toString();
}

function bindFilterReload() {
    const applyBtn = document.getElementById('applyFiltersBtn');
    const dateInput = document.getElementById('dateFilter');
    const typeSelect = document.getElementById('seasonFilter');

    const reload = () => {
        if (changedData.length > 0) {
            const proceed = window.confirm('You have unsaved changes. Continue and discard them?');
            if (!proceed) {
                return;
            }
        }

        window.location.href = buildUrlWithFilters();
    };

    if (applyBtn) {
        applyBtn.addEventListener('click', reload);
    }
    if (dateInput) {
        dateInput.addEventListener('change', reload);
    }
    if (typeSelect) {
        typeSelect.addEventListener('change', reload);
    }

    const playerFilter = document.getElementById('playerFilter');
    if (playerFilter) {
        playerFilter.addEventListener('change', applyPlayerFilter);
    }
}

async function saveAttendance() {
    if (changedData.length === 0) {
        alert('No changes to save.');
        return;
    }

    const dateInput = document.getElementById('dateFilter');
    const typeSelect = document.getElementById('seasonFilter');

    const payload = {
        date: (dateInput && dateInput.value) || CONFIG.selectedDate,
        type: (typeSelect && typeSelect.value) || CONFIG.selectedType,
        rows: changedData
    };

    const today = new Date().toISOString().split('T')[0];
    if (payload.date > today) {
        alert('Future dates are not allowed for attendance.');
        if (dateInput) {
            dateInput.value = today;
        }
        return;
    }

    try {
        const response = await fetch(String(CONFIG.baseUrl || '/coachAttendance') + '/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const responseText = await response.text();
        let result = null;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            const preview = responseText.trim().slice(0, 120);
            throw new Error('Server returned non-JSON response while saving. ' + preview);
        }

        if (!response.ok || !result || !result.success) {
            throw new Error((result && result.message) ? result.message : 'Failed to save attendance');
        }

        alert('Attendance saved successfully.');
        window.location.href = buildUrlWithFilters();
    } catch (error) {
        alert(error.message || 'Failed to save attendance.');
    }
}

function resetChanges() {
    if (changedData.length === 0) {
        return;
    }

    const confirmed = window.confirm('Reset all unsaved attendance changes?');
    if (!confirmed) {
        return;
    }

    document.querySelectorAll('.attendance-table tbody tr[data-player-id]').forEach(row => {
        const playerId = parseInt(row.dataset.playerId || '0', 10);
        const original = originalStatuses.get(playerId) || row.dataset.originalStatus || 'Absent';
        renderStatusBadge(row, original);
    });

    changedData = [];
    updateStatsFromTable();
}

function exportReport() {
    if (changedData.length > 0) {
        alert('You have unsaved changes. Please save before exporting.');
        return;
    }

    if (!window.jspdf || !window.jspdf.jsPDF) {
        alert('PDF library is not loaded. Please refresh and try again.');
        return;
    }

    const dateInput = document.getElementById('dateFilter');
    const typeSelect = document.getElementById('seasonFilter');
    const dateValue = (dateInput && dateInput.value) || CONFIG.selectedDate || '';
    const typeValue = (typeSelect && typeSelect.value) || CONFIG.selectedType || 'Practice';

    const rows = Array.from(document.querySelectorAll('.attendance-table tbody tr[data-player-id]')).map((row) => {
        const name = row.querySelector('.player-info span')?.textContent?.trim() || 'Player';
        const position = row.cells[1]?.textContent?.trim() || '-';
        const status = row.querySelector('.status-badge')?.textContent?.trim() || 'Absent';
        return { name, position, status };
    });

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'portrait', unit: 'pt', format: 'a4' });

    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const left = 40;
    const right = pageWidth - 40;
    let y = 48;

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.text('UOC Football - Attendance Report', left, y);

    y += 20;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(11);
    doc.text(`Date: ${dateValue || 'N/A'}`, left, y);
    doc.text(`Session Type: ${typeValue}`, left + 180, y);
    doc.text(`Generated: ${new Date().toLocaleString()}`, left + 340, y);

    y += 22;
    doc.setDrawColor(220, 220, 220);
    doc.line(left, y, right, y);
    y += 18;

    doc.setFont('helvetica', 'bold');
    doc.text('Player', left, y);
    doc.text('Position', left + 270, y);
    doc.text('Status', left + 420, y);

    y += 10;
    doc.line(left, y, right, y);
    y += 16;

    doc.setFont('helvetica', 'normal');
    rows.forEach((row) => {
        if (y > pageHeight - 42) {
            doc.addPage();
            y = 48;
            doc.setFont('helvetica', 'bold');
            doc.text('Player', left, y);
            doc.text('Position', left + 270, y);
            doc.text('Status', left + 420, y);
            y += 10;
            doc.line(left, y, right, y);
            y += 16;
            doc.setFont('helvetica', 'normal');
        }

        const playerName = String(row.name || '').slice(0, 42);
        const position = String(row.position || '').slice(0, 22);
        const status = String(row.status || '').slice(0, 12);

        doc.text(playerName, left, y);
        doc.text(position, left + 270, y);
        doc.text(status, left + 420, y);
        y += 16;
    });

    const safeType = String(typeValue).toLowerCase().replace(/[^a-z0-9]+/g, '-');
    const filename = `coach-attendance-${dateValue || 'report'}-${safeType}.pdf`;
    doc.save(filename);
}

function createAttendanceTrendChart() {
    const canvas = document.getElementById('attendanceTrendChart');
    if (!canvas || typeof Chart === 'undefined') {
        return;
    }

    attendanceTrendChart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: Array.isArray(CONFIG.trendLabels) ? CONFIG.trendLabels : ['No Data'],
            datasets: [{
                label: 'Attendance Rate',
                data: Array.isArray(CONFIG.trendValues) ? CONFIG.trendValues : [0],
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124, 58, 237, 0.12)',
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#7c3aed'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: value => value + '%'
                    }
                }
            }
        }
    });
}

function createAttendanceDistributionChart() {
    const canvas = document.getElementById('attendanceDistributionChart');
    if (!canvas || typeof Chart === 'undefined') {
        return;
    }

    attendanceDistributionChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: Array.isArray(CONFIG.distributionLabels)
                ? CONFIG.distributionLabels
                : ['Present', 'Absent', 'Late', 'Excused'],
            datasets: [{
                data: Array.isArray(CONFIG.distributionValues) ? CONFIG.distributionValues : [0, 0, 0, 0],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: { display: false }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('dateFilter');
    if (dateInput) {
        dateInput.max = new Date().toISOString().split('T')[0];
    }

    initializeRowStatusActions();
    bindFilterReload();
    applyPlayerFilter();

    const markAllButton = document.getElementById('markAllPresentBtn');
    if (markAllButton) {
        markAllButton.addEventListener('click', () => {
            const visibleRows = getVisibleRows();
            visibleRows.forEach(row => setStatus(row, 'Present'));
        });
    }

    const saveButton = document.querySelector('.save-attendance');
    if (saveButton) {
        saveButton.addEventListener('click', saveAttendance);
    }

    const resetButton = document.querySelector('.reset-changes');
    if (resetButton) {
        resetButton.addEventListener('click', resetChanges);
    }

    const exportButton = document.querySelector('.exportreport');
    if (exportButton) {
        exportButton.addEventListener('click', exportReport);
    }

    createAttendanceTrendChart();
    createAttendanceDistributionChart();
});