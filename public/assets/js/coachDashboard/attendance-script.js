// Attendance Page JavaScript

const ATTENDANCE_TREND_LABELS = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'];
const ATTENDANCE_DISTRIBUTION_LABELS = ['Present', 'Absent', 'Late', 'Excused'];
const DEFAULT_SEASON_KEY = '2025-season';

let attendanceTrendChart = null;
let attendanceDistributionChart = null;

const ATTENDANCE_STATS = {
    '2025-season': {
        season: {
            trend: [90, 91, 89, 92, 93, 94, 92, 95],
            distribution: [79, 8, 7, 6]
        },
        players: {
            john: {
                name: 'John Smith',
                color: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                trend: [94, 96, 93, 95, 97, 98, 96, 97],
                distribution: [86, 5, 5, 4]
            },
            mike: {
                name: 'Mike Johnson',
                color: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.12)',
                trend: [88, 89, 86, 90, 91, 92, 90, 91],
                distribution: [77, 10, 8, 5]
            },
            alex: {
                name: 'Alex Brown',
                color: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.12)',
                trend: [78, 80, 77, 81, 82, 84, 81, 83],
                distribution: [68, 14, 10, 8]
            },
            david: {
                name: 'David Wilson',
                color: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.12)',
                trend: [85, 87, 84, 88, 89, 90, 88, 90],
                distribution: [74, 11, 9, 6]
            }
        }
    },
    '2024-season': {
        season: {
            trend: [87, 88, 86, 89, 90, 91, 89, 90],
            distribution: [75, 10, 8, 7]
        },
        players: {
            john: {
                name: 'John Smith',
                color: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                trend: [91, 93, 90, 92, 94, 95, 93, 94],
                distribution: [83, 6, 6, 5]
            },
            mike: {
                name: 'Mike Johnson',
                color: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.12)',
                trend: [85, 86, 84, 87, 88, 89, 87, 88],
                distribution: [73, 11, 9, 7]
            },
            alex: {
                name: 'Alex Brown',
                color: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.12)',
                trend: [75, 76, 74, 78, 79, 80, 78, 79],
                distribution: [65, 16, 11, 8]
            },
            david: {
                name: 'David Wilson',
                color: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.12)',
                trend: [82, 84, 81, 85, 86, 87, 85, 86],
                distribution: [71, 12, 10, 7]
            }
        }
    },
    '2023-season': {
        season: {
            trend: [84, 85, 83, 86, 87, 88, 86, 87],
            distribution: [72, 12, 9, 7]
        },
        players: {
            john: {
                name: 'John Smith',
                color: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.12)',
                trend: [88, 90, 87, 89, 91, 92, 90, 91],
                distribution: [80, 7, 7, 6]
            },
            mike: {
                name: 'Mike Johnson',
                color: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.12)',
                trend: [81, 82, 80, 83, 84, 85, 83, 84],
                distribution: [69, 13, 10, 8]
            },
            alex: {
                name: 'Alex Brown',
                color: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.12)',
                trend: [72, 74, 71, 75, 76, 77, 75, 76],
                distribution: [62, 18, 12, 8]
            },
            david: {
                name: 'David Wilson',
                color: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.12)',
                trend: [79, 81, 78, 82, 83, 84, 82, 83],
                distribution: [67, 14, 11, 8]
            }
        }
    }
};

const DEFAULT_FILTERS = {
    player: 'all',
    season: DEFAULT_SEASON_KEY,
    date: ''
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeCharts();
    initializeTableInteractions();
    filterAttendanceRecords();
});

// Filter functionality
function initializeFilters() {
    const playerFilter = document.getElementById('playerFilter');
    const seasonFilter = document.getElementById('seasonFilter');
    const dateFilter = document.getElementById('dateFilter');

    // Add event listeners to filters
    [playerFilter, seasonFilter, dateFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', function() {
                if (filter.id === 'seasonFilter') {
                    updateSeasonStatValue();
                }
                filterAttendanceRecords();
            });
        }
    });

    updateSeasonStatValue();
}

