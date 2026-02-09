// Player Performance Dashboard JavaScript

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeFilters();
    initializePlayerComparison();
    initializeNotes();
});

// Initialize all charts
function initializeCharts() {
    createPerformanceTrendChart();
    createComparisonChart();
    createOutcomeChart();
}

// Performance Trend Line Chart
function createPerformanceTrendChart() {
    const ctx = document.getElementById('performanceChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
            datasets: [
                {
                    label: 'Player A',
                    data: [65, 72, 68, 80, 85],
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.1)',
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#7c3aed',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Player B',
                    data: [70, 68, 75, 73, 78],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Match Stats Comparison Bar Chart
function createComparisonChart() {
    const ctx = document.getElementById('comparisonChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Match 1', 'Match 2', 'Match 3', 'Match 4', 'Match 5'],
            datasets: [
                {
                    label: 'Current Match',
                    data: [12, 19, 15, 18, 22],
                    backgroundColor: '#7c3aed',
                    borderRadius: 8,
                    borderSkipped: false
                },
                {
                    label: 'Previous Match',
                    data: [10, 15, 13, 16, 20],
                    backgroundColor: '#a855f7',
                    borderRadius: 8,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'rect'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Match Outcomes Pie Chart
function createOutcomeChart() {
    const ctx = document.getElementById('outcomeChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Wins', 'Draws', 'Losses'],
            datasets: [{
                data: [60, 25, 15],
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ],
                borderWidth: 0,
                spacing: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
}

// Filter functionality
function initializeFilters() {
    const analysisType = document.getElementById('analysisType');
    const matchSelect = document.getElementById('matchSelect');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    if (analysisType) {
        analysisType.addEventListener('change', function() {
            console.log('Analysis type changed to:', this.value);
            // Add your filter logic here
            updateDashboard();
        });
    }

    if (matchSelect) {
        matchSelect.addEventListener('change', function() {
            console.log('Match changed to:', this.value);
            // Add your filter logic here
            updateDashboard();
        });
    }

    if (startDate) {
        startDate.addEventListener('change', function() {
            console.log('Start date changed to:', this.value);
            // Add your filter logic here
            updateDashboard();
        });
    }

    if (endDate) {
        endDate.addEventListener('change', function() {
            console.log('End date changed to:', this.value);
            // Add your filter logic here
            updateDashboard();
        });
    }
}

// Update dashboard based on filters
function updateDashboard() {
    // This function would typically make an AJAX call to fetch new data
    // For now, we'll just log that an update is needed
    console.log('Dashboard update triggered');
    
    // You can add animations here
    animateStats();
}

// Animate stat cards on update
function animateStats() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Player comparison functionality
function initializePlayerComparison() {
    const player1Select = document.getElementById('player1');
    const player2Select = document.getElementById('player2');

    if (player1Select) {
        player1Select.addEventListener('change', function() {
            console.log('Player 1 selected:', this.value);
            updateComparison();
        });
    }

    if (player2Select) {
        player2Select.addEventListener('change', function() {
            console.log('Player 2 selected:', this.value);
            updateComparison();
        });
    }
}

// Update comparison metrics
function updateComparison() {
    // This would typically fetch comparison data via AJAX
    console.log('Updating player comparison');
    
    // Animate the comparison cards
    const metricItems = document.querySelectorAll('.metric-item');
    metricItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.4s ease';
            item.style.opacity = '1';
            item.style.transform = 'scale(1)';
        }, index * 80);
    });
}

// Notes functionality
function initializeNotes() {
    const saveBtn = document.querySelector('.save-btn');
    const notesTextarea = document.querySelector('.notes-textarea');

    if (saveBtn && notesTextarea) {
        saveBtn.addEventListener('click', function() {
            const notes = notesTextarea.value;
            if (notes.trim() === '') {
                alert('Please enter some notes before saving.');
                return;
            }
            
            // Here you would typically save to database via AJAX
            console.log('Saving notes:', notes);
            
            // Show success feedback
            const originalText = this.textContent;
            this.textContent = 'Saved!';
            this.style.background = 'linear-gradient(135deg, #10b981 0%, #34d399 100%)';
            
            setTimeout(() => {
                this.textContent = originalText;
                this.style.background = 'linear-gradient(135deg, #7c3aed 0%, #a855f7 100%)';
            }, 2000);
        });
    }
}

// Add smooth scroll to sections
function smoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Initialize smooth scroll
smoothScroll();

// Console log for debugging
console.log('Player Performance Dashboard initialized successfully');