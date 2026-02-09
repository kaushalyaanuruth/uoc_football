// Attendance Page JavaScript

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeCharts();
    initializeTableInteractions();
});

// Filter functionality
function initializeFilters() {
    const playerFilter = document.getElementById('playerFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');
    const sessionFilter = document.getElementById('sessionFilter');

    // Add event listeners to filters
    [playerFilter, typeFilter, dateFilter, sessionFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', function() {
                filterAttendanceRecords();
            });
        }
    });
}

// Filter attendance records based on selected filters
function filterAttendanceRecords() {
    const playerFilter = document.getElementById('playerFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const sessionFilter = document.getElementById('sessionFilter').value;

    console.log('Filtering with:', {
        player: playerFilter,
        type: typeFilter,
        date: dateFilter,
        session: sessionFilter
    });

    // Here you would typically make an AJAX call to fetch filtered data
    // For now, we'll just log the filter values
}

// Initialize charts
function initializeCharts() {
    createAttendanceTrendChart();
    createAttendanceDistributionChart();
}

// Attendance Trend Line Chart
function createAttendanceTrendChart() {
    const ctx = document.getElementById('attendanceTrendChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'],
            datasets: [
                {
                    label: 'Team Average',
                    data: [85, 88, 82, 90, 87, 92, 89, 91],
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#7c3aed',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                },
                {
                    label: 'John Smith',
                    data: [90, 95, 88, 93, 91, 96, 94, 95],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                },
                {
                    label: 'Alex Brown',
                    data: [75, 78, 70, 82, 76, 80, 78, 79],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false,
                    pointRadius: 4,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 13,
                            weight: 600,
                            family: 'Poppins'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 600,
                        family: 'Poppins'
                    },
                    bodyFont: {
                        size: 13,
                        family: 'Poppins'
                    },
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        },
                        font: {
                            size: 12,
                            family: 'Poppins'
                        },
                        color: '#6b7280'
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            family: 'Poppins'
                        },
                        color: '#6b7280'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

// Attendance Distribution Doughnut Chart
function createAttendanceDistributionChart() {
    const ctx = document.getElementById('attendanceDistributionChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Late', 'Excused'],
            datasets: [{
                data: [75, 10, 8, 7],
                backgroundColor: [
                    '#10b981',
                    '#ef4444',
                    '#f59e0b',
                    '#3b82f6'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 600,
                        family: 'Poppins'
                    },
                    bodyFont: {
                        size: 13,
                        family: 'Poppins'
                    },
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + percentage + '%';
                        }
                    }
                }
            }
        }
    });
}

// Table interactions
function initializeTableInteractions() {
    const tableRows = document.querySelectorAll('.attendance-table tbody tr');
    
    tableRows.forEach(row => {
        row.addEventListener('click', function() {
            // Remove active class from all rows
            tableRows.forEach(r => r.classList.remove('active-row'));
            
            // Add active class to clicked row
            this.classList.add('active-row');
            
            // Get player info
            const playerName = this.querySelector('.player-info span').textContent;
            console.log('Selected player:', playerName);
        });
    });
}

// Mark attendance button functionality
const markBtn = document.querySelector('.mark-btn');
if (markBtn) {
    markBtn.addEventListener('click', function() {
        alert('Mark Attendance functionality - This would open a modal or form to mark attendance');
    });
}

// Add note button functionality
const addNoteBtn = document.querySelector('.add-note-btn');
if (addNoteBtn) {
    addNoteBtn.addEventListener('click', function() {
        alert('Add Note functionality - This would open a modal or form to add a new note');
    });
}

// Load more notes button
const loadMoreBtn = document.querySelector('.load-more-btn');
if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
        console.log('Loading more notes...');
        // Here you would typically load more notes via AJAX
        this.textContent = 'Loading...';
        
        setTimeout(() => {
            this.textContent = 'Load more...';
            alert('More notes loaded');
        }, 1000);
    });
}

// Console log for debugging
console.log('Attendance page initialized successfully');