function getActiveAttendanceFilters() {
    const playerFilter = document.getElementById('playerFilter');
    const seasonFilter = document.getElementById('seasonFilter');
    const dateFilter = document.getElementById('dateFilter');

    return {
        player: playerFilter ? playerFilter.value : DEFAULT_FILTERS.player,
        season: seasonFilter ? seasonFilter.value : DEFAULT_FILTERS.season,
        date: dateFilter ? dateFilter.value : DEFAULT_FILTERS.date
    };
}

function updateSeasonStatValue() {
    const seasonFilter = document.getElementById('seasonFilter');
    const seasonStatValue = document.getElementById('seasonStatValue');

    if (!seasonFilter || !seasonStatValue) {
        return;
    }

    seasonStatValue.textContent = seasonFilter.options[seasonFilter.selectedIndex].text;
}

function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
}

function roundToSingleDecimal(value) {
    return Math.round(value * 10) / 10;
}

function normalizeDistribution(distribution) {
    const safeDistribution = distribution.map(function(value) {
        return Math.max(0, value);
    });

    const total = safeDistribution.reduce(function(sum, value) {
        return sum + value;
    }, 0) || 1;

    const normalized = safeDistribution.map(function(value) {
        return roundToSingleDecimal((value / total) * 100);
    });

    const normalizedTotal = normalized.reduce(function(sum, value) {
        return sum + value;
    }, 0);

    const diff = roundToSingleDecimal(100 - normalizedTotal);
    normalized[0] = roundToSingleDecimal(clamp(normalized[0] + diff, 0, 100));

    return normalized;
}

function calculateDateImpact(dateFilter) {
    if (!dateFilter) {
        return 0;
    }

    const parsedDate = new Date(dateFilter + 'T00:00:00');
    if (Number.isNaN(parsedDate.getTime())) {
        return 0;
    }

    const dayOfMonth = parsedDate.getDate();
    return ((dayOfMonth % 7) - 3) * 0.35;
}

function applyDateAdjustmentsToTrend(trendSeries, dateFilter) {
    const dateImpact = calculateDateImpact(dateFilter);

    return trendSeries.map(function(point, index) {
        const weeklyVariance = (index % 2 === 0 ? 0.2 : -0.2);
        return roundToSingleDecimal(clamp(point + dateImpact + weeklyVariance, 0, 100));
    });
}

function applyDateAdjustmentsToDistribution(distributionSeries, dateFilter) {
    if (!dateFilter) {
        return normalizeDistribution(distributionSeries.slice());
    }

    const parsedDate = new Date(dateFilter + 'T00:00:00');
    if (Number.isNaN(parsedDate.getTime())) {
        return normalizeDistribution(distributionSeries.slice());
    }

    const dayOfWeek = parsedDate.getDay();
    const presentShift = (dayOfWeek - 3) * 0.4;
    const absentShift = -presentShift * 0.6;
    const lateShift = presentShift < 0 ? Math.abs(presentShift) * 0.45 : -Math.abs(presentShift) * 0.25;
    const excusedShift = -(presentShift + absentShift + lateShift);

    const adjusted = [
        distributionSeries[0] + presentShift,
        distributionSeries[1] + absentShift,
        distributionSeries[2] + lateShift,
        distributionSeries[3] + excusedShift
    ];

    return normalizeDistribution(adjusted);
}

function applyDateAdjustments(baseData, dateFilter) {
    return {
        trend: applyDateAdjustmentsToTrend(baseData.trend, dateFilter),
        distribution: applyDateAdjustmentsToDistribution(baseData.distribution, dateFilter)
    };
}

function createTrendDataset(label, data, color, backgroundColor, options) {
    return {
        label: label,
        data: data,
        borderColor: color,
        backgroundColor: backgroundColor,
        borderWidth: options.borderWidth,
        tension: 0.4,
        fill: options.fill,
        pointRadius: options.pointRadius,
        pointBackgroundColor: color,
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointHoverRadius: options.pointHoverRadius
    };
}

function resolveAttendanceChartData(filters) {
    const seasonData = ATTENDANCE_STATS[filters.season] || ATTENDANCE_STATS[DEFAULT_SEASON_KEY];

    const seasonBaseData = {
        trend: seasonData.season.trend.slice(),
        distribution: seasonData.season.distribution.slice()
    };

    const adjustedSeasonData = applyDateAdjustments(seasonBaseData, filters.date);

    if (filters.player !== 'all') {
        const selectedPlayerData = seasonData.players[filters.player];

        if (selectedPlayerData) {
            const playerBaseData = {
                trend: selectedPlayerData.trend.slice(),
                distribution: selectedPlayerData.distribution.slice()
            };

            const adjustedPlayerData = applyDateAdjustments(playerBaseData, filters.date);

            return {
                mode: 'player',
                trend: {
                    labels: ATTENDANCE_TREND_LABELS,
                    datasets: [
                        createTrendDataset(
                            'Season Average',
                            adjustedSeasonData.trend,
                            '#7c3aed',
                            'rgba(124, 58, 237, 0.08)',
                            { fill: false, borderWidth: 2, pointRadius: 3, pointHoverRadius: 5 }
                        ),
                        createTrendDataset(
                            selectedPlayerData.name,
                            adjustedPlayerData.trend,
                            selectedPlayerData.color,
                            selectedPlayerData.backgroundColor,
                            { fill: true, borderWidth: 3, pointRadius: 4, pointHoverRadius: 6 }
                        )
                    ]
                },
                distribution: {
                    labels: ATTENDANCE_DISTRIBUTION_LABELS,
                    data: adjustedPlayerData.distribution
                }
            };
        }
    }

    return {
        mode: 'season',
        trend: {
            labels: ATTENDANCE_TREND_LABELS,
            datasets: [
                createTrendDataset(
                    'Season Average',
                    adjustedSeasonData.trend,
                    '#7c3aed',
                    'rgba(124, 58, 237, 0.1)',
                    { fill: true, borderWidth: 3, pointRadius: 5, pointHoverRadius: 7 }
                )
            ]
        },
        distribution: {
            labels: ATTENDANCE_DISTRIBUTION_LABELS,
            data: adjustedSeasonData.distribution
        }
    };
}

// Filter attendance records based on selected filters
function filterAttendanceRecords() {
    const activeFilters = getActiveAttendanceFilters();
    const chartData = resolveAttendanceChartData(activeFilters);

    updateAttendanceTrendChart(chartData.trend);
    updateAttendanceDistributionChart(chartData.distribution);

    console.log('Filtering with:', {
        player: activeFilters.player,
        season: activeFilters.season,
        date: activeFilters.date,
        mode: chartData.mode
    });
}

// Initialize charts
function initializeCharts() {
    const activeFilters = getActiveAttendanceFilters();
    const chartData = resolveAttendanceChartData(activeFilters);

    createAttendanceTrendChart(chartData.trend);
    createAttendanceDistributionChart(chartData.distribution);
}

// Attendance Trend Line Chart
function createAttendanceTrendChart(trendChartData) {
    const ctx = document.getElementById('attendanceTrendChart');
    if (!ctx) return;

    if (attendanceTrendChart) {
        attendanceTrendChart.destroy();
    }

    attendanceTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendChartData.labels,
            datasets: trendChartData.datasets
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
function createAttendanceDistributionChart(distributionChartData) {
    const ctx = document.getElementById('attendanceDistributionChart');
    if (!ctx) return;

    if (attendanceDistributionChart) {
        attendanceDistributionChart.destroy();
    }

    attendanceDistributionChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: distributionChartData.labels,
            datasets: [{
                data: distributionChartData.data,
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

function updateAttendanceTrendChart(trendChartData) {
    if (!attendanceTrendChart) {
        createAttendanceTrendChart(trendChartData);
        return;
    }

    attendanceTrendChart.data.labels = trendChartData.labels;
    attendanceTrendChart.data.datasets = trendChartData.datasets;
    attendanceTrendChart.update();
}

function updateAttendanceDistributionChart(distributionChartData) {
    if (!attendanceDistributionChart) {
        createAttendanceDistributionChart(distributionChartData);
        return;
    }

    attendanceDistributionChart.data.labels = distributionChartData.labels;
    attendanceDistributionChart.data.datasets[0].data = distributionChartData.data;
    attendanceDistributionChart.update();
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